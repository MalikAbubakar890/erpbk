<?php $__env->startSection('page_content'); ?>
<div class="card p-4 shadow-sm">
  <div class="row">
    <div class="form-group col-md-6">
      <label>File Name:</label>
      <p><?php echo e($file->name); ?></p>
    </div>
    <div class="form-group col-md-6">
      <label>Uploaded By:</label>
      <p><?php echo e($file->uploader->name); ?></p>
    </div>
    <div class="form-group col-md-6">
      <label>Uploaded At:</label>
      <p><?php echo e($file->created_at->format('d M Y, h:i A')); ?></p>
    </div>
    <div class="form-group col-md-6">
      <label>Details:</label>
      <p><?php echo e($file->details); ?></p>
    </div>
   <div class="form-group col-md-12">
  <div style="text-align:center;"><label>File Preview:</label><br></div>
  <?php
    $fileUrl = asset('storage/' . $file->path);
    $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
?>


<div class="file-preview mt-4 text-center">
    <?php if($extension === 'pdf'): ?>
        <object data="<?php echo e($fileUrl); ?>" type="application/pdf" width="100%" height="600px">
            <p>
                PDF preview not supported by your browser. 
                <a href="<?php echo e($fileUrl); ?>" target="_blank">Open PDF</a>
            </p>
        </object>
    <?php elseif(in_array($extension, ['doc', 'docx'])): ?>
        <iframe 
            src="https://view.officeapps.live.com/op/embed.aspx?src=<?php echo e(urlencode($fileUrl)); ?>" 
            width="100%" 
            height="600px" 
            frameborder="0">
        </iframe>
    <?php elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
        <img src="<?php echo e($fileUrl); ?>" alt="Image Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px;">
    <?php else: ?>
        <p>Preview not available for this file type.</p>
    <?php endif; ?>
</div>


<div class="mt-3 text-center">
    <a href="<?php echo e($fileUrl); ?>" class="btn btn-primary" download>Download File</a>
</div>

</div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('upload_files.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/upload_files/show.blade.php ENDPATH**/ ?>