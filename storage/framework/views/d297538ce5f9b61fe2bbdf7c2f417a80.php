<form action="<?php echo e(url('riders/sendemail/'.$rider->id)); ?>" method="POST" id="formajax">

    <div class="col-md-12 form-group">
        <label>Email Address</label>
        <input type="email" class="form-control form-control" name="email_to" value="<?php echo e($rider->personal_email); ?>" readonly>
    </div>
    <div class="col-md-12 form-group">
        <label>Subject</label>
        <input type="text" class="form-control form-control" name="email_subject" value="Warning for Attendance and Performance  Rider I,D <?php echo e($rider->rider_id); ?>" >
    </div>
    <div class="col-md-12 form-group">
        <label>Message</label>
<textarea name="email_message" rows="8" class="form-control">Hi <?php echo e($rider->name); ?>,

Rider I,D : <?php echo e($rider->rider_id); ?>

Employee Name : <?php echo e($rider->name); ?>


I hope you're doing well.
We need to address some important issues regarding your attendance and performance in <?php echo e(date('M Y')); ?>. We've noticed that you have been absent several times without prior notice. Additionally, your performance as a bike rider has not met the company’s standards. Specifically, you have been late logging in, and your on-time delivery rate has been below expectations.

As per company guidelines, we expect 26 perfect attendance days and at least 90% on-time delivery. Unfortunately, these targets were not met.
This is a formal warning. If your attendance and performance do not improve immediately, we may need to take further action, including ending your employment According to UAE labor law.
If there are any challenges affecting your work, please speak with your Fleet Supervisor or HR. We are here to support you.
We expect to see improvement starting right away.

Best regards,
<?php echo e(env('APP_NAME')); ?>

</textarea>
    </div>
    <div class="col-md-6 form-group">
      <label>Activity Attachment Month</label>
      <input type="month" name="month" value="<?php echo e(request('month')??date('Y-m')); ?>" class="form-control" />
  </div>

    <button type="submit" class="btn btn-primary pull-right mt-3">Send Email</button>
  </form>
<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/riders/send_email.blade.php ENDPATH**/ ?>