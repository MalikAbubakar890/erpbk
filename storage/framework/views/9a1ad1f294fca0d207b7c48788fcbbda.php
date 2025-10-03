<li style="list-style:none; cursor: pointer;border-bottom:1px solid #efefef;"
  class="text-primary py-2"
  data-account-id="<?php echo e($account->id); ?>">

  <span class="toggle-btn plus-box" style="">+</span>
  <?php echo e($account->account_code); ?>-<?php echo e($account->name); ?>

  <span class="text-muted"><small>(<?php echo e($account->account_type); ?>)</small></span>
  <?php echo App\Helpers\Common::status($account->status); ?>


  
  <?php if(is_null($account->parent_id)): ?>
  <span class="lock-toggle"
    style="cursor:pointer;"
    title="<?php echo e($account->is_locked ? 'Parent account is locked' : 'Unlocked'); ?>"
    data-account-id="<?php echo e($account->id); ?>"
    data-locked="<?php echo e($account->is_locked ? '1' : '0'); ?>">
    <i class="fas <?php echo e($account->is_locked ? 'fa-lock text-secondary' : 'fa-unlock text-success'); ?>"></i>
  </span>
  <?php endif; ?>

  <span class="text-muted"><?php echo $account->notes; ?></span>

  <span style="float:right;">
    <?php echo Form::open(['route' => ['accounts.destroy', $account->id], 'method' => 'delete','id'=>'formajax']); ?>


    <a href="javascript:void(0);"
      data-size="lg"
      data-title="Edit Account"
      data-action="<?php echo e(route('accounts.edit', $account->id)); ?>"
      class="btn btn-info px-1 py2 edit-btn waves-effect waves-light
       <?php echo e(($account->is_locked) ? 'locked-btn' : 'show-modal'); ?>"
      <?php echo e(($account->is_locked) ? 'disabled' : ''); ?>>
      <i class="fa fa-edit fa-xs"></i>
    </a>

    <input type="hidden" id="reload_page" value="1" />
    <?php echo Form::button('<i class="fa fa-trash"></i>', [
    'type' => 'submit',
    'class' => 'btn btn-danger btn-xs p-1 delete-btn' . ($account->is_locked ? ' locked-btn' : ''),
    'onclick' => 'return confirm("Are you sure? You will not be able to revert this!")',
    ($account->is_locked ? 'disabled' : null)
    ]); ?>

    <?php echo Form::close(); ?>

  </span>

  <?php if($account->children->count() > 0): ?>
  <ul class="nested d-none">
    <?php $__currentLoopData = $account->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('accounts.account-node', ['account' => $child], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>
  <?php endif; ?>
</li><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/accounts/account-node.blade.php ENDPATH**/ ?>