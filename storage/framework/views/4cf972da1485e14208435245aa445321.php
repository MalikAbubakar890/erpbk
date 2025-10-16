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
    $(document).ready(function() {
        getTotal();


    });
</script><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/vouchers/create.blade.php ENDPATH**/ ?>