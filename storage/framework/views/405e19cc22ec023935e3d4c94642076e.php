<?php echo Form::open(['route' => 'riders.storevendorcharges','id'=>'formajax']); ?>


<input type="hidden" id="reload_page" value="1">


<div class="row">
    <?php echo $__env->make('vouchers.vendorcharges_fields', ['bank_accounts' => $bank_accounts], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<div class="card-footer">
    <?php echo Form::submit('Save', ['class' => 'btn btn-primary','onclick'=>'getTotal();']); ?>

</div>

<?php echo Form::close(); ?>

<script>
    $(document).ready(function() {
        getTotal();
    });
</script><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/vendorcharges-modal.blade.php ENDPATH**/ ?>