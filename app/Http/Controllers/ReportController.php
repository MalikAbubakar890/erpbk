<?php

namespace App\Http\Controllers;

use App\Helpers\Account;
use App\Helpers\CommonHelper;
use App\Helpers\General;
use App\Models\Rider;
use App\Models\Riders;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
  public function rider_invoice_index()
  {
    return view('reports.rider');
  }
  public function vendor_invoice_index()
  {
    return view('reports.vendor');
  }
  public function rider_list()
  {
    $riders = Riders::all()->sortBy('rider_id');
    return view('reports.rider_list', compact('riders'));
  }
  public function rider_report()
  {
    $riders = []; //Rider::all()->sortBy('rider_id');
    return view('reports.rider_report', compact('riders'));
  }
  public function rider_report_data(Request $request)
  {
    $data = '';
    $total = 0;
    $ob_total = 0;
    $opening_balance_total = 0;
    $b_total = 0;
    $total_debit_sum = 0;
    $total_credit_sum = 0;

    // Handle billing_month - make it optional
    $billing_month = null;
    if ($request->billing_month) {
      $billing_month = $request->billing_month;
      if (!str_contains($billing_month, '-01')) {
        $billing_month = $billing_month . "-01";
      }
    }

    $result = new Riders();
    if ($request->status && $request->status !== '') {
      $result = $result->where('status', $request->status);
    }
    if ($request->VID && $request->VID !== '') {
      $result = $result->where('VID', $request->VID);
    }
    if ($request->designation && $request->designation !== '') {
      $result = $result->where('designation', $request->designation);
    }
    $result = $result->get();



    // Process each rider
    foreach ($result as $rider) {
      // Initialize default values
      $opening_balance = 0.00;
      $balance = 0.00;
      $rider_total_debit = 0.00;
      $rider_total_credit = 0.00;

      if (isset($rider->account_id) && $rider->account_id) {
        if ($billing_month) {
          // If billing month is provided, get monthly data
          $opening_balance = Account::Monthly_ob($billing_month, $rider->account_id);
          $balance = Account::BillingMonth_Balance($billing_month, $rider->account_id);

          // Calculate total debits and credits for the specific month
          $rider_total_debit = \App\Models\Transactions::where('account_id', $rider->account_id)
            ->whereDate('billing_month', $billing_month)->sum('debit');
          $rider_total_credit = \App\Models\Transactions::where('account_id', $rider->account_id)
            ->whereDate('billing_month', $billing_month)->sum('credit');

          $subtotal_debit = \App\Models\Transactions::where('account_id', $rider->account_id)
            ->sum('debit');
          $subtotal_credit = \App\Models\Transactions::where('account_id', $rider->account_id)
            ->sum('credit');
          $subtotal_balance = $subtotal_debit - $subtotal_credit;
        } else {
          // If no billing month, get overall account balance
          $opening_balance = 0.00; // No opening balance for overall report

          // Calculate total debits and credits for entire history
          $rider_total_debit = \App\Models\Transactions::where('account_id', $rider->account_id)->sum('debit');

          $rider_total_credit = \App\Models\Transactions::where('account_id', $rider->account_id)->sum('credit');

          // Calculate balance as debit minus credit
          $balance = $rider_total_debit - $rider_total_credit;
          $subtotal_debit = \App\Models\Transactions::where('account_id', $rider->account_id)
            ->sum('debit');
          $subtotal_credit = \App\Models\Transactions::where('account_id', $rider->account_id)
            ->sum('credit');
          $subtotal_balance = $subtotal_debit - $subtotal_credit;
        }

        // No additional calculations - just show raw debit and credit values
      }
      $data .= '<tr>';
      $data .= '<td  >' . @$rider->rider_id . '</td>';
      $data .= '<td  >' . @$rider->name . '</td>';
      $data .= '<td >' . @$rider->vendor->name . '</td>';
      $data .= '<td >' . @$rider->designation . '</td>';
      $data .= '<td >' . @$rider->person_code . '</td>';
      $data .= '<td >' . @$rider->labor_card_number . '</td>';
      $data .= '<td  >' . @$rider->bikes->plate . '</td>';
      $data .= '<td  >' . $rider->wps . '</td>';
      $hasActiveBike = DB::table('bikes')
        ->where('rider_id', @$rider->id)
        ->where('warehouse', 'Active')
        ->exists();

      // Determine status based on bike assignment
      $isActive = $hasActiveBike;
      $badgeClass = $isActive ? 'bg-label-success' : 'bg-label-danger';
      $statusText = $isActive ? 'Active' : 'Inactive';
      $data .= '<td>
            <span class="badge ' . $badgeClass . '">' . $statusText . '</span>
          </td>';


      $data .= '<td align="right" >' . number_format($opening_balance, 2) . '</td>';
      $data .= '<td align="right" >' . number_format($balance, 2) . '</td>';
      $data .= '<td align="right">' . Account::show_bal($opening_balance + $balance) . '</td>';
      $data .= '<td align="right">' . Account::show_bal($subtotal_balance - ($opening_balance + $balance)) . '</td>';
      $data .= '<td align="right">' . number_format($subtotal_balance, 2) . '</td>';
      $data .= '</tr>';

      $opening_balance_total += $opening_balance;
      $ob_total += $opening_balance;
      $total += $balance;
      $b_total += $opening_balance + $balance;
      $total_debit_sum += $rider_total_debit;
      $total_credit_sum += $rider_total_credit;
    }






    $data .= '<tr>';
    $data .= '<td colspan="9"></td>';
    $data .= '<th style="text-align: right">' . number_format($opening_balance_total, 2) . '</th>';
    $data .= '<th style="text-align: right">' . number_format($total, 2) . '</th>';
    $data .= '<th style="text-align: right">' . Account::show_bal($b_total) . '</th>';
    $data .= '<th style="text-align: right">' . number_format($total_debit_sum, 2) . '</th>';
    $data .= '<th style="text-align: right">' . number_format($total_credit_sum, 2) . '</th>';
    $data .= '</tr>';

    return compact('data', 'opening_balance_total', 'total', 'b_total', 'total_debit_sum', 'total_credit_sum');
  }
}
