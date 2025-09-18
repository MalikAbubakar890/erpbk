<?php

namespace App\Http\Controllers;

use App\DataTables\RiderActivitiesDataTable;
use App\Http\Requests\CreateRiderActivitiesRequest;
use App\Http\Requests\UpdateRiderActivitiesRequest;
use App\Http\Controllers\AppBaseController;
use App\Imports\ImportRiderActivities;
use App\Repositories\RiderActivitiesRepository;
use App\Models\RiderActivities;
use Illuminate\Http\Request;
use Flash;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class RiderActivitiesController extends AppBaseController
{
  /** @var RiderActivitiesRepository $riderActivitiesRepository*/
  private $riderActivitiesRepository;

  public function __construct(RiderActivitiesRepository $riderActivitiesRepo)
  {
    $this->riderActivitiesRepository = $riderActivitiesRepo;
  }

  /**
   * Display a listing of the RiderActivities.
   */
  public function index(Request $request)
  {
    $perPage = (int) $request->input('per_page', 50);
    $perPage = $perPage > 0 ? $perPage : 50;

    $query = RiderActivities::query()
      ->orderBy('id', 'desc');

    // ID Filter
    if ($request->filled('id')) {
      $query->where('d_rider_id', 'like', '%' . $request->id . '%');
    }

    // Rider ID Filter
    if ($request->filled('rider_id')) {
      $query->where('rider_id', $request->rider_id);
    }

    // NEW Date Range Filter
    if ($request->filled('from_date')) {
      $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
      $query->where('date', '>=', $fromDate);
    }

    if ($request->filled('to_date')) {
      $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
      $query->where('date', '<=', $toDate);
    }

    // OLD Billing Month Filter
    if ($request->filled('billing_month_from')) {
      $fromMonth = \Carbon\Carbon::parse($request->billing_month_from)->startOfMonth();
      $query->where('date', '>=', $fromMonth);
    }

    if ($request->filled('billing_month_to')) {
      $toMonth = \Carbon\Carbon::parse($request->billing_month_to)->endOfMonth();
      $query->where('date', '<=', $toMonth);
    }

    // Fleet Supervisor Filter
    if ($request->filled('fleet_supervisor')) {
      $query->whereHas('rider', function ($q) use ($request) {
        $q->where('fleet_supervisor', $request->fleet_supervisor);
      });
    }

    // Payout Type
    if ($request->filled('payout_type')) {
      $query->where('payout_type', $request->payout_type);
    }

    $data = $query->paginate($perPage);

    // Dropdown data
    $riders = DB::table('riders')
      ->select('rider_id', 'name')
      ->whereIn('id', DB::table('rider_activities')->pluck('rider_id')->unique())
      ->get();

    $fleetSupervisors = DB::table('riders')
      ->whereNotNull('fleet_supervisor')
      ->distinct()
      ->pluck('fleet_supervisor');

    $payoutTypes = DB::table('rider_activities')
      ->whereNotNull('payout_type')
      ->distinct()
      ->pluck('payout_type');

    if ($request->ajax()) {
      $tableData = view('rider_activities.table', compact('data', 'riders', 'fleetSupervisors', 'payoutTypes'))->render();
      $paginationLinks = $data->links('pagination')->render();
      return response()->json([
        'tableData' => $tableData,
        'paginationLinks' => $paginationLinks,
      ]);
    }

    return view('rider_activities.index', compact('data', 'riders', 'fleetSupervisors', 'payoutTypes'));
  }




  /**
   * Show the form for creating a new RiderActivities.
   */
  public function create()
  {
    return view('rider_activities.create');
  }

  /**
   * Store a newly created RiderActivities in storage.
   */
  public function store(CreateRiderActivitiesRequest $request)
  {
    $input = $request->all();

    $riderActivities = $this->riderActivitiesRepository->create($input);

    Flash::success('Rider Activities saved successfully.');

    return redirect(route('riderActivities.index'));
  }

  /**
   * Display the specified RiderActivities.
   */
  public function show($id)
  {
    $riderActivities = $this->riderActivitiesRepository->find($id);

    if (empty($riderActivities)) {
      Flash::error('Rider Activities not found');

      return redirect(route('riderActivities.index'));
    }

    return view('rider_activities.show')->with('riderActivities', $riderActivities);
  }

  /**
   * Show the form for editing the specified RiderActivities.
   */
  public function edit($id)
  {
    $riderActivities = $this->riderActivitiesRepository->find($id);

    if (empty($riderActivities)) {
      Flash::error('Rider Activities not found');

      return redirect(route('riderActivities.index'));
    }

    return view('rider_activities.edit')->with('riderActivities', $riderActivities);
  }

  /**
   * Update the specified RiderActivities in storage.
   */
  public function update($id, UpdateRiderActivitiesRequest $request)
  {
    $riderActivities = $this->riderActivitiesRepository->find($id);

    if (empty($riderActivities)) {
      Flash::error('Rider Activities not found');

      return redirect(route('riderActivities.index'));
    }

    $riderActivities = $this->riderActivitiesRepository->update($request->all(), $id);

    Flash::success('Rider Activities updated successfully.');

    return redirect(route('riderActivities.index'));
  }

  /**
   * Remove the specified RiderActivities from storage.
   *
   * @throws \Exception
   */
  public function destroy($id)
  {
    $riderActivities = $this->riderActivitiesRepository->find($id);

    if (empty($riderActivities)) {
      Flash::error('Rider Activities not found');

      return redirect(route('riderActivities.index'));
    }

    $this->riderActivitiesRepository->delete($id);

    Flash::success('Rider Activities deleted successfully.');

    return redirect(route('riderActivities.index'));
  }

  public function import(Request $request)
  {
    if ($request->isMethod('post')) {
      $rules = [
        'file' => 'required|max:50000|mimes:xlsx,csv'
      ];
      $message = [
        'file.required' => 'Excel File Required'
      ];
      $this->validate($request, $rules, $message);
      Excel::import(new ImportRiderActivities(), $request->file('file'));
    }

    return view('rider_activities.import');
  }
}
