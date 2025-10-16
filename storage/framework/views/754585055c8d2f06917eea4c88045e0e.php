<?php echo Form::model($file, ['route' => ['upload_files.update', $file->id], 'method' => 'patch', 'files' => true, 'id' => 'formajax']); ?>

<div class="card-body">
  <div class="row">
    <?php echo $__env->make('upload_files.fields', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </div>
</div>
<div class="action-btn pt-3">
  <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
  <?php echo Form::submit('Update', ['class' => 'btn btn-primary']); ?>

</div>
<?php echo Form::close(); ?>

<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/upload_files/edit.blade.php ENDPATH**/ ?>