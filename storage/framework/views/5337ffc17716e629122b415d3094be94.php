<?php $__env->startSection('title','Settings'); ?>

<?php $__env->startSection('content'); ?>

<div class="card">

    <div class="card-header">
    <h4 class="card-title">Application Settings</h4>
    </div>
    <div class="card-body">

            <?php echo Form::open(['route'=>'settings','method'=>'post']); ?>

            <?php echo csrf_field(); ?>
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="">Company Name</label>
                    <div class="input-group ">
                    <input type="text" name="settings[company_name]" class="form-control" value="<?php echo e($settings['company_name']??''); ?>" />
                   
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="">Email</label>
                  <div class="input-group ">
                  <input type="text" name="settings[company_email]" class="form-control" value="<?php echo e($settings['company_email']??''); ?>" />
                 
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="">Phone</label>
                  <div class="input-group ">
                  <input type="text" name="settings[company_phone]" class="form-control" value="<?php echo e($settings['company_phone']??''); ?>" />
                 
                  </div>
                </div>

                <div class="col-md-8 mb-3">
                  <label class="">Address</label>
                  <div class="input-group ">
                  <input type="text" name="settings[company_address]" class="form-control" value="<?php echo e($settings['company_address']??''); ?>" />
                 
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="">VAT Number</label>
                  <div class="input-group ">
                  <input type="text" name="settings[vat_number]" class="form-control" value="<?php echo e($settings['vat_number']??''); ?>" />
                 
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="">VAT Percentage</label>
                  <div class="input-group ">
                  <input type="number" step="any" name="settings[vat_percentage]" class="form-control" value="<?php echo e($settings['vat_percentage']??''); ?>" />
                  <div class="input-group-text">%</div>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="">RTA Admin Fee</label>
                  <div class="input-group ">
                  <input type="number" step="any" name="settings[rta_admin_fee]" class="form-control" value="<?php echo e($settings['rta_admin_fee']??''); ?>" />
                  <div class="input-group-text">AED</div>
                  </div>
                </div>

            </div>
            <div class="card-footer" >
              <button type="submit" class="btn btn-primary" style="float:right;">Save Settings</button>
              </div>
            <?php echo Form::close(); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/content/settings.blade.php ENDPATH**/ ?>