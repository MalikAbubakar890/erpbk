<?php $__env->startSection('page_content'); ?>
<div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="ti ti-notes ti-sm me-1_5 me-2" style="background: #28c76f45;color: #28c76f;"></i><b>Items & Prices</b></div>
        <div class="small text-muted">Inline add / edit / delete</div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 45%">Item</th>
                        <th style="width: 25%">Price</th>
                        <th style="width: 30%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-row="new">
                        <td>
                            <select class="form-select" id="new_item_id">
                                <option value="">Select Item</option>
                                <?php $__currentLoopData = \App\Models\Items::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($it->id); ?>"><?php echo e($it->name.' - '.$it->price); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" id="new_price" step="any" placeholder="0.00">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success" id="btn-add-row">Save</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="btn-clear-row">Clear</button>
                        </td>
                    </tr>

                    <?php if(isset($rider['items'])): ?>
                    <?php $__currentLoopData = $rider['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $riderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $it = \App\Models\Items::find($riderItem->item_id);
                    ?>
                    <?php if($it): ?>
                    <tr data-row="existing" data-rip-id="<?php echo e($riderItem->id); ?>">
                        <td>
                            <select class="form-select item-select">
                                <option value="">Select Item</option>
                                <?php $__currentLoopData = \App\Models\Items::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($opt->id); ?>" <?php echo e($opt->id == $it->id ? 'selected' : ''); ?>><?php echo e($opt->name.' - '.$opt->price); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control item-price" step="any" value="<?php echo e($riderItem->price); ?>">
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary btn-save-existing">Save</button>
                                <form action="<?php echo e(route('riders.deleteitem', ['rider_id' => $rider->id, 'item_id' => $riderItem->id])); ?>" method="POST" onsubmit="return confirm('Delete this item?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function() {
        var csrf = '<?php echo e(csrf_token()); ?>';
        var riderId = {
            {
                $rider - > id
            }
        };

        function alertMsg(msg) {
            window.alert(msg);
        }

        // Add new row
        document.getElementById('btn-add-row').addEventListener('click', function() {
            var itemId = document.getElementById('new_item_id').value;
            var price = document.getElementById('new_price').value;

            if (!itemId) {
                return alertMsg('Please select an item');
            }
            if (!price || parseFloat(price) < 0) {
                return alertMsg('Please enter a valid price');
            }

            fetch(`/riders/${riderId}/additem`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    price: price
                })
            }).then(r => r.json()).then(function(res) {
                if (!res.success) {
                    return alertMsg(res.message || 'Error adding');
                }
                location.reload();
            }).catch(function() {
                alertMsg('Error adding');
            });
        });

        document.getElementById('btn-clear-row').addEventListener('click', function() {
            document.getElementById('new_item_id').value = '';
            document.getElementById('new_price').value = '';
        });

        // Save existing row
        document.querySelectorAll('.btn-save-existing').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tr = this.closest('tr');
                var ripId = tr.getAttribute('data-rip-id');
                var itemId = tr.querySelector('.item-select').value;
                var price = tr.querySelector('.item-price').value;

                if (!itemId) {
                    return alertMsg('Please select an item');
                }
                if (!price || parseFloat(price) < 0) {
                    return alertMsg('Please enter a valid price');
                }

                fetch(`/riders/${riderId}/updateitem/${ripId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        price: price
                    })
                }).then(r => r.json()).then(function(res) {
                    if (!res.success) {
                        return alertMsg(res.message || 'Error saving');
                    }
                    location.reload();
                }).catch(function() {
                    alertMsg('Error saving');
                });
            });
        });
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('riders.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/items.blade.php ENDPATH**/ ?>