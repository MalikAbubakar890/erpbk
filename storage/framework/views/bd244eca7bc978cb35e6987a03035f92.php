<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Print Table</title>
        <meta charset="UTF-8">
        <meta name=description content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <style>
          body {margin: 20px}
          @media print
{
  .no-print, .no-print *
  {
      display: none !important;
  }
}
      </style>
  </head>
  <body>
    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-primary no-print">Back</a>
    <?php
    $settings = App\Helpers\Common::settings();

?>
    <table width="100%" style="font-family: sans-serif;margin-bottom:20px;">
      <tr>
          <td width="33.33%"><img src="<?php echo e(URL::asset('assets/img/logo-full.png')); ?>" width="150" /></td>
          <td width="33.33%" style="text-align: center;"><h4 style="margin-bottom: 10px;margin-top: 5px;font-size: 14px;"><?php echo e($settings['company_name']); ?></h4>
              <p style="margin-bottom: 5px;font-size: 14px;margin-top: 5px;"><?php echo e($settings['company_address']); ?></p>
              <p style="margin-bottom: 5px;font-size: 14px;margin-top: 5px;"> TRN <?php echo e($settings['vat_number']); ?></p>
          <td width="33.33%" style="text-align: right;"></td>
      </tr>

  </table>

        <table class="table table-condensed">
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($loop->first): ?>
                    <tr>
                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo $key; ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endif; ?>
                <tr>
                    <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_string($value) || is_numeric($value)): ?>
                            <td><?php echo $value; ?></td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
        <script>
          window.print();
        </script>
    </body>
</html>
<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/vendor/datatables/print.blade.php ENDPATH**/ ?>