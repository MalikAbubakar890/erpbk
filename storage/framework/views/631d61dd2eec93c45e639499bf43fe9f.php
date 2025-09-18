<script src="<?php echo e(asset('js/modal_custom.js')); ?>"></script>
<div class="form-group col-sm-6">
    <?php echo Form::label('date', 'Date:' , ['class' => 'required']); ?>

    <?php echo Form::date('date', $visaExpenses->date ?? 'null', ['class' => 'form-control', 'required']); ?>

</div>
<div class="form-group col-sm-6">
    <label class="">Visa Status:</label>
    <select class="form select select2" id="visa_status" name="visa_status" required>
        <option value=""></option>
        <option value="Job offer Letter" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Job offer Letter') ? 'selected' : ''); ?>>Job offer Letter</option>
        <option value="Labor Insurance" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Labor Insurance') ? 'selected' : ''); ?>>Labor Insurance</option>
        <option value="Work Permit" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Work Permit') ? 'selected' : ''); ?>>Work Permit</option>
        <option value="Work Man Insurance" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Work Man Insurance') ? 'selected' : ''); ?>>Work Man Insurance</option>
        <option value="Entry Permit (Inside)" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Entry Permit (Inside)') ? 'selected' : ''); ?>>Entry Permit (Inside)</option>
        <option value="Entry Permit (Outside)" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Entry Permit (Outside)') ? 'selected' : ''); ?>>Entry Permit (Outside)</option>
        <option value="Status Change" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Status Change') ? 'selected' : ''); ?>>Status Change</option>
        <option value="Medical" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Tawjeeh') ? 'selected' : ''); ?>>Tawjeeh</option>
        <option value="Medical" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Medical') ? 'selected' : ''); ?>>Medical</option>
        <option value="Emirates ID + Residency" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Emirates ID + Residency') ? 'selected' : ''); ?>>Emirates ID + Residency</option>
        <option value="Emirates ID" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Emirates ID') ? 'selected' : ''); ?>>Emirates ID</option>
        <option value="Residency" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Residency') ? 'selected' : ''); ?>>Residency</option>
        <option value="Bike License" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Bike License') ? 'selected' : ''); ?>>Bike License</option>
        <option value="Violation" <?php echo e((isset($visaExpenses) && $visaExpenses->visa_status == 'Violation') ? 'selected' : ''); ?>>Violation</option>
    </select>
</div>
<div class="form-group col-sm-6">
    <?php echo Form::label('amount', 'Amount:', ['class' => 'required']); ?>

    <?php echo Form::number('amount', $visaExpenses->amount ?? '', ['class' => 'form-control','step'=>'any', 'required']); ?>

</div>
<div class="form-group col-sm-6">
    <?php echo Form::label('billing_month', 'Billing Month:', ['class' => 'required']); ?>

    <?php echo Form::month('billing_month', isset($visaExpenses) && $visaExpenses->billing_month ? \Carbon\Carbon::parse($visaExpenses->billing_month)->format('Y-m') : null, ['class' => 'form-control' , 'required']); ?>

</div>
<div class="form-group col-sm-12">
    <?php echo Form::label('detail', 'Detail:', ['class' => 'required']); ?>

    <?php echo Form::textarea('detail', $visaExpenses->detail ?? '', ['class' => 'form-control', 'maxlength' => 500,'rows'=>3, 'required']); ?>

</div><?php /**PATH D:\xammp1\htdocs\erpbk1\resources\views/visa_expenses/fields.blade.php ENDPATH**/ ?>