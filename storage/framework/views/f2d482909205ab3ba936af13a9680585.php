<!-- Rider Id Field -->
<div class="col-sm-12">
    <?php echo Form::label('rider_id', 'Rider ID:'); ?>

    <p><?php echo e($riderEmails->rider->rider_id); ?></p>
</div>

<!-- Mail To Field -->
<div class="col-sm-12">
    <?php echo Form::label('mail_to', 'Mail To:'); ?>

    <p><?php echo e($riderEmails->mail_to); ?></p>
</div>

<!-- Subject Field -->
<div class="col-sm-12">
    <?php echo Form::label('subject', 'Subject:'); ?>

    <p><?php echo e($riderEmails->subject); ?></p>
</div>

<!-- Message Field -->
<div class="col-sm-12">
    <?php echo Form::label('message', 'Message:'); ?>

    <p><?php echo e($riderEmails->message); ?></p>
</div>

<!-- Status Field -->
<div class="col-sm-12">
    <?php echo Form::label('status', 'Status:'); ?>

    <p style="text-transform: uppercase;"><?php echo e($riderEmails->status); ?></p>
</div>

<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/rider_emails/show_fields.blade.php ENDPATH**/ ?>