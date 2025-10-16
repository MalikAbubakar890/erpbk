<!-- File Name -->
<div class="form-group col-sm-6">
  <?php echo Form::label('name', 'File Name:', ['class' => 'required']); ?>

  <?php echo Form::text('name', null, ['class' => 'form-control', 'required']); ?>

</div>

<!-- File Upload -->
<div class="form-group col-sm-6">
  <?php echo Form::label('file', 'Upload File:', ['class' => 'required']); ?>

  <?php echo Form::file('file', ['class' => 'form-control', 'required']); ?>

</div>

<!-- Details -->
<div class="form-group col-sm-12">
  <?php echo Form::label('details', 'Details:'); ?>

  <?php echo Form::textarea('details', null, ['class' => 'form-control']); ?>

</div>
<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/upload_files/fields.blade.php ENDPATH**/ ?>