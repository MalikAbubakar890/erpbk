<!-- Name Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('name', 'Bank Name:'); ?>

    <?php echo Form::text('name', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>
<!-- Branch Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('branch', 'Branch:'); ?>

  <?php echo Form::text('branch', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>
<!-- Account Type Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('account_type', 'Account Type:'); ?>

  <?php echo Form::text('account_type', null, ['class' => 'form-control', 'maxlength' => 100, 'maxlength' => 100]); ?>

</div>
<!-- Title Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('title', 'Account Title:'); ?>

    <?php echo Form::text('title', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>

<!-- Account No Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('account_no', 'Account No:'); ?>

    <?php echo Form::text('account_no', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>

<!-- Iban Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('iban', 'IBAN:'); ?>

    <?php echo Form::text('iban', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>

<!-- Swift Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('swift', 'Swift:'); ?>

    <?php echo Form::text('swift', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>



<!-- Balance Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('balance', 'Opening Balance:'); ?>

    <?php echo Form::number('balance', null, ['class' => 'form-control']); ?>

</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mt-3">
  <label>Status</label>
  <div class="form-check">
    <input type="hidden" name="status" value="2"/>
     <input type="checkbox" name="status" id="status" class="form-check-input" value="1" <?php if(isset($banks)): ?> <?php if($banks->status == 1): ?> checked <?php endif; ?> <?php else: ?> checked  <?php endif; ?>/>
     <label for="status" class="pt-0">Is Active</label>

  </div>
</div>

<!-- Notes Field -->
<div class="form-group col-sm-12">
    <?php echo Form::label('notes', 'Notes:'); ?>

    <?php echo Form::textarea('notes', null, ['class' => 'form-control','rows'=>3]); ?>

</div>
<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/banks/fields.blade.php ENDPATH**/ ?>