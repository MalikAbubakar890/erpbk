<?php echo Form::open(['route' => ['permissions.destroy', $id], 'method' => 'delete']); ?>

<div class='btn-group'>
   
    <?php echo Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => 'return confirm("'.__('crud.are_you_sure').'")'

    ]); ?>

</div>
<?php echo Form::close(); ?>

<?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/permissions/datatables_actions.blade.php ENDPATH**/ ?>