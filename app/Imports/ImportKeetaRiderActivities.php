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
        // Track import errors
        $importErrors = [];
        $successCount = 0;

        foreach ($rows as $row) {
            try {
                DB::beginTransaction();

                // Skip header or empty rows
                if (!is_array($row) || empty($row['courier_id'])) {
                    continue;
                }

                // Validate courier_id is present and not empty
                $courierID = trim($row['courier_id']);
                if (empty($courierID)) {
                    throw new \Exception('Empty courier ID');
                }
                // Try to find the rider by courier_id
                $rider = Riders::where('courier_id', $courierID)->first();

                // If rider not found, prepare error details
                if (!$rider) {
                    $errorDetails = [
                        'courier_id' => $courierID,
                        'date' => $row['date'] ?? 'N/A',
                        'supervisor' => $row['supervisor'] ?? 'N/A'
                    ];

                    // Add to import errors
                    $importErrors[] = $errorDetails;

                    // Log the error
                    \Log::error('Rider not found in database', [
                        'message' => 'No rider found with the given courier_id',
                        'details' => $errorDetails
                    ]);

                    // Write to separate error log file
                    $errorLogPath = storage_path('logs/keeta_import_errors.log');
                    file_put_contents(
                        $errorLogPath,
                        date('Y-m-d H:i:s') . " - Rider not found: " . json_encode($errorDetails) . "\n",
                        FILE_APPEND
                    );

                    // Rollback and continue to next row
                    DB::rollBack();
                    continue;
                }

                // Process the date from Keeta format
                try {
                    $activity_date = date('Y-m-d', strtotime($row['date']));
                } catch (\Exception $e) {
                    $activity_date = date('Y-m-d');
                    \Log::warning('Invalid date format, using current date: ' . $activity_date);
                }

                // Prepare activity data
                $data = [
                    'rider_id' => $rider->id,
                    'd_rider_id' => $courierID,
                    'date' => $activity_date,
                    'payout_type' => 'Keeta',

                    // Delivered orders - column 13 "Delivered Tasks"
                    'delivered_orders' => isset($row[13]) ? (int)str_replace('-', '0', $row[13]) : (isset($row['delivered_tasks']) ? (int)str_replace('-', '0', $row['delivered_tasks']) : 0),

                    // On-time percentage - "delivery_experience" or "On-time Rate (D)"
                    'ontime_orders_percentage' => isset($row['delivery_experience']) ? (float)str_replace('-', '0', $row['delivery_experience']) : (isset($row[21]) ? (float)str_replace('-', '0', $row[21]) : 0),

                    // Rejected orders - column 16 "Rejected Tasks"
                    'rejected_orders' => isset($row[16]) ? (int)str_replace('-', '0', $row[16]) : (isset($row['rejected_tasks']) ? (int)str_replace('-', '0', $row['rejected_tasks']) : 0),

                    // Login hours - column 10 "Valid Online Time"
                    'login_hr' => isset($row[10]) ? (float)str_replace('-', '0', $row[10]) : (isset($row['valid_online_time']) ? (float)str_replace('-', '0', $row['valid_online_time']) : 0),

                    // Delivery rating using on-time rate as proxy
                    'delivery_rating' => isset($row['delivery_experience']) ? (float)str_replace('-', '0', $row['delivery_experience']) / 20 : (isset($row[21]) ? (float)str_replace('-', '0', $row[21]) / 20 : 0),
                ];

                // Check for existing activity record
                $activity_exist = RiderActivities::where('rider_id', $rider->id)
                    ->where('date', $activity_date)
                    ->first();

                // Create or update activity record
                if (!$activity_exist) {
                    RiderActivities::create($data);
                } else {
                    $activity_exist->update($data);
                }

                // Commit transaction and increment success count
                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                // Rollback transaction and log any unexpected errors
                DB::rollBack();
                \Log::error('Unexpected error during import: ' . $e->getMessage());
            }
        }

        // If there are import errors, throw an exception to be caught by the controller
        if (!empty($importErrors)) {
            throw new \Exception(json_encode($importErrors));
        }

        return true;
    }
}
