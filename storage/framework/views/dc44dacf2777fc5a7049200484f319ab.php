<script src="<?php echo e(asset('js/modal_custom.js')); ?>"></script>

<!-- Name Field -->
<div class="form-group col-sm-12">
    <?php echo Form::label('name', 'Name:'); ?>

    <?php echo Form::text('name', null, ['class' => 'form-control', 'maxlength' => 255, 'maxlength' => 255]); ?>

</div>

<div class="form-group col-sm-6">
    <?php echo Form::label('customer_id', 'Customer:'); ?>

<?php echo Form::select('customer_id', App\Models\Customers::dropdown(), null, ['class' => 'form-control select2']); ?>

</div>
<div class="form-group col-sm-6">
    <?php echo Form::label('supplier_id', 'Supplier:'); ?>

<?php echo Form::select('supplier_id', App\Models\Supplier::dropdown(), null, ['class' => 'form-control select2']); ?>

</div>
<!-- Price Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('price', 'Price:'); ?>

    <?php echo Form::number('price', null, ['class' => 'form-control','step'=>'any']); ?>

</div>

<!-- Cost Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('cost', 'Cost:'); ?>

    <?php echo Form::number('cost', null, ['class' => 'form-control','step'=>'any']); ?>

</div>
<!-- Cost Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('code', 'Code:'); ?>

  <?php echo Form::text('code', null, ['class' => 'form-control']); ?>

</div>
<!-- Cost Field -->
<div class="form-group col-sm-6">
  <?php echo Form::label('barcode', 'Barcode:'); ?>

  <?php echo Form::text('barcode', null, ['class' => 'form-control']); ?>

</div>

<!-- Vat Field -->
<div class="form-group col-sm-6">
    <?php echo Form::label('vat', 'VAT(%):'); ?>

    <?php echo Form::number('vat', null, ['class' => 'form-control','step'=>'any']); ?>

</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mt-3">
  <label>Status</label>
  <div class="form-check">
    <input type="hidden" name="status" value="2"/>
     <input type="checkbox" name="status" id="status" class="form-check-input" value="1" <?php if(isset($items)): ?> <?php if($items->status == 1): ?> checked <?php endif; ?> <?php else: ?> checked  <?php endif; ?>/>
     <label for="status" class="pt-0">Is Active</label>

  </div>
</div>

<!-- Detail Field -->
<div class="form-group col-sm-12">
  <?php echo Form::label('detail', 'Detail:'); ?>

  <?php echo Form::textarea('detail', null, ['class' => 'form-control','rows'=>3]); ?>

</div>
<?php /**PATH /home2/sxjnqpte/public_html/resources/views/items/fields.blade.php ENDPATH**/ ?>