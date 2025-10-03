<?php

namespace App\Imports;

use App\Helpers\Account;
use App\Models\Accounts;
use App\Models\Riders;
use App\Models\Vouchers;
use App\Services\TransactionService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use DB;

class ImportRiderVoucherOnly implements ToCollection
{
    private function parseDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function collection(Collection $rows)
    {
        $rowNum = 0;
        foreach ($rows as $row) {
            $rowNum++;
            // Skip header
            if ($rowNum === 1) {
                continue;
            }
            // Required minimal columns check
            if (!isset($row[0]) || !isset($row[1]) || !isset($row[2]) || !isset($row[3])) {
                continue;
            }

            try {
                DB::beginTransaction();
                $riderExternalId = trim((string)($row[0] ?? ''));
                $billingMonth = $this->parseDate($row[1] ?? null);
                $transDate = $this->parseDate($row[2] ?? null);
                $amount = (float) ($row[3] ?? 0);
                $voucherType = trim((string)($row[4] ?? 'JV')) ?: 'JV';
                if ($voucherType == 'Advance Loan') {
                    $voucherType = 'AL';
                } elseif ($voucherType == 'Vendor Charges') {
                    $voucherType = 'VC';
                } elseif ($voucherType == 'COD') {
                    $voucherType = 'COD';
                } elseif ($voucherType == 'Penalty') {
                    $voucherType = 'PN';
                } else {
                    $voucherType = 'JV';
                }
                $accountId = isset($row[5]) && $row[5] !== '' ? (int) $row[5] : null;

                $rider = Riders::where('rider_id', $riderExternalId)->first();
                if (!$rider) {
                    throw ValidationException::withMessages([
                        'file' => "Row({$rowNum}) - Rider ID {$riderExternalId} not found."
                    ]);
                }
                $riderAccount = Accounts::where('ref_id', $rider->id)->first();

                $transCode = Account::trans_code();

                $voucherData = [
                    'trans_date' => $transDate ?: date('Y-m-d'),
                    'posting_date' => $transDate ?: date('Y-m-d'),
                    'billing_month' => $billingMonth ?: date('Y-m-01'),
                    'payment_to' => $riderAccount->id,
                    'payment_from' => $accountId, // nullable
                    'payment_type' => 0,
                    'voucher_type' => $voucherType,
                    'amount' => $amount,
                    'remarks' => 'Imported rider voucher',
                    'ref_id' => $rider->id,
                    'rider_id' => $rider->id,
                    'trans_code' => $transCode,
                    'Created_By' => auth()->id(),
                    'status' => 1,
                ];

                $voucher = Vouchers::create($voucherData);

                // Create accounting transactions (debit rider account, credit provided account)
                $tx = new TransactionService();

                // Get counter account name for better narration
                $counterAccountName = '';
                if (!empty($accountId)) {
                    $counterAccount = Accounts::find($accountId);
                    $counterAccountName = $counterAccount ? $counterAccount->name : 'Account ID ' . $accountId;
                }

                // Debit - rider account
                $debitNarration = $voucherType . ' - ' . $rider->name . ' (Rider ID: ' . $rider->rider_id . ')';
                if (!empty($counterAccountName)) {
                    $debitNarration .= ' - To ' . $counterAccountName;
                }

                $tx->recordTransaction([
                    'account_id' => $riderAccount->id,
                    'reference_id' => $voucher->id,
                    'reference_type' => $voucherType,
                    'trans_code' => $transCode,
                    'trans_date' => $voucherData['trans_date'],
                    'narration' => $debitNarration,
                    'debit' => $amount,
                    'billing_month' => $voucherData['billing_month'],
                ]);

                // Credit - counter account if provided
                if (!empty($accountId)) {
                    $creditNarration = $voucherType . ' - From ' . $rider->name . ' (Rider ID: ' . $rider->rider_id . ')';

                    $tx->recordTransaction([
                        'account_id' => $accountId,
                        'reference_id' => $voucher->id,
                        'reference_type' => $voucherType,
                        'trans_code' => $transCode,
                        'trans_date' => $voucherData['trans_date'],
                        'narration' => $creditNarration,
                        'credit' => $amount,
                        'billing_month' => $voucherData['billing_month'],
                    ]);
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }
}
