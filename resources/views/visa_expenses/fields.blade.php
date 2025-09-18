<script src="{{ asset('js/modal_custom.js') }}"></script>
<div class="form-group col-sm-6">
    {!! Form::label('date', 'Date:' , ['class' => 'required']) !!}
    {!! Form::date('date', $visaExpenses->date ?? 'null', ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group col-sm-6">
    <label class="">Visa Status:</label>
    <select class="form select select2" id="visa_status" name="visa_status" required>
        <option value=""></option>
        <option value="Job offer Letter" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Job offer Letter') ? 'selected' : '' }}>Job offer Letter</option>
        <option value="Labor Insurance" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Labor Insurance') ? 'selected' : '' }}>Labor Insurance</option>
        <option value="Work Permit" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Work Permit') ? 'selected' : '' }}>Work Permit</option>
        <option value="Work Man Insurance" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Work Man Insurance') ? 'selected' : '' }}>Work Man Insurance</option>
        <option value="Entry Permit (Inside)" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Entry Permit (Inside)') ? 'selected' : '' }}>Entry Permit (Inside)</option>
        <option value="Entry Permit (Outside)" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Entry Permit (Outside)') ? 'selected' : '' }}>Entry Permit (Outside)</option>
        <option value="Status Change" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Status Change') ? 'selected' : '' }}>Status Change</option>
        <option value="Medical" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Tawjeeh') ? 'selected' : '' }}>Tawjeeh</option>
        <option value="Medical" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Medical') ? 'selected' : '' }}>Medical</option>
        <option value="Emirates ID + Residency" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Emirates ID + Residency') ? 'selected' : '' }}>Emirates ID + Residency</option>
        <option value="Emirates ID" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Emirates ID') ? 'selected' : '' }}>Emirates ID</option>
        <option value="Residency" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Residency') ? 'selected' : '' }}>Residency</option>
        <option value="Bike License" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Bike License') ? 'selected' : '' }}>Bike License</option>
        <option value="Violation" {{ (isset($visaExpenses) && $visaExpenses->visa_status == 'Violation') ? 'selected' : '' }}>Violation</option>
    </select>
</div>
<div class="form-group col-sm-6">
    {!! Form::label('amount', 'Amount:', ['class' => 'required']) !!}
    {!! Form::number('amount', $visaExpenses->amount ?? '', ['class' => 'form-control','step'=>'any', 'required']) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('billing_month', 'Billing Month:', ['class' => 'required']) !!}
    {!! Form::month('billing_month', isset($visaExpenses) && $visaExpenses->billing_month ? \Carbon\Carbon::parse($visaExpenses->billing_month)->format('Y-m') : null, ['class' => 'form-control' , 'required']) !!}
</div>
<div class="form-group col-sm-12">
    {!! Form::label('detail', 'Detail:', ['class' => 'required']) !!}
    {!! Form::textarea('detail', $visaExpenses->detail ?? '', ['class' => 'form-control', 'maxlength' => 500,'rows'=>3, 'required']) !!}
</div>