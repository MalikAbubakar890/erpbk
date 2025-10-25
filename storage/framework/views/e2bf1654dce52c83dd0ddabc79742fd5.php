

            <?php echo Form::open(['route' => 'items.store','id'=>'formajax']); ?>


            <div class="card-body">

                <div class="row">
                    <?php echo $__env->make('items.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

            </div>

            <div class="action-btn">
              <button type="button" class="btn btn-default"  data-bs-dismiss="modal">Cancel</button>
                <?php echo Form::submit('Save', ['class' => 'btn btn-primary']); ?>

            </div>

            <?php echo Form::close(); ?>



<?php /**PATH /home2/sxjnqpte/public_html/resources/views/items/create.blade.php ENDPATH**/ ?>