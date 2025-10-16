<?php $__env->startPush('third_party_stylesheets'); ?>
<?php $__env->stopPush(); ?>
<table class="table table-striped dataTable no-footer" id="dataTableBuilder">
    <thead class="text-center">
        <tr role="row">
            <th title="Transaction Number" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-sort="descending" aria-label="Transaction Number: activate to sort column ascending">Transaction Number</th>
            <th title="Sender" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Sender: activate to sort column ascending">Sender</th>
            <th title="Bank" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Bank: activate to sort column ascending">Bank</th>
            <th title="Amount" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Amount: activate to sort column ascending">Amount</th>
            <th title="Date of Receipt" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Date of Receipt: activate to sort column ascending">Date of Receipt</th>
            <th title="Billing Month" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Billing Month: activate to sort column ascending">Billing Month</th>
            <th title="Description" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Description: activate to sort column ascending">Description</th>
            <th title="Status" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
            <th title="Action" width="120px" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Action"><a data-bs-toggle="modal" data-bs-target="#searchModal" href="javascript:void(0);"> <i class="fa fa-search"></i></a></th>
            <th tabindex="0" rowspan="1" colspan="1" aria-sort="descending">
                <a data-bs-toggle="modal" data-bs-target="#customoizecolmn" href="javascript:void(0);"> <i class="fa fa-filter"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($receipt->transaction_number); ?></td>
            <td>
                <?php
                $account = $receipt->account_id ? \App\Models\Accounts::find($receipt->account_id) : null;
                ?>
                <?php echo e($account ? $account->name : '-'); ?>

            </td>
            <td>
                <?php
                $bank = $receipt->bank_id ? \App\Models\Banks::find($receipt->bank_id) : null;
                ?>
                <?php echo e($bank ? $bank->name : '-'); ?>

            </td>
            <td>AED <?php echo e(number_format($receipt->amount, 2)); ?></td>
            <td><?php echo e($receipt->date_of_receipt); ?></td>
            <td><?php echo e($receipt->billing_month); ?></td>
            <td><?php echo e($receipt->description); ?></td>
            <td>
                <?php if($receipt->status == 1): ?>
                <span class="badge bg-success">Active</span>
                <?php else: ?>
                <span class="badge bg-secondary">Inactive</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="<?php echo e(route('receipts.show', $receipt->id)); ?>" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>
                <a href="javascript:void(0);" class="btn btn-sm btn-warning show-modal" data-title="Update" data-size="lg" data-action="<?php echo e(route('receipts.edit', $receipt->id)); ?>"><i class="fa fa-pencil"></i></a>
            </td>
            <td></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php if(method_exists($data, 'links')): ?>
    <?php echo $data->links('components.global-pagination'); ?>

<?php endif; ?>
<div class="modal modal-default filtetmodal fade" id="customoizecolmn" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-top modal-full-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Riders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="searchTopbody">
                <div style="display: none;" class="loading-overlay" id="loading-overlay">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <form id="filterForm" action="<?php echo e(route('banks.index')); ?>" method="GET">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input type="number" name="search" class="form-control" placeholder="Search">
                        </div>
                        <div class="col-md-12 form-group text-center">
                            <button type="submit" class="btn btn-primary pull-right mt-3"><i class="fa fa-filter mx-2"></i> Filter Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/receipts/table.blade.php ENDPATH**/ ?>