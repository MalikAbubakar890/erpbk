
            <?php echo Form::open(['route' => 'permissions.store','id' => 'formajax']); ?>


          
                    <?php echo $__env->make('permissions.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             

            <div class="action-btn">
                <?php echo Form::submit('Save', ['class' => 'btn btn-primary']); ?>

            </div>

            <?php echo Form::close(); ?>

<?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/permissions/create.blade.php ENDPATH**/ ?>