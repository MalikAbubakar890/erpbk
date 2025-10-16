<?php echo Form::open(['route' => 'receipts.store','id'=>'formajax', 'enctype' => 'multipart/form-data']); ?>


<div class="card-body">

    <div class="row">
        <?php echo $__env->make('receipts.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

</div>

<div class="action-btn">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
    <?php echo Form::submit('Save', ['class' => 'btn btn-primary']); ?>

</div>

<?php echo Form::close(); ?><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/receipts/create.blade.php ENDPATH**/ ?>