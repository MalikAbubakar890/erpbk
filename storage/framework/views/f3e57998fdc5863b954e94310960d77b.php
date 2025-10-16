

            <?php echo Form::model($dropdowns, ['route' => ['dropdowns.update', $dropdowns->id], 'method' => 'patch','id'=>'formajax']); ?>


            <div class="card-body">
                <div class="row">
                    <?php echo $__env->make('dropdowns.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <div class="action-btn pt-3">
              <button type="button" class="btn btn-default"  data-bs-dismiss="modal">Cancel</button>
                <?php echo Form::submit('Save', ['class' => 'btn btn-primary']); ?>


            </div>

            <?php echo Form::close(); ?>

<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/dropdowns/edit.blade.php ENDPATH**/ ?>