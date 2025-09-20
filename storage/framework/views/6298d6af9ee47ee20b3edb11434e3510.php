<?php $__env->startPush('third_party_stylesheets'); ?>
<?php $__env->stopPush(); ?>
<table class="table table-striped dataTable no-footer" id="dataTableBuilder">
   <thead class="text-center">
      <tr role="row">
         <th title="Rider ID" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-sort="descending" aria-label="Rider ID: activate to sort column ascending">Rider ID</th>
         <th title="Name" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending">Name</th>
         <th title="Contact" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Contact: activate to sort column ascending">Contact</th>
         <th title="Fleet Supv" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Fleet Supv: activate to sort column ascending">Fleet Supv</th>
         <th title="Hub" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Hub: activate to sort column ascending">Hub</th>
         <th title="Customer" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Customer: activate to sort column ascending">Customer</th>
         <th title="Desig" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Desig: activate to sort column ascending">Desig</th>
         <th title="Bike" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Bike">Bike</th>
         <th title="Status" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
         <th title="Shift" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Shift: activate to sort column ascending">Shift</th>
         <th title="ATTN" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="ATTN: activate to sort column ascending">ATTN</th>
         <th title="Orders" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Orders: activate to sort column ascending">Orders</th>
         <th title="Days" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Days: activate to sort column ascending">Days</th>
         <th title="Balance" class="sorting" tabindex="0" rowspan="1" colspan="1" aria-label="Balance: activate to sort column ascending">Balance</th>
         <th title="Actions" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Actions">Actions</th>
         <th tabindex="0" rowspan="1" colspan="1" aria-sort="descending">
            <a class="openFilterSidebar" href="javascript:void(0);"> <i class="fa fa-search"></i></a>
         </th>
         <th tabindex="0" rowspan="1" colspan="1" aria-sort="descending">
            <a class="openColumnControlSidebar" href="javascript:void(0);" title="Column Control"> <i class="fa fa-columns"></i></a>
         </th>
      </tr>
   </thead>
   <tbody>
      <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="text-center">
         <td><?php echo e($r->rider_id); ?></td>
         <td class="text-start"><a href="<?php echo e(route('riders.show', $r->id)); ?>"><?php echo e($r->name); ?></a><br /></td>
         <?php
         $phone = preg_replace('/[^0-9]/', '', $r->company_contact);

         // Agar already +971 se start hai
         if (strpos($phone, '971') === 0) {
         $whatsappNumber = '+' . $phone; // WhatsApp link ke liye
         $displayNumber = '0' . substr($phone, 3); // Show ke liye 05... style
         } else {
         // Agar +971 nahi diya, to default UAE code add karo
         $whatsappNumber = '+971' . ltrim($phone, '0');
         $displayNumber = '0' . ltrim($phone, '0');
         }
         ?>

         <td>
            <?php if($r->company_contact): ?>
            <a href="https://wa.me/<?php echo e($whatsappNumber); ?>" target="_blank" class="text-success">
               <?php echo e($displayNumber); ?>

            </a>
            <?php else: ?>
            N/A
            <?php endif; ?>
         </td>
         <td><?php echo e($r->fleet_supervisor); ?></td>
         <td><?php echo e($r->emirate_hub); ?></td>
         <td><?php echo e(DB::table('customers')->where('id' , $r->customer_id)->first()->name ?? '-'); ?></td>
         <td><?php echo e($r->designation); ?></td>
         <?php
         $bike = DB::table('bikes')->where('rider_id', $r->id)->first();
         ?>
         <td><?php echo e($bike ? $bike->plate : '-'); ?></td>
         <?php
         $hasActiveBike = DB::table('bikes')
         ->where('rider_id', $r->id)
         ->where('warehouse', 'Active')
         ->exists();
         $isActive = $hasActiveBike;
         $badgeClass = $isActive ? 'bg-label-success' : 'bg-label-danger';
         ?>
         <td>
            <span class="badge <?php echo e($badgeClass); ?>">
               <?php if($isActive): ?>Active
               <?php else: ?> Inactive
               <?php endif; ?>
            </span>
         </td>
         <td><?php echo e($r->shift); ?></td>
         <?php
         $rider = DB::Table('riders')->find($r->id);
         $timeline = DB::Table('job_status')->select('id')->where('RID', $r->id)->whereDate('created_at', '=', $r->attendance_date)->first();
         $emails = DB::Table('rider_emails')->select('id')->where('rider_id', $r->id)->whereDate('created_at', '=', $r->attendance_date)->first();
         ?>
         <td>
            <?php if($timeline): ?>
            <a href="<?php echo e(route('rider.timeline')); ?>/<?php echo e($rider->id); ?>"><span class="text-danger cursor-pointer" title="Timeline Added">●</span></a>&nbsp;
            <?php endif; ?>
            <?php if($emails): ?>
            <a href="<?php echo e(route('rider.emails')); ?>/<?php echo e($rider->id); ?>"><span class="text-success cursor-pointer" title="Email Sent">●</span></a>&nbsp;
            <?php endif; ?>
            <a href="javascript:void(0);" data-action="<?php echo e(url('riders/job_status', $rider->id)); ?>" data-size="md" data-title="Add Timeline" class="show-modal">
               <?php echo e($r->attendance); ?>

            </a>
         </td>
         <?php
         $rider_sum = DB::table('rider_activities')
         ->where('d_rider_id', $r->rider_id)
         ->whereMonth('date', now()->month)
         ->whereYear('date', now()->year)
         ->sum('delivered_orders');
         ?>
         <td><?php echo e($rider_sum ? $rider_sum : '-'); ?></td>

         <?php
         $days = DB::table('rider_activities')
         ->where('d_rider_id', $r->rider_id)
         ->whereMonth('date', now()->month)
         ->whereYear('date', now()->year)
         ->count('date');
         ?>
         <td><?php echo e($days ? $days : '-'); ?></td>
         <?php
         $balance = App\Helpers\Accounts::getBalance($r->account_id);
         ?>
         <td><?php echo e($balance ? $balance : '-'); ?></td>
         <td style="position: relative;">
            <div class="dropdown">
               <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1 waves-effect" type="button" id="actiondropdown_<?php echo e($r->id); ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="visibility: visible !important; display: inline-block !important;">
                  <i class="icon-base ti ti-dots icon-md text-body-secondary"></i>
               </button>
               <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actiondropdown_<?php echo e($r->id); ?>" style="z-index: 1050;">
                  <a href="javascript:void();" data-action="<?php echo e(route('rider_contract_upload', $r->id)); ?>" data-size="md"
                     data-title="<?php echo e($r->name); ?> (<?php echo e($r->rider_id); ?>) Contract" class="dropdown-item waves-effect show-modal"><i class="fas fa-file my-1"></i> Contract</a>
                  <a href="javascript:void();" data-action="<?php echo e(route('rider.sendemail', $r->id)); ?>" data-size="md"
                     data-title="<?php echo e($r->name); ?> (<?php echo e($r->rider_id); ?>)" class="dropdown-item waves-effect show-modal"><i class="fas fa-envelope my-1"></i> Send Email</a>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rider_edit')): ?>
                  <a href="<?php echo e(route('riders.edit', $r->id)); ?>" class='dropdown-item waves-effect'>
                     <i class="fa fa-edit my-1"></i> Edit
                  </a>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rider_delete')): ?>
                  <a href="<?php echo e(route('rider.delete', $r->id)); ?>" class='dropdown-item waves-effect'>
                     <i class="fa fa-trash my-1"></i> Delete
                  </a>
                  <?php endif; ?>
               </div>
            </div>
         </td>
         <td></td>
         <td></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
   </tbody>
</table>
<?php if(method_exists($data, 'links')): ?>
<?php echo $data->links('components.global-pagination'); ?>

<?php endif; ?>

<!-- Filter modal removed: using right-side sliding sidebar instead --><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/table.blade.php ENDPATH**/ ?>