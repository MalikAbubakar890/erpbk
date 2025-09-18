<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\Transactions;
use Carbon\Carbon;

class TransactionService
{
  public function recordTransaction($data)
  {
    $transactionData = [
      'account_id' => $data['account_id'],
      'reference_id' => $data['reference_id'],
      'reference_type' => $data['reference_type'],
      'trans_code' => $data['trans_code'],
      'trans_date' => $data['trans_date'],
      'narration' => $data['narration'],
      'debit' => $data['debit'] ?? 0,
      'credit' => $data['credit'] ?? 0,
      'billing_month' => Carbon::parse($data['billing_month'] ?? now())->startOfMonth(),
    ];

    // Save transaction
    $transaction = Transactions::create($transactionData);

    // Update ledger balances
    $this->updateLedger(
      $transaction->account_id,
      $transaction->debit,
      $transaction->credit,
      $transaction->billing_month
    );

    return $transaction;
  }

  public function updateLedger($accountId, $debit, $credit, $billing_month)
  {
    $billing_month = Carbon::parse($billing_month)->startOfMonth();

    // Find or create this month's ledger
    $ledger = LedgerEntry::where('account_id', $accountId)
      ->where('billing_month', $billing_month)
      ->first();

    if (!$ledger) {
      // Get previous month's closing
      $lastLedger = LedgerEntry::where('account_id', $accountId)
        ->where('billing_month', '<', $billing_month)
        ->orderBy('billing_month', 'desc')
        ->first();

      $openingBalance = $lastLedger ? $lastLedger->closing_balance : 0;

      $ledger = LedgerEntry::create([
        'account_id' => $accountId,
        'billing_month' => $billing_month,
        'debit_balance' => $debit,
        'credit_balance' => $credit,
        'opening_balance' => $openingBalance,
        'closing_balance' => $openingBalance + $debit - $credit,
      ]);
    } else {
      // Update debit/credit
      $ledger->debit_balance += $debit;
      $ledger->credit_balance += $credit;
      // DO NOT recalculate opening balance — just update closing
      $ledger->closing_balance = $ledger->opening_balance + $ledger->debit_balance - $ledger->credit_balance;
      $ledger->save();
    }

    // Recalculate all future ledgers from this point
    $this->recalculateFromMonth($accountId, $billing_month);
  }


  protected function recalculateFromMonth($accountId, $fromMonth)
  {
    $ledgers = LedgerEntry::where('account_id', $accountId)
      ->where('billing_month', '>=', $fromMonth)
      ->orderBy('billing_month')
      ->get();

    $prevClosing = 0;

    foreach ($ledgers as $ledger) {
      // Set opening from previous month’s closing
      $ledger->opening_balance = $prevClosing;
      $ledger->closing_balance = $ledger->opening_balance + $ledger->debit_balance - $ledger->credit_balance;
      $ledger->save();

      $prevClosing = $ledger->closing_balance;
    }
  }


  public function deleteTransaction($transactionId)
  {
    $transactions = Transactions::where('trans_code', $transactionId)->get();

    foreach ($transactions as $transaction) {
      if ($transaction) {
        $ledger = LedgerEntry::where('account_id', $transaction->account_id)
          ->where('billing_month', $transaction->billing_month)
          ->first();

        if ($ledger) {
          // Reverse the transaction
          $ledger->debit_balance -= $transaction->debit;
          $ledger->credit_balance -= $transaction->credit;
          $ledger->closing_balance = $ledger->opening_balance + $ledger->debit_balance - $ledger->credit_balance;
          $ledger->save();

          // Delete the transaction
          $transaction->delete();

          // Update future ledgers
          $this->recalculateFromMonth($ledger->account_id, $ledger->billing_month);
        }
      }
    }
  }
}
