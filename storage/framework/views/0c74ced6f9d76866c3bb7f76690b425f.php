
<?php $__env->startSection('title','Rider Report'); ?>
<?php $__env->startSection('content'); ?>
<style>
    .table tr:first-child>td {
        position: sticky;
        top: 0;
    }

    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -360px;
        width: 360px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
        transition: right .3s ease;
        z-index: 1111;
        overflow-y: auto;
    }

    .filter-sidebar.open {
        right: 0;
    }

    .filter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .35);
        display: none;
        z-index: 1110;
    }

    .filter-overlay.show {
        display: block;
    }

    .filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
    }

    .filter-body {
        padding: 12px 16px;
    }

    .btn-close {
        background: transparent;
        border: 0;
        font-size: 18px;
    }

    .card-header .actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .control-icon {
        cursor: pointer;
    }

    .sorted-asc:after {
        content: ' \25B2';
    }

    .sorted-desc:after {
        content: ' \25BC';
    }

    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .filter-loading-overlay {
        position: absolute;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .6);
    }

    .copy-text {
        cursor: pointer;
    }

    /* Totals cards */
    .totals-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .total-card {
        flex: 1 1 220px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-left-width: 6px;
        border-radius: 10px;
        padding: 12px 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .total-card .label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .total-card .value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .total-card .sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 2px;
    }

    .total-opening {
        border-left-color: #3b82f6;
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.06), rgba(59, 130, 246, 0.02));
    }

    .total-amount {
        border-left-color: #10b981;
        background: linear-gradient(180deg, rgba(16, 185, 129, 0.06), rgba(16, 185, 129, 0.02));
    }

    .total-balance {
        border-left-color: #8b5cf6;
        background: linear-gradient(180deg, rgba(139, 92, 246, 0.06), rgba(139, 92, 246, 0.02));
    }

    .total-debit {
        border-left-color: #f59e0b;
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.06), rgba(245, 158, 11, 0.02));
    }

    .total-credit {
        border-left-color: #ef4444;
        background: linear-gradient(180deg, rgba(239, 68, 68, 0.06), rgba(239, 68, 68, 0.02));
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h3>Rider Report</h3>
            <div class="actions">
                <button class="btn btn-sm btn-success exportToExcel action-btn"><i class="fa fa-file-excel"></i> Export</button>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <?php
                // Define table columns for column control (report-specific)
                $tableColumns = [
                ['data' => 'id', 'title' => '#'],
                ['data' => 'name', 'title' => 'Name'],
                ['data' => 'vendor', 'title' => 'Vendor'],
                ['data' => 'designation', 'title' => 'Designation'],
                ['data' => 'person_code', 'title' => 'Person Code'],
                ['data' => 'labor_card', 'title' => 'Labor Card'],
                ['data' => 'bike', 'title' => 'Bike'],
                ['data' => 'wps', 'title' => 'WPS'],
                ['data' => 'status', 'title' => 'Status'],
                ['data' => 'balance_forward', 'title' => 'Balance Forward'],
                ['data' => 'amount', 'title' => 'Amount'],
                ['data' => 'balance', 'title' => 'Balance'],
                ['data' => 'sub_total', 'title' => 'Sub Total'],
                ['data' => 'total', 'title' => 'Total'],
                // Utility columns for search/control (optional placeholders)
                ['data' => 'search', 'title' => 'Search'],
                ['data' => 'control', 'title' => 'Control'],
                ];
                ?>
                <?php echo $__env->make('components.column-control-panel', [
                'tableColumns' => $tableColumns,
                'exportRoute' => route('rider.exportCustomizableRiders'),
                'tableIdentifier' => 'rider_report_table'
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="card rounded-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">
                            <h4 class="m-0">Report Table</h4>
                        </div>
                        <div class="card-search">
                            <input type="text" id="quickSearch" name="quick_search" class="form-control" placeholder="Quick Search..." value="<?php echo e(request('quick_search')); ?>">
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive px-2 py-0" id="table-data">
                        <div id="totalsBar" class="mb-2" style="display:none;">
                            <div class="totals-cards">
                                <div class="total-card total-opening">
                                    <div class="label"><i class="fa fa-wallet"></i> Opening Balance</div>
                                    <div class="value" id="total_opening_balance">0.00</div>
                                </div>
                                <div class="total-card total-amount">
                                    <div class="label"><i class="fa fa-coins"></i> Total</div>
                                    <div class="value" id="total_amount">0.00</div>
                                </div>
                                <div class="total-card total-balance">
                                    <div class="label"><i class="fa fa-scale-balanced"></i> Balance (OB + Total)</div>
                                    <div class="value" id="total_b">0.00</div>
                                </div>
                                <div class="total-card total-debit">
                                    <div class="label"><i class="fa fa-arrow-down"></i> Debit Sum</div>
                                    <div class="value" id="total_debit_sum">0.00</div>
                                </div>
                                <div class="total-card total-credit">
                                    <div class="label"><i class="fa fa-arrow-up"></i> Credit Sum</div>
                                    <div class="value" id="total_credit_sum">0.00</div>
                                </div>
                            </div>
                        </div>
                        <table id="dataTableBuilder" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th title="#">#</th>
                                    <th title="Name">Name</th>
                                    <th title="Vendor">Vendor</th>
                                    <th title="Designation">Designation</th>
                                    <th title="Person Code">Person Code</th>
                                    <th title="Labor Card">Labor Card</th>
                                    <th title="Bike">Bike</th>
                                    <th title="WPS">WPS</th>
                                    <th title="Status">Status</th>
                                    <th title="Balance Forward" style="text-align: right;">Balance Forward</th>
                                    <th title="Amount" style="text-align: right;">Amount</th>
                                    <th title="Balance" style="text-align: right;">Balance</th>
                                    <th title="Sub Total" style="text-align: right;">Sub Total</th>
                                    <th title="Total" style="text-align: right;">Total</th>
                                    <th>
                                        <a class="openFilterSidebar" href="javascript:void(0);" title="Filters">
                                            <i class="fa fa-filter"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a class="openColumnControlSidebar" href="javascript:void(0);" title="Column Control">
                                            <i class="fa fa-columns"></i>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="get_data"></tbody>
                        </table>
                        <div id="paginationLinks" class="mt-2"></div>
                        <div class="filter-loading-overlay text-center">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
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

<!-- Filter Sidebar (same as Riders filters) -->
<div id="filterSidebar" class="filter-sidebar">
    <div class="filter-header">
        <h5>Filter Riders</h5>
        <button type="button" class="btn-close" id="closeSidebar">&times;</button>
    </div>
    <div class="filter-body" id="searchTopbody">
        <form id="filterForm">
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="designation">Filter by Designation</label>
                    <select class="form-control" id="designation" name="designation">
                        <?php
                        $emiratedesignation = DB::table('riders')->whereNotNull('designation')->where('designation', '!=', '')->select('designation')->distinct()->pluck('designation');
                        ?>
                        <option value="" selected>Select</option>
                        <?php $__currentLoopData = $emiratedesignation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $des): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($des); ?>" <?php echo e(request('designation') == $des ? 'selected' : ''); ?>><?php echo e($des); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label for="VID">Vendor</label>
                    <?php echo Form::select('VID', \App\Models\Vendors::dropdown(), request('VID'), ['class' => 'form-control form-select']); ?>

                </div>
                <div class="form-group col-md-12">
                    <label for="status">Status</label>
                    <select class="form-control form-select" id="status" name="status">
                        <option value="">Select</option>
                        <?php $__currentLoopData = App\Helpers\General::RiderStatus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(request('status')==$key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label for="billing_month">Billing Month</label>
                    <input type="month" id="billing_month" name="billing_month" value="<?php echo e(request('billing_month') ?? date('Y-m')); ?>" class="form-control" />
                </div>


                <div class="col-md-12 form-group text-center">
                    <button type="button" class="btn btn-primary pull-right mt-3" onclick="get_data()"><i class="fa fa-filter mx-2"></i> Filter Data</button>
                </div>
            </div>
        </form>
    </div>
    <div class="p-2"></div>
    <div class="p-2"></div>
    <div class="p-2"></div>
</div>
<!-- Filter Overlay -->
<div id="filterOverlay" class="filter-overlay"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="<?php echo e(URL::asset('export_excel/jquery.table2excel.js')); ?>"></script>
<script>
    $(document).ready(function() {
        // Filter sidebar toggle
        $(document).on('click', '#openFilterSidebar, .openFilterSidebar', function(e) {
            e.preventDefault();
            $('#filterSidebar').addClass('open');
            $('#filterOverlay').addClass('show');
            return false;
        });
        $('#closeSidebar, #filterOverlay').on('click', function() {
            $('#filterSidebar').removeClass('open');
            $('#filterOverlay').removeClass('show');
        });

        // Quick search
        $('#quickSearch').on('keyup', function(e) {
            if (e.keyCode === 13 || $(this).val().length === 0) {
                get_data();
            }
        });

        // Export button
        $(".exportToExcel").click(function() {
            $("#dataTableBuilder").table2excel({
                filename: "Rider_" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls",
                fileext: ".xls",
                exclude: ".noExl",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: true,
            });
        });

        // Initial load
        get_data();

        // Pagination controls
        $(document).on('change', '#pageSize', function() {
            applyPagination();
        });
        $(document).on('click', '#firstPage', function() {
            changePage('first');
        });
        $(document).on('click', '#prevPage', function() {
            changePage('prev');
        });
        $(document).on('click', '#nextPage', function() {
            changePage('next');
        });
        $(document).on('click', '#lastPage', function() {
            changePage('last');
        });
    });

    function get_data() {
        $('.filter-loading-overlay').show();
        $.ajax({
            url: "<?php echo e(url('reports/rider_report_data')); ?>",
            headers: {
                'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
            },
            type: "POST",
            timeout: 20000,
            data: $('#filterForm').serialize() + '&quick_search=' + encodeURIComponent($('#quickSearch').val() || ''),
            success: function(data) {
                try {
                    // Try to normalize both JSON and HTML responses
                    if (typeof data === 'string') {
                        try {
                            var parsed = JSON.parse(data);
                            data = parsed;
                        } catch (e) {
                            // Not JSON, assume it's pure HTML rows
                            $("#get_data").html(data);
                            $('#totalsBar').hide();
                            applyPagination();
                            $('.filter-loading-overlay').hide();
                            return;
                        }
                    }

                    // If we reach here, data should be an object with .data and totals
                    $("#get_data").html(data.data || '');

                    if (typeof data.opening_balance_total !== 'undefined') {
                        $('#total_opening_balance').text(parseFloat(data.opening_balance_total).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_amount').text(parseFloat(data.total).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_b').text(parseFloat(data.b_total).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_debit_sum').text(parseFloat(data.total_debit_sum).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_credit_sum').text(parseFloat(data.total_credit_sum).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#totalsBar').show();
                    } else {
                        $('#totalsBar').hide();
                    }

                    if (data.paginationLinks) {
                        $('#paginationLinks').html(data.paginationLinks);
                    }
                } finally {
                    $('.filter-loading-overlay').hide();
                    if (window.ColumnController && typeof window.ColumnController.reapplySettings === 'function') {
                        setTimeout(function() {
                            window.ColumnController.reapplySettings();
                        }, 60);
                    }
                }
            },
            error: function(xhr) {
                $('.filter-loading-overlay').hide();
                console.error('Rider report load failed', xhr && xhr.status, xhr && xhr.responseText);
                if (!$('#get_data').children().length) {
                    $('#get_data').html('<tr><td colspan="16"><div class="alert alert-warning mb-0">Failed to load report data. Please try again.</div></td></tr>');
                }
            }
        });
    }

    // Handle clicks on pagination links (rendered from backend component)
    $(document).on('click', '#paginationLinks a', function(e) {
        e.preventDefault();
        var url = new URL($(this).attr('href'), window.location.origin);
        var page = url.searchParams.get('page') || 1;
        get_data_with_page(page);
    });

    function get_data_with_page(page) {
        $('.filter-loading-overlay').show();
        $.ajax({
            url: "<?php echo e(url('reports/rider_report_data')); ?>?page=" + encodeURIComponent(page),
            headers: {
                'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
            },
            type: "POST",
            timeout: 20000,
            data: $('#filterForm').serialize() + '&quick_search=' + encodeURIComponent($('#quickSearch').val() || ''),
            dataType: "JSON",
            success: function(data) {
                try {
                    if (typeof data === 'string') {
                        try {
                            data = JSON.parse(data);
                        } catch (e) {
                            $("#get_data").html(data);
                            $('#totalsBar').hide();
                            $('#paginationLinks').empty();
                            return;
                        }
                    }
                    $("#get_data").html(data.data || '');
                    if (typeof data.opening_balance_total !== 'undefined') {
                        $('#total_opening_balance').text(parseFloat(data.opening_balance_total).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_amount').text(parseFloat(data.total).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_b').text(parseFloat(data.b_total).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_debit_sum').text(parseFloat(data.total_debit_sum).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#total_credit_sum').text(parseFloat(data.total_credit_sum).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#totalsBar').show();
                    } else {
                        $('#totalsBar').hide();
                    }
                    if (data.paginationLinks) {
                        $('#paginationLinks').html(data.paginationLinks);
                    }
                } finally {
                    $('.filter-loading-overlay').hide();
                    if (window.ColumnController && typeof window.ColumnController.reapplySettings === 'function') {
                        setTimeout(function() {
                            window.ColumnController.reapplySettings();
                        }, 60);
                    }
                }
            },
            error: function(xhr) {
                $('.filter-loading-overlay').hide();
                console.error('Rider report load failed', xhr && xhr.status, xhr && xhr.responseText);
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home2/sxjnqpte/public_html/resources/views/reports/rider_report.blade.php ENDPATH**/ ?>