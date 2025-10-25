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
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class ImportKeetaRiderActivities implements ToCollection, WithCalculatedFormulas, WithHeadingRow
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
        $i = 0; // Start at 0 since WithHeadingRow already skips header
        foreach ($rows as $row) {
            $i++;
            try {
                DB::beginTransaction();

                // Debug the row structure to understand what we're getting
                \Log::info('Processing row: ' . $i);
                \Log::info(print_r($row, true));

                // Check if courier_id exists
                if (!isset($row['courier_id'])) {
                    \Log::warning('Row ' . $i . ' missing courier_id');
                    continue; // Skip this row
                }

                $rider = Riders::where('courier_id', $row['courier_id'])->first();
                if (!$rider) {
                    \Log::warning('Rider not found for courier_id: ' . $row['courier_id']);
                    continue; // Skip this row instead of throwing exception
                }

                // Process the date from Keeta format
                try {
                    $activity_date = date('Y-m-d', strtotime($row['date']));
                } catch (\Exception $e) {
                    \Log::warning('Invalid date format for row ' . $i . ': ' . ($row['date'] ?? 'null'));
                    $activity_date = date('Y-m-d'); // Use current date as fallback
                }

                $RID = $rider->id;
                $d_rider_id = $rider->courier_id;
                $activity_exist = RiderActivities::where('rider_id', $rider->id)
                    ->where('date', $activity_date)
                    ->first();

                // Map Keeta data format to our database structure
                $data = [
                    'rider_id' => $RID,
                    'd_rider_id' => $d_rider_id,
                    'date' => $activity_date,
                    'payout_type' => 'Keeta', // Mark as Keeta source
                    'delivered_orders' => isset($row['delivered_orders']) ? (int)$row['delivered_orders'] : 0,
                    'ontime_orders_percentage' => isset($row['delivery_experience']) ? (float)str_replace('-', '0', $row['delivery_experience']) : 0,
                    'rejected_orders' => isset($row['rejected_orders']) ? (int)$row['rejected_orders'] : 0,
                    'login_hr' => isset($row['online_hours']) ? (float)$row['online_hours'] : 0,
                    'delivery_rating' => isset($row['rating']) ? (float)str_replace('-', '0', $row['rating']) : 0,
                ];
                if (!$activity_exist) {
                    $ret = RiderActivities::create($data);
                } else {
                    $ret = $activity_exist->update($data); // Update on model instance
                }

                DB::commit();
            } catch (QueryException $e) {
                DB::rollBack();
                \Log::error('Database error: ' . $e->getMessage());
                throw $e;
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('General error: ' . $e->getMessage());
                // Continue processing other rows
            }
        }

        return true;
    }
}
