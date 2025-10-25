<?php echo Form::open(['route' => 'vouchers.store', 'id'=>'formajax']); ?>

<input type="hidden" id="reload_page" value="1">

<div class="">
    <?php echo $__env->make('vouchers.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>


<div class="card-footer">
    <?php echo Form::submit('Save', ['class' => 'btn btn-primary','onclick'=>'getTotal();']); ?>

</div>

<?php echo Form::close(); ?>

<script>
    // Wait for jQuery to be available
    (function() {
        if (typeof jQuery === 'undefined') {
            setTimeout(arguments.callee, 50);
            return;
        }

        $(document).ready(function() {
            getTotal();
        });
    })();
</script><?php /**PATH /home2/sxjnqpte/public_html/resources/views/vouchers/create.blade.php ENDPATH**/ ?>