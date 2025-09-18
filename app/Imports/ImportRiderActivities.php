<?php

namespace App\Imports;

use App\Helpers\Account;
use App\Helpers\General;
use App\Helpers\HeadAccount;
use App\Models\Items;
use App\Models\RiderActivities;
use App\Models\RiderInvoiceItem;
use App\Models\RiderInvoices;
use App\Models\Riders;
use App\Services\TransactionService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class ImportRiderActivities implements ToCollection
{
  /**
   * @param array $row
   *
   * @return \Illuminate\Database\Eloquent\Model|null
   */

  function extractValue($formula)
  {
    // Matches fallback value after last comma before the closing parenthesis
    if (preg_match('/=IFERROR\([^,]+,\s*(.+?)\)$/', $formula, $matches)) {
      $fallback = trim($matches[1], '"'); // remove quotes if any
      return $fallback;
    }

    return null;
  }
  public function collection(Collection $rows)
  {

    /* if (!is_numeric($rows[0][0])) {
      preg_match('/"([\d\/]+)"/', $rows[0][8], $matches);
      $cleanDate = $matches[1] ?? null;
      // Optionally format it
      if ($cleanDate) {
        $carbonDate = Carbon::createFromFormat('n/j/Y', $cleanDate);
        $attendance_date = $carbonDate->format('Y-m-d'); // Output: 2025-04-08
      } else {
        $attendance_date = date('Y-m-d');
      }
    } */


    $i = 1;
    foreach ($rows as $row) {

      $i++;
      try {
        DB::beginTransaction();
        if ($i > 2) {

          $rider = Riders::where('rider_id', $row[1])->first();

          if (!$rider) {
            throw ValidationException::withMessages(['file' => 'Row(' . $i . ') - Rider ID ' . $row[1] . ' do not match.']);
          } else {

            /* $rider->shift = $this->extractValue($row[2]);
            $rider->attendance = $this->extractValue($row[8]) ?? '';
            $rider->save(); */
            $activity_date = date('Y-m-d', strtotime($row[0]));

            $RID = $rider->id;
            $activity_exist = RiderActivities::where('rider_id', $rider->id)->where('date', $activity_date)->first();
            $data = [
              'rider_id' => $RID,
              'd_rider_id' => $row[1],
              'date' => $activity_date,
              'payout_type' => $row[2],
              'delivered_orders' => $row[12],
              /* 'ontime_orders' => $row[7], */
              'ontime_orders_percentage' => number_format($row[13], 2),
              /* 'avg_time' => $row[9], */
              'rejected_orders' => $row[10],
              /* 'rejected_orders_percentage' => $row[11], */
              'login_hr' => $row[16],
              'delivery_rating' => str_replace("-", "0", $row[17]),

            ];
            if (!$activity_exist) {
              $ret = RiderActivities::create($data);
            } else {
              $ret = $activity_exist->update($data); // ✅ Update on model instance
            }
          }


        }
        DB::commit();
      } catch (QueryException $e) {
        DB::rollBack();
        throw $e;
      }
    }
  }

}
