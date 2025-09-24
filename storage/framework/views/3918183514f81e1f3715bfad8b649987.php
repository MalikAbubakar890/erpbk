<script src="<?php echo e(asset('js/modal_custom.js')); ?>"></script>

<!-- Account Type Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('account_type', 'Account Type:'); ?>

  <?php echo Form::select('account_type', App\Helpers\Accounts::AccountTypes(),null, ['class' => 'form-control form-select select2']); ?>

</div>
<div class="form-group col-sm-6"></div>
<!-- Account Name Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('name', 'Account Name:'); ?>

  <?php echo Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 100, 'maxlength' => 100]); ?>

</div>
<div class="form-group col-sm-6"></div>
<!-- Account Code Field -->
<?php if(Route::currentRouteName() == 'accounts.edit' && isset($accounts->id)): ?>
<div class="form-group col-sm-6">
  <?php echo Form::label('account_code', 'Account Code:'); ?>

  <?php echo Form::text('account_code', $accounts->account_code, ['class' => 'form-control']); ?>

</div>
<?php endif; ?>

<!-- Parent Account Id Field -->
<div class="form-group col-sm-8">
  <?php echo Form::label('parent_id', 'Parent Account:'); ?>

  <select name="parent_id" class="form-control form-select select2">
    <option value="">Select</option>
    <?php echo App\Helpers\Accounts::dropdown($parents,$accounts->parent_id??null); ?>

  </select>
  
</div>

<!-- Opening Balance Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('opening_balance', 'Opening Balance:'); ?>

  <?php echo Form::number('opening_balance', null, ['class' => 'form-control','step'=>'any']); ?>

</div>

<div class="form-group col-sm-6"></div>
<!-- Status Field -->
<div class="form-group col-sm-6">
  <label>Status</label>
  <div class="form-check">
    <input type="hidden" name="status" value="2" />
    <input type="checkbox" name="status" id="status" class="form-check-input" value="1" <?php if(isset($accounts)): ?> <?php if($accounts->status == 1): ?> checked <?php endif; ?> <?php else: ?> checked <?php endif; ?>/>
    <label for="status" class="pt-0">Is Active</label>

  </div>
</div>

<div class="form-group col-sm-12">
  <?php echo Form::label('notes', 'Notes:'); ?>

  <?php echo Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 4]); ?>


</div>
<?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/accounts/fields.blade.php ENDPATH**/ ?>