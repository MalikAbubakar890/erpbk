<?php

namespace App\Http\Controllers;

use App\Helpers\Account;
use App\Helpers\Common;
use App\Http\Requests\StoreVisaExpenseRequest;
use App\Http\Requests\UpdateVisaExpenseRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Bikes;
use App\Models\Riders;
use App\Models\visa_expenses;
use App\Models\Accounts;
use App\Models\Vouchers;
use App\Models\LedgerEntry;
use App\Models\visa_installment_plan;
use App\Models\Transactions;
use App\Repositories\VisaExpensesRepository;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Flash;
use DB;

class VisaexpenseController extends Controller
{
    protected $visaRepo;
    public function __construct(VisaExpensesRepository $visaRepo)
    {
        $this->visaRepo = $visaRepo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_view')) {
            abort(403, 'Unauthorized action.');
        }

        $parent = Accounts::where('name', 'Visa Expense')->first();
        $perPage = request()->input('per_page', 50);
        $perPage = is_numeric($perPage) ? (int) $perPage : 50;
        $perPage = $perPage > 0 ? $perPage : 50;

        $query = Accounts::query()
            ->orderBy('id', 'desc')
            ->where('parent_id', $parent->id);

        // Existing filters
        if ($request->has('account_code') && !empty($request->account_code)) {
            $query->where('account_code', 'like', '%' . $request->account_code . '%');
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Payment status filter
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $paymentStatus = $request->payment_status;

            if ($paymentStatus === 'paid') {
                // Show accounts where ALL visa_expense records are paid
                $query->whereHas('visa_expenses', function ($q) {
                    // Account must have at least one visa_expense record
                    $q->select('rider_id');
                })->whereDoesntHave('visa_expenses', function ($q) {
                    // And no visa_expense record should be unpaid
                    $q->where('payment_status', 'unpaid');
                });
            } elseif ($paymentStatus === 'unpaid') {
                // Show accounts that have at least one unpaid visa_expense record
                $query->whereHas('visa_expenses', function ($q) {
                    $q->where('payment_status', 'unpaid');
                });
            }
        }

        $data = $query->paginate($perPage);

        if ($request->ajax()) {
            $tableData = view('visa_expenses.account_table', [
                'data' => $data,
                'parent' => $parent,
            ])->render();
            $paginationLinks = $data->links('pagination')->render();
            return response()->json([
                'tableData' => $tableData,
                'paginationLinks' => $paginationLinks,
            ]);
        }

        return view('visa_expenses.account_index', [
            'data' => $data,
            'parent' => $parent,
        ]);
    }
    public function accountcreate(Request $request)
    {
        $rider = DB::Table('riders')->where('id', $request->rider_id)->first();
        $parent = Accounts::where('name', 'Visa Expense')->first();
        if (!$parent) {
            Flash::error('Parent account "Visa Expense" not found.');
            return redirect()->back();
        }
        $exists = Accounts::where('name', $rider->name)->where('parent_id', $parent->id)->exists();
        if ($exists) {
            Flash::error('Account with this name already exists.');
            return redirect()->back();
        }
        $newdata = new Accounts();
        $newdata->name = $rider->name;
        $newdata->parent_id = $parent->id;
        $newdata->account_type = 'Expense';
        $newdata->ref_id = $request->rider_id;
        $newdata->status = 1;
        $newdata->save();
        $newdata->account_code = 'ACCT-' . str_pad($newdata->id, 5, '0', STR_PAD_LEFT);
        $newdata->save();
        Flash::success('Account added successfully.');
        return redirect()->back();
    }
    public function editaccount(Request $request)
    {
        $parent = Accounts::where('name', 'RTA Fines')->first();
        if (!$parent) {
            Flash::error('Parent account "RTA Fines" not found.');
        }
        $newdata = Accounts::find($request->id);
        $newdata->name = $request->name;
        $newdata->parent_id = $parent->id;
        $newdata->account_type = 'Expense';
        $newdata->status = 1;
        $newdata->save();
        $newdata->account_code = 'ACCT-' . str_pad($newdata->id, 5, '0', STR_PAD_LEFT);
        $newdata->save();
        Flash::success('Account Updated successfully.');
        return redirect()->back();
    }

    public function deleteaccount($id)
    {
        // Check if any visa_expenses exist for this rider_id
        $hasExpenses = visa_expenses::where('rider_id', $id)->exists();

        if ($hasExpenses) {
            Flash::error('Cannot delete account. Visa Expense entries exist for this account.');
            return redirect()->back();
        }

        // No expenses — safe to delete
        Accounts::where('id', $id)->delete();
        Flash::success('Account deleted successfully.');
        return redirect()->back();
    }

    public function generatentries(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_view')) {
            abort(403, 'Unauthorized action.');
        }

        // Auto-mark installments silently in the background
        $this->checkAndAutoMarkInstallments($id);
        $perPage = request()->input('per_page', 50);
        $perPage = is_numeric($perPage) ? (int) $perPage : 50;
        $perPage = $perPage > 0 ? $perPage : 50;
        $query = visa_expenses::query()
            ->orderBy('id', 'asc')->where('rider_id', $id);
        if ($request->has('trans_date') && !empty($request->trans_date)) {
            $fromDate = \Carbon\Carbon::createFromFormat('Y-d-m', $request->trans_date);
            $query->where('trans_date', $billingMonth->year);
        }
        if ($request->has('trans_code') && !empty($request->trans_code)) {
            $query->where('trans_code', $request->trans_code);
        }
        if ($request->filled('date')) {
            $toDate = \Carbon\Carbon::createFromFormat('Y-d-m', $request->date);
            $query->where('date', '<=', $toDate);
        }
        if ($request->has('visa_status') && !empty($request->visa_status)) {
            $query->where('visa_status', $request->visa_status);
        }
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }
        $data = $query->paginate($perPage);
        $account = Accounts::where('id', $id)->first();
        if ($request->ajax()) {
            $tableData = view('visa_expenses.table', [
                'data' => $data,
                'account' => $account,
            ])->render();
            $paginationLinks = $data->links('pagination')->render();
            return response()->json([
                'tableData' => $tableData,
                'paginationLinks' => $paginationLinks,
            ]);
        }
        return view('visa_expenses.index', [
            'data' => $data,
            'account' => $account,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $data = Accounts::where('id', $id)->first();
        return view('visa_expenses.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rider_id'       => 'required|exists:accounts,id',
            'visa_status'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('visa_expenses')->where(function ($query) use ($request) {
                    return $query->where('rider_id', $request->rider_id);
                }),
            ],
            'billing_month'  => 'required|date_format:Y-m',
            'detail'         => 'nullable|string',
            'amount'         => 'required|numeric|min:0',
            'attach_file'    => 'nullable|string|max:255',
        ]);

        try {
            $trans_code = Account::trans_code();
            $billingMonth = $validated['billing_month'] . "-01";
            $trans_date = Carbon::today();
            $visaExpenses = visa_expenses::create([
                'rider_id'       => $validated['rider_id'],
                'visa_status'    => $validated['visa_status'],
                'billing_month'  => $billingMonth,
                'date'           => $request->date,
                'amount'         => $request->amount,
                'payment_status' => 'unpaid',
                'detail'         => $validated['detail'],
                'trans_date'     => $trans_date,
                'trans_code'     => $trans_code,
            ]);
            Flash::success('Visa Expenses added successfully ');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Flash::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function viewvoucher($id)
    {
        $data = visa_expenses::where('id', $id)->first();
        $accounts = Accounts::where('id', $data->rider_id)->first();
        return view('visa_expenses.viewvoucher', compact('data', 'accounts'));
    }
    public function installmentPlan(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_view')) {
            abort(403, 'Unauthorized action.');
        }

        // Auto-mark installments as paid if their date has arrived
        $this->checkAndAutoMarkInstallments($id);

        $perPage = request()->input('per_page', 50);
        $perPage = is_numeric($perPage) ? (int) $perPage : 50;
        $perPage = $perPage > 0 ? $perPage : 50;

        $query = visa_installment_plan::query()
            ->where('rider_id', $id)
            ->orderBy('date', 'asc');

        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('billing_month') && !empty($request->billing_month)) {
            $query->where('billing_month', 'like', '%' . $request->billing_month . '%');
        }

        $data = $query->paginate($perPage);
        $account = Accounts::where('id', $id)->first();

        if ($request->ajax()) {
            $tableData = view('visa_expenses.installmentPlanTable', [
                'data' => $data,
                'account' => $account,
            ])->render();
            $paginationLinks = $data->links('pagination')->render();
            return response()->json([
                'tableData' => $tableData,
                'paginationLinks' => $paginationLinks,
            ]);
        }

        return view('visa_expenses.installmentPlan', compact('data', 'account'));
    }

    public function createInstallmentPlanForm($riderId)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_create')) {
            abort(403, 'Unauthorized action.');
        }

        // Auto-mark installments silently in the background
        $this->checkAndAutoMarkInstallments($riderId);

        $account = Accounts::findOrFail($riderId);

        // Calculate total unpaid amount for this rider
        $totalUnpaidAmount = visa_expenses::where('rider_id', $riderId)
            ->where('payment_status', 'unpaid')
            ->sum('amount');

        if ($totalUnpaidAmount <= 0) {
            Flash::error('No unpaid visa expenses found for this rider.');
            return redirect()->back();
        }

        // Check if there's already an installment plan for this rider in the current month
        $currentMonth = Carbon::now()->format('Y-m');
        $existingCurrentMonthPlan = visa_installment_plan::where('rider_id', $riderId)
            ->where('billing_month', $currentMonth)
            ->exists();

        if ($existingCurrentMonthPlan) {
            Flash::warning('An installment plan already exists for this rider in ' . Carbon::now()->format('F Y') . '. You can still create a new plan, but please select a different starting month.');
        }

        return view('visa_expenses.createInstallmentPlan', compact('account', 'totalUnpaidAmount', 'existingCurrentMonthPlan'));
    }

    public function createInstallmentPlan(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rider_id' => 'required|exists:accounts,id',
            'total_amount' => 'required|numeric|min:1',
            'billing_month' => 'required|string',
            'number_of_installments' => 'required|integer|min:1|max:12',
            'installment_amounts' => 'required|array|min:1',
            'installment_amounts.*' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Get the rider account (visa expense account)
            $riderAccount = Accounts::findOrFail($validated['rider_id']);



            // Check if there's already an installment plan for this rider in the current month
            $currentMonth = Carbon::parse($validated['billing_month'] . '-01')->format('Y-m');
            $existingCurrentMonthPlan = visa_installment_plan::where('rider_id', $validated['rider_id'])
                ->where('billing_month', $currentMonth)
                ->exists();

            if ($existingCurrentMonthPlan) {
                Flash::error('Cannot create installment plan. An installment plan already exists for this rider in ' . Carbon::parse($validated['billing_month'] . '-01')->format('F Y') . '.');
                return redirect()->back();
            }

            // Validate that installment amounts sum to total amount
            $installmentAmounts = $validated['installment_amounts'];
            $sumOfInstallments = array_sum($installmentAmounts);
            $totalAmount = $validated['total_amount'];

            if (abs($sumOfInstallments - $totalAmount) > 0.01) {
                Flash::error('The sum of individual installment amounts (' . number_format($sumOfInstallments, 2) . ') does not match the total amount (' . number_format($totalAmount, 2) . ').');
                return redirect()->back()->withInput();
            }

            // Validate number of installments matches the array length
            if (count($installmentAmounts) != $validated['number_of_installments']) {
                Flash::error('Number of installment amounts does not match the selected number of installments.');
                return redirect()->back()->withInput();
            }

            // Find the liability account using ref_id from rider account
            $liabilityAccount = Accounts::where('ref_id', $riderAccount->ref_id)
                ->where('account_type', 'Liability')
                ->where('parent_id', 1)
                ->first();
            if (!$liabilityAccount) {
                Flash::error('Liability account not found for this rider. Please create the liability account first.');
                return redirect()->back();
            }
            $rider = Riders::findOrFail($riderAccount->ref_id);

            // Create multiple installment entries for consecutive months
            for ($i = 0; $i < $validated['number_of_installments']; $i++) {
                // Calculate billing month for this installment (consecutive months)
                $billingDate = Carbon::parse($validated['billing_month'] . '-01')->addMonths($i);
                $billingMonth = $billingDate->format('Y-m-d');
                $billingMonthFormatted = $billingDate->format('Y-m');

                // Calculate installment date - set to 10th of next month from billing month
                $installmentDate = $billingDate->copy()->addMonth()->day(10)->format('Y-m-d');

                // Get the individual installment amount
                $installmentAmount = $installmentAmounts[$i];

                // Create installment entry
                $installment = visa_installment_plan::create([
                    'rider_id' => $validated['rider_id'],
                    'billing_month' => $billingMonthFormatted,
                    'amount' => $installmentAmount,
                    'total_amount' => $totalAmount, // Store the total amount for reference
                    'status' => visa_installment_plan::STATUS_PENDING,
                    'date' => $installmentDate,
                    'created_by' => auth()->user()->id,
                ]);

                // Generate unique transaction code for each installment
                $trans_code = Account::trans_code();
                $trans_date = Carbon::today();
                $TransactionService = new TransactionService();

                // Create separate voucher for each installment
                $voucher = Vouchers::create([
                    'rider_id' => $rider->id, // Original rider ref_id
                    'trans_date' => $trans_date,
                    'trans_code' => $trans_code,
                    'billing_month' => $billingMonth,
                    'payment_type' => 1, // Liability payment
                    'voucher_type' => 'VL', // Visa Loan
                    'remarks' => 'Loan Voucher - Installment ' . ($i + 1) . ' of ' . $validated['number_of_installments'] . ' (Amount: ' . number_format($installmentAmount, 2) . ')',
                    'amount' => $installmentAmount,
                    'Created_By' => auth()->user()->id,
                    'ref_id' => $installment->id,
                ]);

                // Debit the liability account for each installment
                $TransactionService->recordTransaction([
                    'account_id' => $liabilityAccount->id,
                    'reference_id' => $installment->id,
                    'reference_type' => 'VL',
                    'trans_code' => $trans_code,
                    'trans_date' => $trans_date,
                    'narration' => $rider->rider_id . ' - ' . $rider->name . ' - deducting installment ' . ($i + 1) . ' - ' . $billingMonthFormatted . ' (Amount: ' . number_format($installmentAmount, 2) . ')',
                    'debit' => $installmentAmount,
                    'billing_month' => $billingMonth,
                    'created_by' => auth()->user()->id,
                ]);

                // Credit the rider account (visa expense account) for each installment
                $TransactionService->recordTransaction([
                    'account_id' => $riderAccount->id,
                    'reference_id' => $installment->id,
                    'reference_type' => 'VL',
                    'trans_code' => $trans_code,
                    'trans_date' => $trans_date,
                    'narration' => $rider->rider_id . ' - ' . $rider->name . ' - deducting installment ' . ($i + 1) . ' - ' . $billingMonthFormatted . ' (Amount: ' . number_format($installmentAmount, 2) . ')',
                    'credit' => $installmentAmount,
                    'billing_month' => $billingMonth,
                ]);

                // Create ledger entry for liability account for each installment
                $lastLedger = DB::table('ledger_entries')
                    ->where('account_id', $liabilityAccount->id)
                    ->orderBy('billing_month', 'desc')
                    ->first();

                $opening_balance = $lastLedger ? $lastLedger->closing_balance : 0.00;
                $debit_balance = $installmentAmount;
                $credit_balance = 0.00;
                $closing_balance = $opening_balance + $installmentAmount; // Liability increases with debit

                DB::table('ledger_entries')->insert([
                    'account_id' => $liabilityAccount->id,
                    'billing_month' => $billingMonth,
                    'opening_balance' => $opening_balance,
                    'debit_balance' => $debit_balance,
                    'credit_balance' => $credit_balance,
                    'closing_balance' => $closing_balance,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $installmentDetails = '';
            foreach ($installmentAmounts as $index => $amount) {
                $installmentDetails .= 'Installment ' . ($index + 1) . ': ' . number_format($amount, 2) . ', ';
            }
            $installmentDetails = rtrim($installmentDetails, ', ');

            Flash::success($validated['number_of_installments'] . ' installment entries created successfully with individual amounts: ' . $installmentDetails . '. Total amount: ' . number_format($validated['total_amount'], 2));
            return redirect()->route('VisaExpense.installmentPlan', $validated['rider_id']);
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error creating installment plan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function payInstallment(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_pay')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'installment_id' => 'required|exists:visa_installment_plans,id'
        ]);

        try {
            DB::beginTransaction();

            $installment = visa_installment_plan::findOrFail($validated['installment_id']);

            if ($installment->status === visa_installment_plan::STATUS_PAID) {
                Flash::error('This installment is already paid.');
                return redirect()->back();
            }

            // Mark installment as paid
            $installment->status = visa_installment_plan::STATUS_PAID;
            $installment->save();

            DB::commit();

            Flash::success('Installment marked as paid successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error processing payment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function updateInstallmentField(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'installment_id' => 'required|exists:visa_installment_plans,id',
            'field' => 'required|in:date,billing_month,amount',
            'value' => 'required',
            'update_subsequent' => 'nullable|in:true,false,1,0'
        ]);

        // Convert update_subsequent to boolean
        $validated['update_subsequent'] = in_array($request->input('update_subsequent'), ['true', '1', 1, true], true);

        try {
            DB::beginTransaction();

            $installment = visa_installment_plan::findOrFail($validated['installment_id']);

            if ($installment->status === visa_installment_plan::STATUS_PAID) {
                Flash::error('Cannot edit a paid installment.');
                return redirect()->back();
            }

            // Get the rider account for transactions
            $riderAccount = Accounts::findOrFail($installment->rider_id);
            $liabilityAccount = Accounts::where('ref_id', $riderAccount->ref_id)
                ->where('account_type', 'Liability')
                ->where('parent_id', 1)
                ->first();

            $rider = Riders::findOrFail($riderAccount->ref_id);

            // Update the installment field
            $oldValue = $installment->{$validated['field']};

            if ($validated['field'] === 'billing_month') {
                $installment->billing_month = $validated['value'];
                // Check if billing month already contains day, if not add it
                $billingMonth = (strlen($validated['value']) <= 7) ? $validated['value'] . "-01" : $validated['value'];

                // Update current installment date to 10th of next month from new billing month
                $newBillingDate = Carbon::parse($validated['value'] . '-01');
                $installment->date = $newBillingDate->addMonth()->day(10)->format('Y-m-d');
                $installment->updated_by = auth()->user()->id;
                $installment->save();

                // Update subsequent installments if requested
                if ($validated['update_subsequent']) {
                    $this->updateSubsequentInstallments($installment, 'billing_month', $validated['value'], $rider);
                }
            } elseif ($validated['field'] === 'date') {
                $installment->date = $validated['value'];
                $installment->updated_by = auth()->user()->id;
                $installment->save();

                $billingMonth = $installment->billing_month;
                // Ensure billing month has proper format for voucher/transaction updates
                if (strlen($billingMonth) <= 7) {
                    $billingMonth = $billingMonth . "-01";
                }

                // Update subsequent installments if requested
                if ($validated['update_subsequent']) {
                    $this->updateSubsequentInstallments($installment, 'date', $validated['value'], $rider);
                }
            } elseif ($validated['field'] === 'amount') {
                $installment->amount = $validated['value'];
                $installment->updated_by = auth()->user()->id;
                $installment->save();

                $billingMonth = $installment->billing_month;
                // Ensure billing month has proper format for voucher/transaction updates
                if (strlen($billingMonth) <= 7) {
                    $billingMonth = $billingMonth . "-01";
                }

                // Update subsequent installments if requested - handle proportional recalculation
                if ($validated['update_subsequent']) {
                    $this->recalculateInstallmentAmounts($installment, $validated['value'], $rider);
                }
            }

            // Update voucher
            $voucher = Vouchers::where('ref_id', $installment->id)
                ->where('voucher_type', 'VL')
                ->first();

            if ($voucher) {
                if ($validated['field'] === 'billing_month') {
                    $voucher->billing_month = $billingMonth;
                } elseif ($validated['field'] === 'amount') {
                    $voucher->amount = $validated['value'];
                } elseif ($validated['field'] === 'date') {
                    $voucher->trans_date = $validated['value'];
                }
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }

            // Update transactions
            $transactions = Transactions::where('reference_id', $installment->id)
                ->where('reference_type', 'VL')
                ->get();

            foreach ($transactions as $transaction) {
                if ($validated['field'] === 'billing_month') {
                    $transaction->billing_month = $billingMonth;
                    $transaction->narration = $rider->rider_id . ' - ' . $rider->name . ' - deducting installment - ' . $validated['value'];
                } elseif ($validated['field'] === 'amount') {
                    if ($transaction->credit > 0) {
                        $transaction->credit = $validated['value'];
                    } else {
                        $transaction->debit = $validated['value'];
                    }
                } elseif ($validated['field'] === 'date') {
                    $transaction->trans_date = $validated['value'];
                }
                $transaction->updated_at = now();
                $transaction->save();
            }

            // Update ledger entry if amount changed
            if ($validated['field'] === 'amount' && $liabilityAccount) {
                $ledgerEntry = DB::table('ledger_entries')
                    ->where('account_id', $liabilityAccount->id)
                    ->where('billing_month', $billingMonth)
                    ->first();

                if ($ledgerEntry) {
                    $difference = $validated['value'] - $oldValue;
                    DB::table('ledger_entries')
                        ->where('account_id', $liabilityAccount->id)
                        ->where('billing_month', $billingMonth)
                        ->update([
                            'debit_balance' => $validated['value'],
                            'closing_balance' => $ledgerEntry->opening_balance + $validated['value'],
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();

            $message = ucfirst($validated['field']) . ' updated successfully with voucher and transactions.';
            if ($validated['update_subsequent']) {
                $message .= ' Subsequent installments were also updated accordingly.';
            }

            Flash::success($message);
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error updating installment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function deleteInstallment($id)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $installment = visa_installment_plan::findOrFail($id);

            if ($installment->status === visa_installment_plan::STATUS_PAID) {
                Flash::error('Cannot delete a paid installment.');
                return redirect()->back();
            }

            // Delete related vouchers
            Vouchers::where('ref_id', $installment->id)
                ->where('voucher_type', 'VL')
                ->delete();

            // Delete related transactions
            Transactions::where('reference_id', $installment->id)
                ->where('reference_type', 'VL')
                ->delete();

            // Delete related ledger entries
            $riderAccount = Accounts::findOrFail($installment->rider_id);
            $liabilityAccount = Accounts::where('ref_id', $riderAccount->ref_id)
                ->where('account_type', 'Liability')
                ->where('parent_id', 1)
                ->first();

            if ($liabilityAccount) {
                DB::table('ledger_entries')
                    ->where('account_id', $liabilityAccount->id)
                    ->where('billing_month', $installment->billing_month)
                    ->delete();
            }

            // Delete the installment
            $installment->delete();

            DB::commit();

            Flash::success('Installment deleted successfully along with voucher and transactions.');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error deleting installment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function generateInstallmentInvoice($riderId)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Get the rider account
            $account = Accounts::findOrFail($riderId);
            $rider = Riders::findOrFail($account->ref_id);

            // Get all installments for this rider, ordered by billing month
            $installments = visa_installment_plan::where('rider_id', $riderId)
                ->orderBy('billing_month', 'asc')
                ->get();

            if ($installments->isEmpty()) {
                Flash::error('No installment plans found for this rider.');
                return redirect()->back();
            }

            return view('visa_expenses.installmentInvoice', compact('rider', 'installments', 'account'));
        } catch (\Exception $e) {
            Flash::error('Error generating invoice: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function payfine(Request $request)
    {
        DB::beginTransaction();

        try {

            $fine = visa_expenses::findOrFail($request->id);
            $fine->pay_account = $request->account;

            if ($fine->payment_status == 'paid') {
                $fine->payment_status = 'unpaid';
            } else {
                $fine->payment_status = 'paid';
                $payment_type_flag = match ($request->payment_type) {
                    'Liability' => 1,
                    'Asset' => 0,
                    default => null,
                };
                $photo = $request->file('attach_file');
                $docFile = $photo->store('vouchers');
                $remarks = $request->voucher_type === 'LV' ? 'Visa Expense Voucher' : 'Journal Voucher';

                $trans_code = Account::trans_code();
                $TransactionService = new TransactionService();

                $billingMonth = $fine->billing_month ?? date('Y-m-01');
                $transDate = $fine->trans_date;

                // 1. Fine Amount
                if ($fine->amount > 0) {
                    // Debit RTA Account
                    $TransactionService->recordTransaction([
                        'account_id'     => $fine->rider_id,
                        'reference_id'   => $fine->id,
                        'reference_type' => 'LV',
                        'trans_code'     => $trans_code,
                        'trans_date'     => $transDate,
                        'narration'      => $fine->detail ?? 'Viss Expense Payment',
                        'debit'          => $fine->amount,
                        'billing_month'  => $billingMonth,
                    ]);
                }
                if ($fine->amount > 0) {
                    // Credit Selected Payment Account
                    $TransactionService->recordTransaction([
                        'account_id'     => $request->account,
                        'reference_id'   => $fine->id,
                        'reference_type' => 'LV',
                        'trans_code'     => $trans_code,
                        'trans_date'     => $transDate,
                        'narration'      => $fine->detail ?? 'Visa Expense Payment',
                        'credit'         => $fine->amount,
                        'billing_month'  => $billingMonth,
                    ]);
                }
                Vouchers::create([
                    'rider_id'      => $request->rider_id,
                    'trans_date'    => $transDate,
                    'trans_code'    => $trans_code,
                    'trip_date'     => $request->trip_date,
                    'billing_month' => $billingMonth,
                    'payment_type'  => $payment_type_flag,
                    'voucher_type'  => $request->voucher_type,
                    'remarks'       => $remarks,
                    'amount'        => $fine->amount,
                    'Created_By'    => $request->Created_By,
                    'attach_file'   => $docFile,
                    'pay_account'   => $request->account,
                    'ref_id'        => $fine->id,
                ]);

                // 5. Ledger Entry (Against Payment Account)
                $total_amount = floatval($fine->amount);
                $lastLedger = DB::table('ledger_entries')
                    ->where('account_id', $request->account)
                    ->orderBy('billing_month', 'desc')
                    ->first();

                $opening_balance = $lastLedger ? $lastLedger->closing_balance : 0.00;
                $debit_balance = $credit_balance = 0.00;

                if ($payment_type_flag === 1) { // Liability
                    $debit_balance = $total_amount;
                    $closing_balance = $opening_balance + $total_amount;
                } elseif ($payment_type_flag === 0) { // Asset
                    $credit_balance = $total_amount;
                    $closing_balance = $opening_balance - $total_amount;
                } else {
                    $closing_balance = $opening_balance;
                }

                DB::table('ledger_entries')->insert([
                    'account_id'      => $request->account,
                    'billing_month'   => $billingMonth,
                    'opening_balance' => $opening_balance,
                    'debit_balance'   => $debit_balance,
                    'credit_balance'  => $credit_balance,
                    'closing_balance' => $closing_balance,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
            $fine->save();
            DB::commit();
            Flash::success('Visa Expense Paid Successfully with Transaction and Ledger Entries.');
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error: ' . $e->getMessage());
        }

        return redirect(route('VisaExpense.generatentries', $fine->rider_id));
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $visaExpenses = visa_expenses::find($id);
        $data = Accounts::where('id', $visaExpenses->rider_id)->first();
        if (empty($visaExpenses)) {
            Flash::error('Visa Expenses not found');

            return redirect(route('visaExpenses.index'));
        }
        return view('visa_expenses.edit', compact('data', 'visaExpenses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $visaExpenses = visa_expenses::findOrFail($request->id);
        $oldAmount = $visaExpenses->amount;
        $trans_code = $visaExpenses->trans_code;
        $billingMonth = $request->billing_month . "-01";
        $trans_date = $visaExpenses->trans_date ?? Carbon::today();
        $visaExpenses->visa_status = $request->visa_status;
        $visaExpenses->billing_month = $billingMonth;
        $visaExpenses->date = $request->date;
        $visaExpenses->amount = $request->amount;
        $visaExpenses->detail = $request->detail;
        $visaExpenses->save();
        Flash::success('Visa Expense updated successfully');
        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $visaExpenses = visa_expenses::find($id);

        //$banks = $this->banksRepository->find($id);

        if (empty($visaExpenses)) {
            Flash::error('Visa Expense Entry not found');
        }
        Transactions::where('reference_id', $visaExpenses->id)->delete();
        Vouchers::where('ref_id', $visaExpenses->id)->delete();
        LedgerEntry::where('account_id', $visaExpenses->rider_id)->delete();
        $visaExpenses->delete($id);
        Flash::success('Visa Expenses Entry deleted successfully.');
        return redirect()->back();
    }

    /**
     * Update subsequent installments based on the current installment change
     */
    private function updateSubsequentInstallments($currentInstallment, $field, $newValue, $rider)
    {
        // Get all installments for the same rider, ordered by ID (creation order)
        $allInstallments = visa_installment_plan::where('rider_id', $currentInstallment->rider_id)
            ->where('status', visa_installment_plan::STATUS_PENDING)
            ->orderBy('id', 'asc')
            ->get();

        // Find the current installment's position
        $currentIndex = $allInstallments->search(function ($item) use ($currentInstallment) {
            return $item->id === $currentInstallment->id;
        });

        // Get subsequent installments (after current one)
        $subsequentInstallments = $allInstallments->slice($currentIndex + 1);

        if ($subsequentInstallments->isEmpty()) {
            return;
        }

        $monthIncrement = 1;
        foreach ($subsequentInstallments as $subsequentInstallment) {
            if ($field === 'billing_month') {
                // Calculate new billing month for subsequent installments
                $baseDate = Carbon::parse($newValue . '-01');
                $newBillingMonth = $baseDate->copy()->addMonths($monthIncrement)->format('Y-m');
                $billingMonthWithDay = $newBillingMonth . '-01';

                // Calculate new date - set to 10th of next month from billing month
                $newInstallmentDate = $baseDate->copy()->addMonths($monthIncrement)->addMonth()->day(10)->format('Y-m-d');

                $subsequentInstallment->billing_month = $newBillingMonth;
                $subsequentInstallment->date = $newInstallmentDate;
                $subsequentInstallment->updated_by = auth()->user()->id;
                $subsequentInstallment->save();

                // Update related voucher
                $voucher = Vouchers::where('ref_id', $subsequentInstallment->id)
                    ->where('voucher_type', 'VL')
                    ->first();
                if ($voucher) {
                    $voucher->billing_month = $billingMonthWithDay;
                    $voucher->trans_date = $newInstallmentDate;
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }

                // Update related transactions
                $transactions = Transactions::where('reference_id', $subsequentInstallment->id)
                    ->where('reference_type', 'VL')
                    ->get();
                foreach ($transactions as $transaction) {
                    $transaction->billing_month = $billingMonthWithDay;
                    $transaction->trans_date = $newInstallmentDate;
                    $transaction->narration = $rider->rider_id . ' - ' . $rider->name . ' - deducting installment - ' . $newBillingMonth;
                    $transaction->updated_at = now();
                    $transaction->save();
                }
            } elseif ($field === 'date') {
                // Calculate new date for subsequent installments
                $baseDate = Carbon::parse($newValue);
                $newDate = $baseDate->copy()->addMonths($monthIncrement)->format('Y-m-d');

                $subsequentInstallment->date = $newDate;
                $subsequentInstallment->updated_by = auth()->user()->id;
                $subsequentInstallment->save();

                // Update related voucher
                $voucher = Vouchers::where('ref_id', $subsequentInstallment->id)
                    ->where('voucher_type', 'VL')
                    ->first();
                if ($voucher) {
                    $voucher->trans_date = $newDate;
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }

                // Update related transactions
                $transactions = Transactions::where('reference_id', $subsequentInstallment->id)
                    ->where('reference_type', 'VL')
                    ->get();
                foreach ($transactions as $transaction) {
                    $transaction->trans_date = $newDate;
                    $transaction->updated_at = now();
                    $transaction->save();
                }
            } elseif ($field === 'amount') {
                // For amount changes, we need to recalculate proportionally
                // This will be handled in the main method, not here
                // Skip subsequent updates for amount field
                break;
            }

            $monthIncrement++;
        }
    }

    /**
     * Recalculate installment amounts proportionally when one amount changes
     */
    private function recalculateInstallmentAmounts($currentInstallment, $newAmount, $rider)
    {
        // Get all installments for this rider (including current one)
        $allInstallments = visa_installment_plan::where('rider_id', $currentInstallment->rider_id)
            ->where('status', visa_installment_plan::STATUS_PENDING)
            ->orderBy('date', 'asc')
            ->get();

        if ($allInstallments->count() <= 1) {
            return; // No other installments to update
        }

        // Calculate current total
        $currentTotal = $allInstallments->sum('amount');

        // Calculate new total (current total - old amount + new amount)
        $oldAmount = $currentInstallment->amount;
        $newTotal = $currentTotal - $oldAmount + $newAmount;

        // Calculate new amount per installment (equal distribution)
        $newAmountPerInstallment = $newTotal / $allInstallments->count();

        // Update all installments with the new calculated amount
        foreach ($allInstallments as $installment) {
            $installment->amount = $newAmountPerInstallment;
            $installment->updated_by = auth()->user()->id;
            $installment->save();

            // Update related voucher
            $voucher = Vouchers::where('ref_id', $installment->id)
                ->where('voucher_type', 'VL')
                ->first();
            if ($voucher) {
                $voucher->amount = $newAmountPerInstallment;
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }

            // Update related transactions
            $transactions = Transactions::where('reference_id', $installment->id)
                ->where('reference_type', 'VL')
                ->get();
            foreach ($transactions as $transaction) {
                if ($transaction->credit > 0) {
                    $transaction->credit = $newAmountPerInstallment;
                } else {
                    $transaction->debit = $newAmountPerInstallment;
                }
                $transaction->updated_at = now();
                $transaction->save();
            }

            // Update related ledger entry
            $riderAccount = Accounts::findOrFail($installment->rider_id);
            $liabilityAccount = Accounts::where('ref_id', $riderAccount->ref_id)
                ->where('account_type', 'Liability')
                ->where('parent_id', 1)
                ->first();

            if ($liabilityAccount) {
                $billingMonthForLedger = (strlen($installment->billing_month) <= 7) ?
                    $installment->billing_month . '-01' : $installment->billing_month;

                $ledgerEntry = DB::table('ledger_entries')
                    ->where('account_id', $liabilityAccount->id)
                    ->where('billing_month', $billingMonthForLedger)
                    ->first();

                if ($ledgerEntry) {
                    DB::table('ledger_entries')
                        ->where('account_id', $liabilityAccount->id)
                        ->where('billing_month', $billingMonthForLedger)
                        ->update([
                            'debit_balance' => $newAmountPerInstallment,
                            'closing_balance' => $ledgerEntry->opening_balance + $newAmountPerInstallment,
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    /**
     * Automatically mark installments as paid when their date equals today
     */
    public function autoMarkInstallmentsAsPaid($riderId = null)
    {
        $today = Carbon::today()->format('Y-m-d');

        $query = visa_installment_plan::where('status', visa_installment_plan::STATUS_PENDING)
            ->where('date', '<=', $today);

        // If rider ID is provided, filter by rider
        if ($riderId) {
            $query->where('rider_id', $riderId);
        }

        $installmentsToUpdate = $query->get();

        if ($installmentsToUpdate->isEmpty()) {
            return 0; // No installments to update
        }

        $updatedCount = 0;

        foreach ($installmentsToUpdate as $installment) {
            try {
                DB::beginTransaction();

                // Mark installment as paid
                $installment->status = visa_installment_plan::STATUS_PAID;
                $installment->updated_by = auth()->user()->id ?? 1; // Default to admin if no auth user
                $installment->save();

                // Update related voucher status if needed
                $voucher = Vouchers::where('ref_id', $installment->id)
                    ->where('voucher_type', 'VL')
                    ->first();

                if ($voucher) {
                    // You might want to add a status field to vouchers table
                    // $voucher->status = 'paid';
                    $voucher->remarks = ($voucher->remarks ?? '') . ' - Auto-paid on ' . $today;
                    $voucher->save();
                }

                DB::commit();
                $updatedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error auto-marking installment as paid: ' . $e->getMessage(), [
                    'installment_id' => $installment->id,
                    'rider_id' => $installment->rider_id
                ]);
            }
        }

        return $updatedCount;
    }

    /**
     * Check and auto-mark installments for a specific rider (silent operation)
     */
    private function checkAndAutoMarkInstallments($riderId)
    {
        $updatedCount = $this->autoMarkInstallmentsAsPaid($riderId);

        // Silent operation - no flash messages to user
        // Only log for admin/debugging purposes
        if ($updatedCount > 0) {
            \Log::info("Auto-marked {$updatedCount} installment(s) as paid for rider {$riderId}");
        }

        return $updatedCount;
    }

    /**
     * Recalculate installment amounts when one is edited
     * This method ensures the total amount is preserved and remaining installments are adjusted
     */
    public function recalculateInstallments(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('visaexpense_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rider_id' => 'required|exists:accounts,id',
            'edited_installment_id' => 'required|exists:visa_installment_plans,id',
            'new_amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Get all pending installments for this rider
            $installments = visa_installment_plan::where('rider_id', $validated['rider_id'])
                ->where('status', visa_installment_plan::STATUS_PENDING)
                ->orderBy('id', 'asc')
                ->get();

            if ($installments->isEmpty()) {
                Flash::error('No pending installments found for this rider.');
                return redirect()->back();
            }

            // Get the total amount from the first installment (all should have the same total_amount)
            $totalAmount = $installments->first()->total_amount;
            if (!$totalAmount) {
                Flash::error('Total amount not found. Please recreate the installment plan.');
                return redirect()->back();
            }

            // Find the edited installment
            $editedInstallment = $installments->where('id', $validated['edited_installment_id'])->first();
            if (!$editedInstallment) {
                Flash::error('Installment not found.');
                return redirect()->back();
            }

            // Step 1: Calculate remaining balance
            $remainingBalance = $totalAmount - $validated['new_amount'];

            // Step 2: Get remaining installments (excluding the edited one)
            $remainingInstallments = $installments->where('id', '!=', $validated['edited_installment_id']);

            if ($remainingInstallments->isEmpty()) {
                Flash::error('No remaining installments to adjust.');
                return redirect()->back();
            }

            // Step 3: Distribute balance equally among remaining installments
            $amountPerInstallment = $remainingBalance / $remainingInstallments->count();

            // Step 4: Apply rounding - round all but the last installment
            $roundedAmount = floor($amountPerInstallment * 100) / 100; // Round down to 2 decimal places
            $lastInstallment = $remainingInstallments->last();

            // Update all remaining installments
            foreach ($remainingInstallments as $index => $installment) {
                if ($installment->id === $lastInstallment->id) {
                    // Last installment gets the remaining balance to handle rounding
                    $usedAmount = $validated['new_amount'] + ($roundedAmount * ($remainingInstallments->count() - 1));
                    $lastAmount = $totalAmount - $usedAmount;
                    $installment->amount = $lastAmount;
                } else {
                    $installment->amount = $roundedAmount;
                }
                $installment->updated_by = auth()->user()->id;
                $installment->save();

                // Update related voucher
                $voucher = Vouchers::where('ref_id', $installment->id)
                    ->where('voucher_type', 'VL')
                    ->first();
                if ($voucher) {
                    $voucher->amount = $installment->amount;
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }

                // Update related transactions
                $transactions = Transactions::where('reference_id', $installment->id)
                    ->where('reference_type', 'VL')
                    ->get();
                foreach ($transactions as $transaction) {
                    if ($transaction->credit > 0) {
                        $transaction->credit = $installment->amount;
                    } else {
                        $transaction->debit = $installment->amount;
                    }
                    $transaction->updated_at = now();
                    $transaction->save();
                }
            }

            // Update the edited installment
            $editedInstallment->amount = $validated['new_amount'];
            $editedInstallment->updated_by = auth()->user()->id;
            $editedInstallment->save();

            // Update related voucher for edited installment
            $voucher = Vouchers::where('ref_id', $editedInstallment->id)
                ->where('voucher_type', 'VL')
                ->first();
            if ($voucher) {
                $voucher->amount = $editedInstallment->amount;
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }

            // Update related transactions for edited installment
            $transactions = Transactions::where('reference_id', $editedInstallment->id)
                ->where('reference_type', 'VL')
                ->get();
            foreach ($transactions as $transaction) {
                if ($transaction->credit > 0) {
                    $transaction->credit = $editedInstallment->amount;
                } else {
                    $transaction->debit = $editedInstallment->amount;
                }
                $transaction->updated_at = now();
                $transaction->save();
            }

            DB::commit();

            Flash::success('Installment amounts recalculated successfully. Total amount preserved: ' . number_format($totalAmount, 2));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error recalculating installments: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
