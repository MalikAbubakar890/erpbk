<?php
// Map voucher type to form action route
$actions = [
'AL' => route('riders.storeadvanceloan'),
'COD' => route('riders.storecod'),
'PN' => route('riders.storepenalty'),
'PAY' => route('riders.storepayment'),
'VC' => route('riders.storevendorcharges'),
];
// Map voucher type to field-rendering endpoint (with rider id)
$urls = [
'AL' => route('riders.advanceloan', ['id' => $rider->id ?? 0]),
'COD' => route('riders.cod', ['id' => $rider->id ?? 0]),
'PN' => route('riders.penalty', ['id' => $rider->id ?? 0]),
'PAY' => route('riders.payment', ['id' => $rider->id ?? 0]),
'VC' => route('riders.vendorcharges', ['id' => $rider->id ?? 0]),
];
?>

<div class="mb-3">
    <label class="form-label">Voucher Type</label>
    <select id="voucherType" class="form-select form-select-sm">
        <option value="">Select</option>
        <?php $__currentLoopData = $voucherTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($code); ?>"><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <small class="text-muted">Incentive is separate and not included here.</small>
    <input type="hidden" id="reload_page" value="1">
    <input type="hidden" id="rider_id" value="<?php echo e($rider->id ?? ''); ?>">
    <input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>">
</div>

<div id="voucherFormContainer"
    data-actions-b64="<?php echo e(base64_encode(json_encode($actions))); ?>"
    data-urls-b64="<?php echo e(base64_encode(json_encode($urls))); ?>">
</div>

<!-- Templates removed; forms will be loaded via AJAX when a type is selected. -->

<script>
    (function() {
        const container = document.getElementById('voucherFormContainer');
        const actionsB64 = container.getAttribute('data-actions-b64') || '';
        const urlsB64 = container.getAttribute('data-urls-b64') || '';
        const typeToAction = actionsB64 ? JSON.parse(atob(actionsB64)) : {};
        const typeToUrl = urlsB64 ? JSON.parse(atob(urlsB64)) : {};
        const riderId = document.getElementById('rider_id').value;
        const typeSelect = document.getElementById('voucherType');

        function loadFormFor(type) {
            if (!type) {
                container.innerHTML = '';
                return;
            }
            // Load the specific form via AJAX from existing endpoints
            const url = typeToUrl[type] || '';
            if (!url) {
                container.innerHTML = '';
                return;
            }

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;

                    const originalForm = temp.querySelector('form#formajax') || temp.querySelector('form');
                    const inner = originalForm ? originalForm.innerHTML : temp.innerHTML;

                    const action = typeToAction[type] || '#';
                    container.innerHTML = '<form id="formajax" method="post" action="' + action + '"></form>';
                    const form = container.querySelector('#formajax');
                    form.innerHTML = inner;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '<?php echo e(csrf_token()); ?>';
                    form.prepend(csrf);

                    if (typeof window.getTotal === 'function') {
                        window.getTotal();
                    }
                })
                .catch(() => {
                    container.innerHTML = '<div class="alert alert-danger">Failed to load form.</div>';
                });
        }

        typeSelect.addEventListener('change', function() {
            loadFormFor(this.value);
        });
    })();
</script><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/voucher-modal.blade.php ENDPATH**/ ?>