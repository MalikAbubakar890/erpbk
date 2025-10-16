
                <form action="<?php echo e(url('riders/job_status',@$rider->id)); ?>" method="post" enctype="multipart/form-data" id="formajax">
<?php echo csrf_field(); ?>

<input type="hidden" name="job_status" value="1" />
                    <div class="row mt-1">
                        <div class="col-md-12">
                            
                            <div class="form-group">
                                <label>Reason</label>
                                <textarea rows="5" name="reason" class="form-control"></textarea>
                            </div>
                        </div>
                        <!--col-->
                    </div>
                    <!--row-->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save_rec btn btn-primary save_rec">Save</button>
                </div>
            </form>
<?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/riders/job_status-modal.blade.php ENDPATH**/ ?>