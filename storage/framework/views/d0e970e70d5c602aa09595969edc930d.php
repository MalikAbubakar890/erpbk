<?php $__env->startSection('page_content'); ?>

<?php echo Form::open(['route' => 'riders.store','id'=>'formajax']); ?>

<input type="hidden" id="redirect_url" value="<?php echo e(route('riders.index')); ?>" />
<div class="card-body">

    <div class="row">
        <?php echo $__env->make('riders.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

</div>
<div class="card-footer bg-light border-top">
    <div class="d-flex justify-content-end gap-3">
        <a href="<?php echo e(route('riders.index')); ?>" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">Save Information</button>
    </div>
</div>

<?php echo Form::close(); ?>


</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('riders.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk1\resources\views/riders/create.blade.php ENDPATH**/ ?>