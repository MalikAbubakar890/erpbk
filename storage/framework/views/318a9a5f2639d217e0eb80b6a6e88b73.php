<?php $__env->startSection('title', 'Rider Profile'); ?>

<?php $__env->startSection('content'); ?>
<style>
  .myform .required:after {
    content: " *";
    color: red;
    font-weight: 200;
  }

  @media print {
    body .content {
      font-size: 18px !important;
    }
  }

  /* Status Cards Styling */
  .status-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 16px;
    min-width: 180px;
    flex: 1;
    max-width: 220px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  .status-card.active {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-color: #28a745;
    color: white;
  }

  .absconder-card.active {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    border-color: #dc3545;
  }

  .flowup-card.active {
    background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);
    border-color: #007bff;
  }

  .llicense-card.active {
    background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
    border-color: #17a2b8;
  }

  .status-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  .status-card:hover::before {
    left: 100%;
  }

  .status-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    margin-bottom: 12px;
    font-size: 20px;
  }

  .status-card.active .status-icon {
    background: rgba(255, 255, 255, 0.3);
  }

  .status-content {
    margin-bottom: 12px;
  }

  .status-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #495057;
  }

  .status-card.active .status-title {
    color: white;
  }

  .status-subtitle {
    font-size: 12px;
    color: #6c757d;
    font-weight: 500;
  }

  .status-card.active .status-subtitle {
    color: rgba(255, 255, 255, 0.9);
  }

  .status-toggle {
    display: flex;
    align-items: center;
  }

  .status-checkbox {
    display: none;
  }

  .toggle-switch {
    position: relative;
    width: 50px;
    height: 24px;
    background: #ccc;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .toggle-slider {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .status-checkbox:checked+.toggle-switch {
    background: #28a745;
  }

  .status-checkbox:checked+.toggle-switch .toggle-slider {
    transform: translateX(26px);
  }

  .absconder-card .status-checkbox:checked+.toggle-switch {
    background: #dc3545;
  }

  .flowup-card .status-checkbox:checked+.toggle-switch {
    background: #007bff;
  }

  .llicense-card .status-checkbox:checked+.toggle-switch {
    background: #17a2b8;
  }

  /* Loading state */
  .status-card.loading {
    opacity: 0.7;
    pointer-events: none;
  }

  .status-card.loading .toggle-switch {
    animation: pulse 1s infinite;
  }

  @keyframes pulse {
    0% {
      opacity: 1;
    }

    50% {
      opacity: 0.5;
    }

    100% {
      opacity: 1;
    }
  }

  /* Responsive design */
  @media (max-width: 768px) {
    .status-card {
      min-width: 150px;
      max-width: 180px;
      padding: 12px;
      flex: 1;
    }

    .status-title {
      font-size: 14px;
    }

    .status-subtitle {
      font-size: 11px;
    }
  }

  @media (max-width: 576px) {
    .status-card {
      min-width: 140px;
      max-width: 160px;
      padding: 10px;
    }

    .status-title {
      font-size: 13px;
    }

    .status-subtitle {
      font-size: 10px;
    }

    .status-icon {
      width: 35px;
      height: 35px;
      font-size: 18px;
    }
  }
</style>
<?php
if((request()->segment(2)) == 'generatentries' || request()->segment(2) == 'installmentPlan'){
$account_id = DB::table('accounts')->where('id', request()->segment(3))->first();
if(is_numeric(request()->segment(3))){
session()->put('rider_id',$account_id->ref_id);
$riders = App\Models\Riders::where('id', $account_id->ref_id)->first();
}
}else{
if(is_numeric(request()->segment(3))){
session()->put('rider_id',request()->segment(3));
$riders = App\Models\Riders::find(request()->segment(3));
}
}
if(isset($riders)){
$result = $riders->toArray();
}
if(isset($result)){
$account = App\Models\Accounts::where('ref_id', $result['id'])->where('account_type', 'expense')->first();
}

?>
<div class="row" style="">
  <div class="col-xl-3 col-md-3 col-lg-5 order-1 order-md-0">
    <!-- User Card -->
    <div class="card mb-6" style="border-radius: 25px 25px 0px 0px;">
      <div class="card-header p-0" style="border-radius: 25px 25px 0px 0px;height: 291px;position: relative;background-image: url(http://127.0.0.1:8000/assets/img/user_back.jpg);background-size: cover;">
        <?php if(isset($result)): ?>
        <div class="profile-img">
          <?php
          if(@$result['image_name']){
          $image_name = url('storage2/profile/'.$result['image_name']);//Storage::url('app/profile/'.$result['image_name']);
          }else{
          $image_name = asset('uploads/default.png');
          }
          ?>
          <img src="<?php echo e($image_name); ?>" id="output" width="270" class="profile-user-img img-fluid" />
        </div>
        <?php endif; ?>
      </div>
      <div class="card-body pt-12">
        <div class="user-avatar-section">
          <div class=" d-flex align-items-center flex-column">
            <div class="col-md-12 mt-2">
              <div class="d-flex align-items-baseline">
                <div class="user-info" style="width: 100%;">
                  <h6>
                    <b>
                      <?php if(isset($result)): ?>
                      <?php echo e(\Illuminate\Support\Str::limit($result['rider_id'] ?? 'not-set', 25)); ?> - <?php echo e(\Illuminate\Support\Str::limit($result['name'] ?? 'not-set', 25)); ?>

                      <?php endif; ?>
                    </b>

                  </h6>
                  <div class="mt-2" style="width: 100%;">
                    <span class="badge bg-label-primary"><?php if(isset($result)): ?><?php echo e($result['designation']??'not-set'); ?><?php endif; ?></span>
                  </div>
                </div>
                <div class="text-end" style="width: 14%;">
                  <i class="ti ti-edit ti-sm"
                    style="border: 2px solid #9593997a !important; border-radius: 24px; padding: 8px; cursor: pointer;"
                    id="edit-icon">
                  </i>
                </div>
              </div>
            </div>
            <div id="photo-upload-form" class="mt-4" style="display: none;">
              <?php if(isset($result)): ?>
              <form action="<?php echo e(url('riders/picture_upload/'.$result['id'])); ?>" method="POST" enctype="multipart/form-data" id="formajax2">
                <?php endif; ?>
                <?php echo csrf_field(); ?>
                <?php if(isset($result)): ?>
                <div class="button-wrapper">
                  <label for="upload" class="btn btn-default me-2 mb-3 mt-3" tabindex="0">
                    <span class="d-none d-sm-block">Change Photo</span>
                    <i class="ti ti-upload d-block d-sm-none"></i>
                    <input type="file" id="upload" name="image_name" class="account-file-input " hidden accept="image/png, image/jpeg" onchange="loadFile(event)" />
                  </label>
                  <button type="submit" class="btn btn-primary">Upload</button>
                </div>
                <?php endif; ?>
              </form>
            </div>
          </div>
        </div>
        <div class="info-container mt-3">
          <h3>Basic Information</h3>
          <ul class="list-unstyled mb-6">
            <script>
              var loadFile = function(event) {
                var image = document.getElementById("output");
                image.src = URL.createObjectURL(event.target.files[0]);
              };
            </script>
            


            <ul class="p-0 mb-3">
              <li class="list-group-item pb-1 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-mail ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Email:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e($result['personal_email']??'not-set'); ?><?php endif; ?></b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-phone ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content mt-2">
                  <span>WhatsApp:</span><br>
                  <b class="float-right">

                    <?php if(isset($result['company_contact'])): ?>
                    <?php
                    $phone = preg_replace('/[^0-9]/', '', $result['company_contact']);
                    $whatsappNumber = '+971' . ltrim($phone, '0');
                    ?>
                    <a href="https://wa.me/<?php echo e($whatsappNumber); ?>"
                      target="_blank"
                      class="text-success">
                      <?php echo e($result['company_contact']); ?>

                    </a>
                    <?php else: ?>
                    N/A
                    <?php endif; ?>

                  </b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-flag ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Nationality:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e(DB::Table('countries')->where('id' , $result['nationality'])->first()->name ??'not-set'); ?><?php endif; ?></b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-cake ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Age:</span><br>
                  <b class="float-right">
                    <?php if(isset($result['dob'])): ?>
                    <?php echo e(\Carbon\Carbon::parse($result['dob'])->age); ?>

                    <?php else: ?>
                    not-set
                    <?php endif; ?>
                  </b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-user-check ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Status:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e(App\Helpers\General::RiderStatus($result['status'])??'not-set'); ?><?php endif; ?></b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-calendar-due ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Date Of Joining:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e(App\Helpers\General::DateFormat($result['doj'])??'not-set'); ?><?php endif; ?></b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-user-check ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Shift:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e($result['shift']??'not-set'); ?><?php endif; ?></b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-file-invoice ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Attendance:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e($result['attendance']??'not-set'); ?><?php endif; ?></b>
                </div>
              </li>
              <li class="list-group-item pb-1 mt-3 user_list d-flex align-items-center">
                <div class="icons me-2">
                  <i class="ti ti-cash-banknote ti-sm me-1_5"></i>
                </div>
                <div class="user_list_content">
                  <span>Balance:</span><br> <b class="float-right"><?php if(isset($result)): ?><?php echo e(App\Helpers\Accounts::getBalance($result['account_id'])); ?><?php endif; ?></b>
                </div>
              </li>
            </ul>
          </ul>
          <div class="d-flex justify-content-start mb-3">
            <?php if(isset($result)): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rider_edit')): ?>
            <a href="<?php echo e(route('riders.edit', $result['id'])); ?>" class="btn btn-outline-primary btn-sm waves-effect waves-light btn-block me-1"><i class="fa fa-edit"></i>&nbsp;Edit</a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('email_create')): ?>
            <a href="javascript:void();" data-action="<?php echo e(route('rider.sendemail', $result['id'])); ?>" data-size="md"
              data-title="<?php echo e($result['name'] . ' (' . $result['rider_id']); ?>')" class="btn btn-outline-warning btn-sm show-modal text-nowrap"><i class="fas fa-envelope"></i>&nbsp;Send Email</a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('timeline_create')): ?>
            <a href="javascript:void(0);" data-action="<?php echo e(url('riders/job_status/' . $result['id'])); ?>" data-size="md" data-title="Add Timeline" class="btn btn-outline-success btn-sm text-nowrap show-modal mx-1"><i class="fas fa-chart-bar"></i>&nbsp;Add Timeline</a>
            <?php endif; ?>
            <?php endif; ?>
            
          </div>
          <?php if(isset($result)): ?>
          <div class="d-flex flex-wrap justify-content-start gap-2 gap-md-3">
            <!-- Absconder Status Card -->
            <div class="status-card absconder-card <?php echo e(($result['absconder'] ?? 0) == 1 ? 'active' : ''); ?>"
              data-rider-id="<?php echo e($result['id'] ?? ''); ?>"
              data-type="absconder">
              <div class="d-flex justify-content-between">
                <div class="status-icon">
                  <i class="ti ti-user-x"></i>
                </div>
                <div class="status-content">
                  <div class="status-title">Absconder</div>
                  <div class="status-subtitle"><?php echo e(($result['absconder'] ?? 0) == 1 ? 'Marked as Absconder' : 'Not Absconder'); ?></div>
                </div>
              </div>
              <div class="status-toggle">
                <input type="checkbox"
                  class="status-checkbox absconder-checkbox"
                  id="absconder-<?php echo e($result['id'] ?? ''); ?>"
                  data-rider-id="<?php echo e($result['id'] ?? ''); ?>"
                  <?php echo e(($result['absconder'] ?? 0) == 1 ? 'checked' : ''); ?>>
                <label for="absconder-<?php echo e($result['id'] ?? ''); ?>" class="toggle-switch">
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            <!-- Follow Up Status Card -->
            <div class="status-card flowup-card <?php echo e(($result['flowup'] ?? 0) == 1 ? 'active' : ''); ?>"
              data-rider-id="<?php echo e($result['id'] ?? ''); ?>"
              data-type="flowup">
              <div class="d-flex justify-content-between">
                <div class="status-icon">
                  <i class="ti ti-bell"></i>
                </div>
                <div class="status-content">
                  <div class="status-title">Follow Up</div>
                  <div class="status-subtitle"><?php echo e(($result['flowup'] ?? 0) == 1 ? 'Follow Up Required' : 'No Follow Up'); ?></div>
                </div>
              </div>
              <div class="status-toggle">
                <input type="checkbox"
                  class="status-checkbox flowup-checkbox"
                  id="flowup-<?php echo e($result['id'] ?? ''); ?>"
                  data-rider-id="<?php echo e($result['id'] ?? ''); ?>"
                  <?php echo e(($result['flowup'] ?? 0) == 1 ? 'checked' : ''); ?>>
                <label for="flowup-<?php echo e($result['id'] ?? ''); ?>" class="toggle-switch">
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            <!-- Learning License Status Card -->
            <div class="status-card llicense-card <?php echo e(($result['l_license'] ?? 0) == 1 ? 'active' : ''); ?>"
              data-rider-id="<?php echo e($result['id'] ?? ''); ?>"
              data-type="llicense">
              <div class="d-flex justify-content-between">
                <div class="status-icon">
                  <i class="ti ti-certificate"></i>
                </div>
                <div class="status-content">
                  <div class="status-title">Learning License</div>
                  <div class="status-subtitle"><?php echo e(($result['l_license'] ?? 0) == 1 ? 'Learning License Required' : 'No Learning License'); ?></div>
                </div>
              </div>
              <div class="status-toggle">
                <input type="checkbox"
                  class="status-checkbox llicense-checkbox"
                  id="llicense-<?php echo e($result['id'] ?? ''); ?>"
                  data-rider-id="<?php echo e($result['id'] ?? ''); ?>"
                  <?php echo e(($result['l_license'] ?? 0) == 1 ? 'checked' : ''); ?>>
                <label for="llicense-<?php echo e($result['id'] ?? ''); ?>" class="toggle-switch">
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-9 col-md-9 col-lg-7 order-0 order-md-1 position-relative">
    <div class="nav-align-top mb-4" style="position: fixed; z-index: 1;">
      <div class="card" style="z-index: 1;">
        <div class="card-body p-3">
          <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-0 row-gap-2 justify-content-between">
            <li class="nav-item"><a class="nav-link <?php if(is_numeric(request()->segment(2)) || request()->segment(2) == 'create'): ?> active <?php endif; ?>" href="<?php if(isset($result['id'])): ?><?php echo e(route('riders.show',$result['id'])); ?><?php else: ?>#<?php endif; ?>"><i class="ti ti-user-check ti-sm me-1_5"></i>Information</a></li>
            <?php if(isset($result)): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('timeline_view')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'timeline'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.timeline',$result['id'])); ?>"><i class="ti ti-timeline ti-sm me-1_5"></i>Timeline</a></li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rider_document')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'files'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.files',$result['id'])); ?>"><i class="ti ti-file-upload ti-sm me-1_5"></i>Files</a></li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('riderinvoice_view')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'invoices'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.invoices',$result['id'])); ?>"><i class="ti ti-file-invoice ti-sm me-1_5"></i>Invoices</a></li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('visaexpense_view')): ?>
            <?php if($account): ?>
            <li class="nav-item">
              <a class="nav-link <?php if(request()->segment(2) == 'generatentries' || request()->segment(2) == 'installmentPlan'): ?> active <?php endif; ?>"
                href="<?php echo e(route('VisaExpense.generatentries', $account->id)); ?>">
                <i class="ti ti-file-invoice ti-sm me-1_5"></i>
                Visa Expense
              </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('item_view')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'items'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.items',$result['id'])); ?>"><i class="ti ti-cash-banknote ti-sm me-1"></i>Salary</a></li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gn_ledger')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'ledger'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.ledger',$result['id'])); ?>"><i class="ti ti-file ti-sm me-1_5"></i>Ledger</a></li>
            
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('activity_view')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'activities'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.activities',$result['id'])); ?>"><i class="ti ti-motorbike ti-sm me-1_5"></i>Activities</a></li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('email_view')): ?>
            <li class="nav-item"><a class="nav-link <?php if(request()->segment(2) == 'emails'): ?> active <?php endif; ?>" href="<?php echo e(route('rider.emails',$result['id'])); ?>"><i class="ti ti-mail ti-sm me-1_5"></i>Emails</a></li>
            <?php endif; ?>
            <li class="nav-item">
              <div class="dropdown">
                <button class="btn btn-outline-secondary rounded-pill p-2 waves-effect" type="button" id="actiondropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="ti ti-dots icon-md"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actiondropdown">
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('advanceloan_create')): ?>
                  <a href="javascript:void(0);" data-action="<?php echo e(route('riders.advanceloan', ['id' => $result['id'], 'vt' => 'AL'])); ?>" data-size="xl" data-title="Advance Loan" class='dropdown-item show-modal'>
                    Advance Loan
                  </a>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cod_create')): ?>
                  <a href="javascript:void(0);" data-action="<?php echo e(route('riders.cod' , $result['id'])); ?>" data-size="xl" data-title="COD" class='dropdown-item show-modal'>
                    COD
                  </a>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('penality_create')): ?>
                  <a href="javascript:void(0);" data-action="<?php echo e(route('riders.penalty' , $result['id'])); ?>" class='dropdown-item show-modal' data-size="xl" data-title="Penality">
                    Penality
                  </a>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('incentives_create')): ?>
                  <a href="javascript:void(0);" data-action="<?php echo e(route('riders.incentive' , $result['id'])); ?>" class='dropdown-item show-modal' data-size="xl" data-title="Incentive">
                    Incentive
                  </a>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment_create')): ?>
                  <a href="javascript:void(0);" data-action="<?php echo e(route('riders.payment' , $result['id'])); ?>" class='dropdown-item show-modal' data-size="xl" data-title="Payment">
                    Payment
                  </a>
                  <?php endif; ?>
                </div>
              </div>
            </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="card mb-5" id="cardBody" style="margin-top: 120px; height:1300px !important;overflow: auto;margin-top: 120px;">
      <?php echo $__env->yieldContent('page_content'); ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add change event listener to absconder checkbox
    document.querySelector('.absconder-checkbox').addEventListener('change', function() {
      const riderId = this.getAttribute('data-rider-id');
      const isChecked = this.checked;
      const card = this.closest('.status-card');
      const subtitle = card.querySelector('.status-subtitle');

      if (!riderId) {
        showNotification('Rider ID not found', 'error');
        return;
      }

      // Add loading state
      card.classList.add('loading');
      subtitle.textContent = 'Updating...';

      // Make AJAX request
      fetch(`/riders/toggle-absconder/${riderId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update card appearance
            updateCardStatus(card, 'absconder', isChecked);
            showNotification(data.message, 'success');
          } else {
            showNotification('Error: ' + data.message, 'error');
            // Revert checkbox state on error
            this.checked = !isChecked;
            updateCardStatus(card, 'absconder', !isChecked);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred while updating absconder status', 'error');
          // Revert checkbox state on error
          this.checked = !isChecked;
          updateCardStatus(card, 'absconder', !isChecked);
        })
        .finally(() => {
          // Remove loading state
          card.classList.remove('loading');
        });
    });

    // Add change event listener to flowup checkbox
    document.querySelector('.flowup-checkbox').addEventListener('change', function() {
      const riderId = this.getAttribute('data-rider-id');
      const isChecked = this.checked;
      const card = this.closest('.status-card');
      const subtitle = card.querySelector('.status-subtitle');

      if (!riderId) {
        showNotification('Rider ID not found', 'error');
        return;
      }

      // Add loading state
      card.classList.add('loading');
      subtitle.textContent = 'Updating...';

      // Make AJAX request
      fetch(`/riders/toggle-flowup/${riderId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update card appearance
            updateCardStatus(card, 'flowup', isChecked);
            showNotification(data.message, 'success');
          } else {
            showNotification('Error: ' + data.message, 'error');
            // Revert checkbox state on error
            this.checked = !isChecked;
            updateCardStatus(card, 'flowup', !isChecked);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred while updating flowup status', 'error');
          // Revert checkbox state on error
          this.checked = !isChecked;
          updateCardStatus(card, 'flowup', !isChecked);
        })
        .finally(() => {
          // Remove loading state
          card.classList.remove('loading');
        });
    });

    // Add change event listener to llicense checkbox
    document.querySelector('.llicense-checkbox').addEventListener('change', function() {
      const riderId = this.getAttribute('data-rider-id');
      const isChecked = this.checked;
      const card = this.closest('.status-card');
      const subtitle = card.querySelector('.status-subtitle');

      if (!riderId) {
        showNotification('Rider ID not found', 'error');
        return;
      }

      // Add loading state
      card.classList.add('loading');
      subtitle.textContent = 'Updating...';

      // Make AJAX request
      fetch(`/riders/toggle-llicense/${riderId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update card appearance
            updateCardStatus(card, 'llicense', isChecked);
            showNotification(data.message, 'success');
          } else {
            showNotification('Error: ' + data.message, 'error');
            // Revert checkbox state on error
            this.checked = !isChecked;
            updateCardStatus(card, 'llicense', !isChecked);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred while updating learning license status', 'error');
          // Revert checkbox state on error
          this.checked = !isChecked;
          updateCardStatus(card, 'llicense', !isChecked);
        })
        .finally(() => {
          // Remove loading state
          card.classList.remove('loading');
        });
    });

    // Function to update card status
    function updateCardStatus(card, type, isActive) {
      const subtitle = card.querySelector('.status-subtitle');

      if (type === 'absconder') {
        if (isActive) {
          card.classList.add('active');
          subtitle.textContent = 'Marked as Absconder';
        } else {
          card.classList.remove('active');
          subtitle.textContent = 'Not Absconder';
        }
      } else if (type === 'flowup') {
        if (isActive) {
          card.classList.add('active');
          subtitle.textContent = 'Follow Up Required';
        } else {
          card.classList.remove('active');
          subtitle.textContent = 'No Follow Up';
        }
      } else if (type === 'llicense') {
        if (isActive) {
          card.classList.add('active');
          subtitle.textContent = 'Learning License Required';
        } else {
          card.classList.remove('active');
          subtitle.textContent = 'No Learning License';
        }
      }
    }

    // Function to show notifications
    function showNotification(message, type) {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;
      notification.innerHTML = `
        <div class="notification-content">
          <i class="ti ti-${type === 'success' ? 'check' : 'x'}"></i>
          <span>${message}</span>
        </div>
      `;

      // Add styles
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: 300px;
      `;

      // Add to page
      document.body.appendChild(notification);

      // Remove after 3 seconds
      setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
          }
        }, 300);
      }, 3000);
    }

    // Add CSS for notifications
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
      }
      .notification-content {
        display: flex;
        align-items: center;
        gap: 8px;
      }
    `;
    document.head.appendChild(style);
  });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/view.blade.php ENDPATH**/ ?>