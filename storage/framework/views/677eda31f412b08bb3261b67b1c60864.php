<!-- Price Assignment Section -->
<div class="row pr-5 pl-5">
    <label>
        <h5>Assign Price</h5>
    </label>
    <span id="error_message_duplicate_id"></span>
    <div id="rows-container">
        <?php
        $counter = 1;
        $sum = 1;
        ?>
        <?php if(isset($riders['items']) && count($riders['items'])>0): ?>
        <?php $resultItems = $riders['items']; ?>
        <?php $__currentLoopData = $resultItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $sum = count($riders['items']); ?>
        <div class="row mb-3 item-row">
            <div class="col-sm-4">
                <label>Select Items</label>
                <select value="0" name="items[id][]" class="form-select select2" required>
                    <option value="0">Select Item</option>
                    <?php $__currentLoopData = \App\Models\Items::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>" <?php if(isset($rowItem->item_id) && $rowItem->item_id == $item->id): ?> selected <?php endif; ?>>
                        <?php echo e($item->name.' - '.$item->price); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <span class="notification" style="font-size: 13px;color:red"></span>
            </div>
            <div class="col-sm-4">
                <label>Price</label>
                <input type="number" class="form-control" step="any" value="<?php if(isset($rowItem)): ?><?php echo e($rowItem->price); ?><?php endif; ?>" name="items[price][]" placeholder="Items Price" required />
            </div>
            <div class="col-sm-2">
                <label>&nbsp;</label><br>
                <button type="button" class="btn btn-danger btn-remove-row"><i class="fa fa-trash"></i></button>
            </div>
        </div>
        <?php $counter++; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        <div class="row mb-3 item-row">
            <div class="col-sm-4">
                <label>Select Items</label>
                <select value="0" name="items[id][]" class="form-select select2" required>
                    <option value="0">Select Item</option>
                    <?php $__currentLoopData = \App\Models\Items::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->name.' - '.$item->price); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <span class="notification" style="font-size: 13px;color:red"></span>
            </div>
            <div class="col-sm-4">
                <label>Price</label>
                <input type="number" class="form-control" step="any" name="items[price][]" placeholder="Items Price" required />
            </div>
            <div class="col-sm-2">
                <label>&nbsp;</label><br>
                <button type="button" class="btn btn-danger btn-remove-row"><i class="fa fa-trash"></i></button>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <button type="button" class="btn btn-success btn-sm mt-3 mb-3 col-sm-2" id="add-new-row">
        <i class="fa fa-plus"></i> Add Row
    </button>
</div>

<script>
    $(document).ready(function() {
        // Add new row
        $('#add-new-row').click(function() {
            var newRow = $('#rows-container .item-row:first').clone();
            newRow.find('select').val('0');
            newRow.find('input[type="number"]').val('');
            newRow.find('.notification').text('');
            $('#rows-container').append(newRow);
            initializeSelect2(newRow.find('.select2'));
        });

        // Remove row
        $(document).on('click', '.btn-remove-row', function() {
            if ($('#rows-container .item-row').length > 1) {
                $(this).closest('.item-row').remove();
            } else {
                alert('At least one item row is required.');
            }
        });

        // Initialize Select2 for existing and new dropdowns
        function initializeSelect2(element) {
            element.select2({
                width: '100%'
            });
        }

        // Initialize Select2 for existing dropdowns
        initializeSelect2($('.select2'));
    });
</script><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/itemsfields.blade.php ENDPATH**/ ?>