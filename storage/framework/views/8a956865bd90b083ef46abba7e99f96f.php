<div class="alert alert-info">
    <h5><i class="icon fas fa-info"></i> Important Notes:</h5>
    <ul>
        <li>This will only update existing unpaid invoices for the same Rider ID and Billing Month.</li>
        <li>If no matching unpaid invoice is found, the record will be skipped.</li>
        <li>The Excel file should include a bank account column for creating voucher entries.</li>
        <li>Make sure the Excel format matches the expected structure.</li>
    </ul>
</div>

<form action="<?php echo e(route('riderInvoices.importPaid')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label for="file">Select Excel File:</label>

        <input type="file" class="form-control-file form-control mb-3 <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            id="file" name="file" accept=".xlsx">
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

    <div class="form-group">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-upload"></i> Import Paid Invoices
        </button>
        <a href="<?php echo e(route('riderInvoices.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>
</form><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/rider_invoices/import_paid.blade.php ENDPATH**/ ?>