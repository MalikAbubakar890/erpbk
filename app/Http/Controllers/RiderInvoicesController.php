<?php

namespace App\Http\Controllers;

use App\DataTables\RiderInvoicesDataTable;
use App\Helpers\HeadAccount;
use App\Http\Requests\CreateRiderInvoicesRequest;
use App\Http\Requests\UpdateRiderInvoicesRequest;
use App\Http\Controllers\AppBaseController;
use App\Imports\ImportRiderInvoice;
use App\Models\Accounts;
use App\Models\Items;
use App\Models\RiderInvoices;
use App\Models\Riders;
use App\Models\Transactions;
use App\Repositories\RiderInvoicesRepository;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use App\Traits\GlobalPagination;
use Flash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class RiderInvoicesController extends AppBaseController
{
    use GlobalPagination;
  /** @var RiderInvoicesRepository $riderInvoicesRepository*/
  private $riderInvoicesRepository;

  public function __construct(RiderInvoicesRepository $riderInvoicesRepo)
  {
    $this->riderInvoicesRepository = $riderInvoicesRepo;
  }

  /**
   * Display a listing of the RiderInvoices.
   */
  public function index(Request $request)
  {
    // Use global pagination trait
        $paginationParams = $this->getPaginationParams($request, $this->getDefaultPerPage());

    $query = RiderInvoices::query()
      ->orderBy('billing_month', 'desc');

    // Filters
    if ($request->has('id') && !empty($request->id)) {
      $query->where('id', 'like', '%' . $request->id . '%');
    }
    if ($request->has('rider_id') && !empty($request->rider_id)) {
      $query->where('rider_id', $request->rider_id);
    }
    if ($request->has('billing_month') && !empty($request->billing_month)) {
      $billingMonth = \Carbon\Carbon::parse($request->billing_month);
      $query->whereYear('billing_month', $billingMonth->year)
        ->whereMonth('billing_month', $billingMonth->month);
    }
    if ($request->has('vendor_id') && !empty($request->vendor_id)) {
      $query->where('vendor_id', $request->vendor_id);
    }
    if ($request->has('zone') && !empty($request->zone)) {
      $query->where('zone', $request->zone);
    }
    if ($request->has('performance') && !empty($request->performance)) {
      $query->where('performance', $request->performance);
    }
    if ($request->has('status') && !empty($request->status)) {
      $query->where('status', $request->status);
    }

    // Apply pagination using the trait
        $data = $this->applyPagination($query, $paginationParams);

    // ✅ Billing month ka check for total calculation
    $billingMonth = $request->has('billing_month') && !empty($request->billing_month)
      ? \Carbon\Carbon::parse($request->billing_month)
      : now();

    $currentMonthTotal = RiderInvoices::whereYear('billing_month', $billingMonth->year)
      ->whereMonth('billing_month', $billingMonth->month)
      ->sum('total_amount');

    // ✅ AJAX Response
    if ($request->ajax()) {
      $tableData = view('rider_invoices.table', [
        'data' => $data,
        'currentMonthTotal' => $currentMonthTotal
      ])->render();

      $paginationLinks = $data->links('components.global-pagination')->render();

      return response()->json([
        'tableData' => $tableData,
        'paginationLinks' => $paginationLinks,
        'currentMonthTotal' => number_format($currentMonthTotal, 1),
      ]);
    }

    // ✅ Normal Response
    return view('rider_invoices.index', [
      'data' => $data,
      'currentMonthTotal' => $currentMonthTotal,
    ]);
  }



  /**
   * Show the form for creating a new RiderInvoices.
   */
  public function create()
  {
    $riders = Riders::dropdown();
    $items = Items::dropdown();
    return view('rider_invoices.create', compact('riders', 'items'));
  }

  /**
   * Store a newly created RiderInvoices in storage.
   */
  public function store(CreateRiderInvoicesRequest $request)
  {
    $input = $request->all();

    $riderInvoices = $this->riderInvoicesRepository->record($request);



    Flash::success('Rider Invoices saved successfully.');

    return redirect(route('riderInvoices.index'));
  }

  /**
   * Display the specified RiderInvoices.
   */
  public function show($id)
  {
    $riderInvoice = $this->riderInvoicesRepository->find($id);

    if (empty($riderInvoice)) {
      Flash::error('Rider Invoices not found');

      return redirect(route('riderInvoices.index'));
    }

    return view('rider_invoices.show')->with('riderInvoice', $riderInvoice);
  }

  /**
   * Show the form for editing the specified RiderInvoices.
   */
  public function edit($id)
  {
    $invoice = $this->riderInvoicesRepository->find($id);

    if (empty($invoice)) {
      Flash::error('Rider Invoices not found');

      return redirect(route('riderInvoices.index'));
    }
    $riders = Riders::dropdown();
    $items = Items::dropdown();

    return view('rider_invoices.edit', compact('riders', 'items', 'invoice'));
  }

  /**
   * Update the specified RiderInvoices in storage.
   */
  public function update($id, UpdateRiderInvoicesRequest $request)
  {
    $riderInvoices = $this->riderInvoicesRepository->find($id);

    if (empty($riderInvoices)) {
      Flash::error('Rider Invoices not found');

      return redirect(route('riderInvoices.index'));
    }

    $riderInvoices = $this->riderInvoicesRepository->record($request, $id);

    Flash::success('Rider Invoices updated successfully.');

    return redirect(route('riderInvoices.index'));
  }

  /**
   * Remove the specified RiderInvoices from storage.
   *
   * @throws \Exception
   */
  public function destroy($id)
  {
    $riderInvoices = $this->riderInvoicesRepository->find($id);

    if (empty($riderInvoices)) {
      Flash::error('Rider Invoices not found');

      return redirect(route('riderInvoices.index'));
    }

    $trans_code = Transactions::where('reference_type', 'Invoice')->where('reference_id', $id)->value('trans_code');
    $transactions = new TransactionService();
    $transactions->deleteTransaction($trans_code);

    $this->riderInvoicesRepository->delete($id);

    Flash::success('Rider Invoices deleted successfully.');

    return redirect(route('riderInvoices.index'));
  }

  public function import(Request $request)
  {
    if ($request->isMethod('post')) {
      $rules = [
        'file' => 'required|max:50000|mimes:xlsx'
      ];
      $message = [
        'file.required' => 'Excel File Required'
      ];
      $this->validate($request, $rules, $message);
      Excel::import(new ImportRiderInvoice(), $request->file('file'));
    }

    return view('rider_invoices.import');
  }

  public function sendEmail($id, Request $request)
  {

    if ($request->isMethod('post')) {

      $data = [
        'html' => $request->email_message
      ];
      $res = RiderInvoices::with(['riderInv_item'])->where('id', $id)->get();
      $pdf = \PDF::loadView('invoices.rider_invoices.show', ['res' => $res]);

      Mail::send('emails.general', $data, function ($message) use ($request, $pdf) {
        $message->to([$request->email_to]);
        //$message->replyTo([$request->email]);
        $message->subject($request->email_subject);
        $message->attachData($pdf->output(), $request->email_subject . '.pdf');
        $message->priority(3);
      });
    }
    $invoice = RiderInvoices::find($id);
    return view('rider_invoices.send_email', compact('invoice'));
  }
}
