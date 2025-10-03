<form action="<?php echo e(route('riders.voucher_import')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label for="payment_from">Credit Account</label>
        <select name="payment_from" id="payment_from" class="form-control <?php $__errorArgs = ['payment_from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">Select</option>
            <?php $__currentLoopData = $bank_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e(old('payment_from') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['payment_from'];
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
    <div class="form-group">
        <label for="file">Select Excel File (.xlsx)</label>
        <input type="file" class="form-control-file form-control mb-3 <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="file" id="file" accept=".xlsx">
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
        <small class="form-text text-muted">Expected columns: Rider ID, Billing Month, Date, Amount, Voucher Type, Account_id</small>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import Vouchers</button>
        <a href="<?php echo e(route('riders.index')); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/import_vouchers.blade.php ENDPATH**/ ?>