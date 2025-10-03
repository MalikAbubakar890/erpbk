
<?php $__env->startSection('title','Import Rider Vouchers'); ?>
<?php $__env->startSection('content'); ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Import Rider Vouchers</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('riders.import_rider_vouchers')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="alert alert-info">
                        Expected columns (order):
                        1) Rider ID, 2) Billing Month, 3) Date, 4) Amount, 5) Voucher Type, 6) Account_id
                    </div>
                    <div class="form-group">
                        <label for="file">Excel File (.xlsx)</label>
                        <input type="file" name="file" id="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".xlsx">
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import</button>
                    <a href="<?php echo e(route('riders.index')); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/import_rider_voucher.blade.php ENDPATH**/ ?>