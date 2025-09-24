

            <?php echo Form::open(['route' => 'accounts.store','id'=>'formajax']); ?>

            <input type="hidden" id="reload_page" value="1"/>
            <div class="card-body">

                <div class="row">
                    <?php echo $__env->make('accounts.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

            </div>

            <div class="action-btn">
              <button type="button" class="btn btn-default"  data-bs-dismiss="modal">Cancel</button>
                <?php echo Form::submit('Save', ['class' => 'btn btn-primary']); ?>

            </div>

            <?php echo Form::close(); ?>

<?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/accounts/create.blade.php ENDPATH**/ ?>