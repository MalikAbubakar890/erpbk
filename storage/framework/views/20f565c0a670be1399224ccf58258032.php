<?php $__env->startPush('third_party_stylesheets'); ?>
<?php $__env->stopPush(); ?>
<table class="table table-striped dataTable no-footer" id="dataTableBuilder">
   <thead class="text-center">
      <tr role="row">
         <th title="Inv Date" class="sorting" rowspan="1" colspan="1" >Inv Date</th>
         <th title="Inv Id" class="sorting" rowspan="1" colspan="1" >Inv Id</th>
         <th title="Billing Month" class="sorting" rowspan="1" colspan="1" >Billing Month</th>
         <th title="Supplier" class="sorting" rowspan="1" colspan="1" >Supplier</th>
         <th title="Descriptions" class="sorting" rowspan="1" colspan="1" >Descriptions</th>
         <th title="Total Amount" class="sorting" rowspan="1" colspan="1" >Total Amount</th>
         <th title="Action" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Action"><a data-bs-toggle="modal" data-bs-target="#searchModal" href="javascript:void(0);" > <i class="fa fa-search"></i></a></th>
         <th tabindex="0" rowspan="1" colspan="1" aria-sort="descending">
            <a data-bs-toggle="modal" data-bs-target="#customoizecolmn" href="javascript:void(0);" > <i class="fa fa-filter"></i></a> 
         </th>
      </tr>
   </thead>
   <tbody>
      <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="text-center">
         <td><?php echo e($r->inv_date); ?></td>
         <td><?php echo e($r->inv_id); ?></td>
         <td><?php echo e($r->billing_month ? \Carbon\Carbon::parse($r->expiry_date)->format('M Y') : '-'); ?></td>
            <?php
                $supplier = DB::table('suppliers')->where('id', $r->supplier_id)->first();
            ?>
            <td>
                <?php if($supplier): ?>
                    <?php echo e($supplier->name); ?>

                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
         <td><?php echo e($r->descriptions ?? 'N/A'); ?></td>
         <td><?php echo e($r->total_amount ?? 'N/A'); ?></td>
         <td>
            <div class='btn-group'>
               <a href="<?php echo e(route('supplierInvoices.show', $r->id)); ?>"  class='btn btn-default btn-sm' target="_blank">
               <i class="fa fa-eye"></i>
               </a> 
               <a href="javascript:void(0);" data-title="Edit Invoice" data-size="xl" data-action="<?php echo e(route('supplierInvoices.edit', $r->id)); ?>" class='btn btn-info btn-sm show-modal'>
               <i class="fa fa-edit"></i>
               </a>
               <a href="javascript:void(0);"  onclick='confirmDelete("<?php echo e(route('supplierInvoices.delete', $r->id)); ?>")' class='btn btn-danger btn-sm confirm-modal' data-size="lg" data-title="Delete Invoice">
               <i class="fa fa-trash"></i>
               </a>
            </div>
         </td>
         <td></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
   </tbody>
</table>
<?php if(method_exists($data, 'links')): ?>
    <?php echo $data->links('components.global-pagination'); ?>

<?php endif; ?>
<div class="modal modal-default filtetmodal fade" id="customoizecolmn" tabindex="-1" data-bs-backdrop="static"role="dialog" aria-hidden="true">
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
</div><?php /**PATH /home2/sxjnqpte/public_html/resources/views/supplier_invoices/table.blade.php ENDPATH**/ ?>