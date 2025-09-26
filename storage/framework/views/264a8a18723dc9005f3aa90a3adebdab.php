
<?php $__env->startSection('title','Rider Report'); ?>
<?php $__env->startSection('content'); ?>
<style>
    .table tr:first-child>td {
        position: sticky;
        top: 0;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <h3>Rider Report</h3>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="card rounded-0">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <button class="btn btn-sm btn-success  exportToExcel action-btn"><i class="fa fa-file-excel"> Export</i> </button>
                        <form id="form">
                            <div class="d-flex d-flex-row gap-2">
                                <div class="col-md-2">
                                    <label>Designation</label>
                                    <select class="form-control form-select" name="designation">
                                        <option value="">Select</option>
                                        <?php echo App\Helpers\General::Designations(@$result['designation']); ?>

                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Vendor</label>

                                    <?php echo Form::select('VID', \App\Models\Vendors::dropdown(),null, ['class'=>'form-control form-select']); ?>

                                </div>
                                <div class="col-md-2">
                                    <label>Status</label>
                                    <select class="form-control form-select" name="status">
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = App\Helpers\General::RiderStatus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php if(request('status')==$key): ?>selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-2">

                                    <label>Biling Month</label>
                                    <input type="month" name="billing_month" value="<?php echo e(request('billing_month')??date('Y-m')); ?>" class="form-control" required />
                                </div>
                                <div class="col-md-2" style="margin-top:35px;">

                                    <button type="button" class="btn btn-primary" onclick="get_data()"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                            <!--row-->
                        </form>
                        <br>

                        <table id="table2excel" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Vendor</th>
                                    <th>Designation</th>
                                    <th>Person Code</th>
                                    <th>Labor Card</th>
                                    <th>Bike</th>
                                    <th>WPS</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Balance Forward</th>
                                    <th style="text-align: right;">Amount</th>
                                    <th style="text-align: right;">Balance</th>
                                    <th style="text-align: right;">Sub Total</th>
                                    <th style="text-align: right;">Total</th>

                                </tr>
                            </thead>
                            <tbody id="get_data"></tbody>
                            
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <div class="pagination-panel"></div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="<?php echo e(URL::asset('export_excel/jquery.table2excel.js')); ?>"></script>
<script>
    $(function() {
        //Initialize Select2 Elements
        $('.select2').select2({
            allowClear: true
        });

    });

    function get_data() {
        bodyblock();
        $.ajax({
            url: "<?php echo e(url('reports/rider_report_data')); ?>",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: $("#form").serialize(),
            dataType: "JSON",
            success: function(data) {
                $("#get_data").html(data.data);
                bodyunblock();
            }
        })
    }

    var jq = $.noConflict();
    jq(document).ready(function() {
        $(".exportToExcel").click(function() {
            jq("#table2excel").table2excel({
                filename: "Rider_report.xls",
                exclude: ".noExl",
                name: "Rider Report",
                filename: "Rider_" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls",
                fileext: ".xls",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: true,
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/reports/rider_report.blade.php ENDPATH**/ ?>