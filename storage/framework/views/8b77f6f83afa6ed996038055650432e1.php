<?php echo Form::open(['route' => ['departments.destroy', $id], 'method' => 'delete','id'=>'formajax']); ?>

<div class='btn-group'>
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department_edit')): ?>
    <a href="javascript:void(0);" data-title="Edit" data-size="sm" data-action="<?php echo e(route('departments.edit', $id)); ?>" class='btn btn-info btn-sm show-modal'>
        <i class="fa fa-edit"></i>
    </a>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department_delete')): ?>

    <?php echo Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => 'return confirm("Are you sure, want to delete this department ?")'

    ]); ?>

    <?php endif; ?>
</div>
<?php echo Form::close(); ?>

<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/departments/datatables_actions.blade.php ENDPATH**/ ?>