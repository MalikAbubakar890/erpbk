<!-- Name Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('name', 'Name:',['class'=>'required']); ?>

    <?php echo Form::text('name', null, ['class' => 'form-control','maxlength' => 255, 'required']); ?>

</div>

<!-- Contact Number Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('contact_number', 'Contact Number:',['class'=>'required']); ?>

  <?php echo Form::text('contact_number', null, ['class' => 'form-control', 'maxlength' => 100, 'required']); ?>

</div>
<!-- Company Name Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('company_name', 'Company Name:'); ?>

    <?php echo Form::text('company_name', null, ['class' => 'form-control', 'maxlength' => 255]); ?>

</div>

<!-- Company Email Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('company_email', 'Company Email:'); ?>

    <?php echo Form::text('company_email', null, ['class' => 'form-control', 'maxlength' => 100]); ?>

</div>


<!-- Address Field -->
<div class="form-group col-sm-12">
    <?php echo Form::label('address', 'Address:'); ?>

    <?php echo Form::text('address', null, ['class' => 'form-control', 'maxlength' => 200]); ?>

</div>

<!-- Tax Number Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('tax_number', 'Tax Number:',['class'=>'required']); ?>

    <?php echo Form::text('tax_number', null, ['class' => 'form-control', 'maxlength' => 100, 'required']); ?>

</div>


<!-- Tax Percentage Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('tax_percentage', 'Tax Percentage:',['class'=>'required']); ?>

    <?php echo Form::number('tax_percentage', null, ['class' => 'form-control','step'=>'any', 'required']); ?>

</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mt-3">
  <label>Status</label>
  <div class="form-check">
    <input type="hidden" name="status" value="2"/>
     <input type="checkbox" name="status" id="status" class="form-check-input" value="1" <?php if(isset($customers)): ?> <?php if($customers->status == 1): ?> checked <?php endif; ?> <?php else: ?> checked  <?php endif; ?>/>
     <label for="status" class="pt-0">Is Active</label>

  </div>
</div>
<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/customers/fields.blade.php ENDPATH**/ ?>