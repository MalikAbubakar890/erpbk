<input type="hidden" name="payment_from" value="{{\App\Helpers\HeadAccount::ADVANCE_LOAN}}" />
<input type="hidden" name="voucher_type" value="AL" />
<input type="hidden" name="trans_date" value="{{ date('Y-m-d') }}" />
<input type="hidden" name="billing_month" value="{{ date('Y-m-01') }}" />

@php
$rider_account = \App\Models\Accounts::where('ref_id', $rider->id)->where('account_type', 'Liability')->first();
if (!$rider_account) {
// Fallback: try to find any account for this rider
$rider_account = \App\Models\Accounts::where('ref_id', $rider->id)->first();
}
@endphp
<div class="row">
    <div class="form-group col-md-3">
        <label for="exampleInputEmail1">Select Account</label>
        <input type="hidden" name="account_id[]" value="{{ $rider_account->id ?? '' }}" />
        {!! Form::select('account_id[]', $accounts, $rider_account->id ?? null, ['class' => 'form-select form-select-sm select2' , 'disabled' => true]) !!}
    </div>
    <div class="form-group col-md-4">
        <label>Narration</label>
        <textarea name="narration[]" class="form-control" rows="10" placeholder="Advance Loan Received" style="height: 40px !important;">Advance Loan Received</textarea>
    </div>
    <div class="form-group col-md-2">
        <label>Amount</label>
        <input type="number" step="any" name="dr_amount[]" class="form-control dr_amount" placeholder="Loan Amount" onchange="getTotal();" required>
    </div>
    {{-- <div class="form-group col-md-2">
            <label>Cr Amount</label>
            <input type="number" step="any" name="cr_amount[]" class="form-control cr_amount" placeholder="Paid Amount" onchange="getTotal();">
        </div> --}}
    <!-- <div class="form-group col-md-1 d-flex align-items-end">
        <a href="javascript:void(0);" class="text-danger btn-remove-row"><i class="fa fa-trash"></i></a>
    </div> -->
</div>
<div id="rows-container" class="mb-3" style="width: 100%;">
    @isset($data)
    @foreach($data as $entry)
    <div class="row">
        <div class="form-group col-md-3">
            <label for="exampleInputEmail1">Select Account</label>
            {!! Form::select('account_id[]', $accounts, $entry->account_id??null, ['class' => 'form-control form-select select2 ']) !!}
        </div>
        <div class="form-group col-md-4">
            <label>Narration</label>
            <textarea name="narration[]" class="form-control " rows="10" placeholder="Narration" style="height: 40px !important;">{{$entry->narration}}</textarea>
        </div>
        <div class="form-group col-md-2">
            <label>Amount</label>
            <input type="number" step="any" name="dr_amount[]" value="{{$entry->debit}}" class="form-control  dr_amount" onchange="getTotal();" placeholder="Paid Amount">
        </div>

        {{-- <div class="form-group col-md-1 d-flex align-items-end">
            <a href="javascript:void(0);" class="text-danger btn-remove-row"><i class="fa fa-trash"></i></a>
        </div> --}}
        {{-- <div class="form-group col-md-1">
              <label style="visibility: hidden">plus</label>
              <button type="button" class="btn btn-primary btn-xs new_line"><i class="fa fa-plus"></i> </button>
          </div> --}}
    </div>
    @endforeach
    @else
    <!-- Second row for credit account (Advance Loan account) -->
    <div class="row">
        <div class="form-group col-md-3">
            <label for="exampleInputEmail1">Select Account</label>
            <input type="hidden" name="account_id[]" value="{{ \App\Helpers\HeadAccount::ADVANCE_LOAN }}" />
            {!! Form::select('account_id[]', $accounts, \App\Helpers\HeadAccount::ADVANCE_LOAN, ['class' => 'form-select form-select-sm select2', 'disabled' => true]) !!}
        </div>
        <div class="form-group col-md-4">
            <label>Narration</label>
            <textarea name="narration[]" class="form-control" rows="10" placeholder="Advance Loan Given" style="height: 40px !important;">Advance Loan Given to {{ $rider->name }}</textarea>
        </div>
        <div class="form-group col-md-2">
            <label>Amount</label>
            <input type="number" step="any" name="dr_amount[]" class="form-control dr_amount" placeholder="Loan Amount" onchange="getTotal();" required readonly>
        </div>
    </div>
    @endisset
</div>