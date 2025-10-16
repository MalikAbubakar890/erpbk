<?php $__env->startPush('third_party_stylesheets'); ?>
<?php $__env->stopPush(); ?>
<table class="table table-striped dataTable no-footer" id="dataTableBuilder">
   <thead class="text-center">
      <tr role="row">
         <th title="Code" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-sort="descending" aria-label="Code: activate to sort column ascending">Code</th>
         <th title="Plate" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Plate: activate to sort column ascending">Plate</th>
         <th title="Rider ID" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Rider ID: activate to sort column ascending">Rider ID</th>
         <th title="Rider Name" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Rider Name: activate to sort column ascending">Rider Name</th>
         <th title="Emirates" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Emirates: activate to sort column ascending">Emirates</th>
         <th title="Company" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Company: activate to sort column ascending">Company</th>
         <th title="Project" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Project: activate to sort column ascending">Project</th>
         <th title="Expiry" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Expiry: activate to sort column ascending">Expiry</th>
         <th title="Created By" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Customer: activate to sort column ascending">Created By</th>
         <th title="Updated By" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Customer: activate to sort column ascending">Updated By</th>
         <th title="Action" width="120px" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Action"><a data-bs-toggle="modal" data-bs-target="#searchModal" href="javascript:void(0);"> <i class="fa fa-search"></i></a></th>
         <th tabindex="0" rowspan="1" colspan="1" aria-sort="descending">
            <a data-bs-toggle="modal" data-bs-target="#customoizecolmn" href="javascript:void(0);"> <i class="fa fa-filter"></i></a>
         </th>
      </tr>
   </thead>
   <tbody>
      <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="text-center">
         <td><?php echo e($r->bike_code); ?></td>
         <td><a href="<?php echo e(route('bikes.show', $r->id)); ?>"><?php echo e($r->plate); ?></a></td>
         <?php
         $rider = DB::table('riders')->where('id', $r->rider_id)->first();
         ?>
         <td><?php echo e($rider->rider_id ?? '-'); ?></td>
         <td>
            <?php if($rider): ?>
            <a href="<?php echo e(route('riders.show', $rider->id)); ?>"><?php echo e($rider->name); ?></a>
            <?php else: ?>
            -
            <?php endif; ?>
         </td>
         <td><?php echo e($r->emirates); ?></td>
         <?php
         $company = DB::Table('leasing_companies')->where('id' , $r->company)->first();
         ?>
         <td><?php echo e($company ? $company->name : '-'); ?></td>
         <td><?php echo e(DB::table('customers')->where('id' , $r->customer_id)->first()->name ?? '-'); ?></td>
         <td><?php echo e($r->expiry_date ? \Carbon\Carbon::parse($r->expiry_date)->format('d M Y') : '-'); ?></td>
         <td><?php echo e($r->created_by ? \App\Models\User::find($r->created_by)->name : '-'); ?></td>
         <td><?php echo e($r->updated_by ? \App\Models\User::find($r->updated_by)->name : '-'); ?></td>
         <td>
            <div class="dropdown">
               <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1 waves-effect" type="button" id="actiondropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="icon-base ti ti-dots icon-md text-body-secondary"></i>
               </button>
               <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actiondropdown" style="">
                  <a href="<?php echo e(route('bikes.show', $r->id)); ?>" class='dropdown-item waves-effect'>
                     <i class="fa fa-eye"></i>Show Bike
                  </a>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('item_edit')): ?>
                  <a href="<?php echo e(route('bikes.edit', $r->id)); ?>" class='dropdown-item waves-effect'>
                     <i class="fa fa-edit"></i>Edit
                  </a>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('item_delete')): ?>
                  <a href="javascript:void(0);" onclick='confirmDelete("<?php echo e(route('bikes.delete', $r->id)); ?>")' class='dropdown-item waves-effect'>
                     <i class="fa fa-trash mx-1"></i> Delete
                  </a>
                  <?php endif; ?>
               </div>
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
</div><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/bikes/table.blade.php ENDPATH**/ ?>