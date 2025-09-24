<?php echo Form::open(['route' => ['riders.update', $rider->id], 'method' => 'PUT', 'id'=>'formajax']); ?>

<input type="hidden" name="rider_id" value="<?php echo e($rider->id); ?>" />
<div class="card-body">
    <div class="row">
        <?php echo $__env->make('riders.itemsfields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<div class="card-footer bg-light border-top">
    <div class="d-flex justify-content-end gap-3">
        <button type="submit" class="btn btn-primary px-4">Save Information</button>
    </div>
</div>
<?php echo Form::close(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/additems.blade.php ENDPATH**/ ?>