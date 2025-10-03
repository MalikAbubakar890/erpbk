<div id="rows-container" style="width: 98%;">
  <?php if(isset($data)): ?>
  <?php
   
  ?>
      <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="row">
          <div class="form-group col-md-3">
              <label for="exampleInputEmail1">Select Account</label>
              <?php echo Form::select('account_id[]', $accounts, $entry->account_id??null, ['class' => 'form-control  form-select select2 ']); ?>

          </div>
          <div class="form-group col-md-4">
              <label>Narration</label>
              <textarea name="narration[]"  class="form-control " rows="10" placeholder="Narration" style="height: 40px !important;"><?php echo e($entry->narration); ?></textarea>
          </div>
          <div class="form-group col-md-2">
              <label>Dr Amount</label>
              <input type="number" step="any" name="dr_amount[]" value="<?php echo e($entry->debit); ?>" class="form-control  dr_amount" onchange="getTotal();" placeholder="Paid Amount">
          </div>
          <div class="form-group col-md-2">
              <label>Cr Amount</label>
              <input type="number" step="any" name="cr_amount[]" value="<?php echo e($entry->credit); ?>" class="form-control  cr_amount" onchange="getTotal();" placeholder="Paid Amount">
          </div>
          <div class="form-group col-md-1 d-flex align-items-end">
            <a href="javascript:void(0);" class="text-danger btn-remove-row"><i class="fa fa-trash"></i></a>
        </div>
         
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php else: ?>

    <div class="row">
        <div class="form-group col-md-3">
            <label for="exampleInputEmail1">Select Account</label>
            <?php echo Form::select('account_id[]', $accounts, null, ['class' => 'form-select form-select-sm select2']); ?>

        </div>
        <div class="form-group col-md-4">
            <label>Narration</label>
            <textarea name="narration[]" class="form-control" rows="10" placeholder="Narration" style="height: 40px !important;"></textarea>
        </div>
        <div class="form-group col-md-2">
            <label>Dr Amount</label>
            <input type="number" step="any" name="dr_amount[]" class="form-control dr_amount" placeholder="Paid Amount" onchange="getTotal();">
        </div>
        <div class="form-group col-md-2">
            <label>Cr Amount</label>
            <input type="number" step="any" name="cr_amount[]" class="form-control cr_amount" placeholder="Paid Amount" onchange="getTotal();">
        </div>
        <div class="form-group col-md-1 d-flex align-items-end">
            <a href="javascript:void(0);" class="text-danger btn-remove-row"><i class="fa fa-trash"></i></a>
        </div>
    </div>


  <?php endif; ?>

  </div>

  <button type="button" id="add-new-row" class="btn btn-success btn-sm mt-3 mb-3">Add New</button>
<?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/vouchers/default_fields.blade.php ENDPATH**/ ?>