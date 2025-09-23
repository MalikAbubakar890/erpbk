
<?php $__env->startSection('page_content'); ?>
<div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="ti ti-notes ti-sm me-1_5 me-2" style=" background: #28c76f45;color: #28c76f;"></i><b>Items & Prices</b></div>
        <a href="javascript:void(0);" class="btn btn-sm btn-primary show-modal" data-action="<?php echo e(route('riders.additems', $rider->id)); ?>" data-size="lg" data-title="Add Item">Add Item</a>
    </div>
    <div class="card-body">
        <div class="row border">
            <table class="table border" style="border-radius:10px;">
                <thead>
                    <tr class="">
                        <th>Items</th>
                        <th>Price</th>
                    </tr>
                </thead>
            </table>
            <table id="myTable" class="table order-list2 border">
                <?php if(isset($rider['items'])): ?>
                <?php $__currentLoopData = $rider['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $riderItemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $item = \App\Models\Items::find($riderItemId->item_id);
                ?>
                <?php if($item): ?>
                <td width="250"><label><?php echo e(@$item->name); ?></label></td>
                <td width="240"><?php echo e(@$riderItemId->price); ?></td>
                <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </table>


        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('riders.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/items.blade.php ENDPATH**/ ?>