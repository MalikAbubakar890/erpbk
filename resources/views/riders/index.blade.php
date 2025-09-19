@extends('layouts.app')
@section('title','Riders')

@push('third_party_stylesheets')
{{-- SortableJS for drag and drop functionality --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/riders-styles.css') }}">
@endpush
@section('content')
<div style="display: none;" class="loading-overlay" id="loading-overlay">
    <div class="spinner-border text-primary" role="status"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <!-- Enhanced Fleet Supervisor Accordion Section -->
        <div class="fleet-supervisor-section">
            <div class="fleet-supervisor-header">
                <div class="fleet-supervisor-header-left">
                    <div class="fleet-supervisor-icon">
                        <i class="ti ti-users"></i>
                    </div>
                    <div>
                        <h1 class="fleet-supervisor-title">Fleet Supervisors</h1>
                        <p class="fleet-supervisor-subtitle">Manage and monitor fleet supervisor performance</p>
                    </div>
                </div>
                <button class="fleet-supervisor-toggle" id="fleetSupervisorToggle">
                    <span>Toggle View</span>
                    <i class="ti ti-chevron-down"></i>
                </button>
            </div>
            <div class="fleet-supervisor-accordion collapsed" id="fleetSupervisorAccordion">
                <div class="fleet-supervisor-cards">
                    @php
                    $dropdown = DB::table('dropdowns')->where('label', 'Fleet Supervisor')->first();
                    $fleetSupervisors = $dropdown && $dropdown->values ? json_decode($dropdown->values, true) : [];
                    @endphp

                    @foreach($fleetSupervisors as $fleet)
                    <div class="fleet-supervisor-card @if($fleet == request('fleet_supervisor')) active @endif"
                        data-fleet_supervisor="{{ $fleet }}">
                        <h3 class="fleet-supervisor-name">{{ $fleet }}</h3>
                        <div class="fleet-supervisor-stats">
                            <div class="fleet-stat active">
                                <i class="fleet-stat-icon ti ti-user-check"></i>
                                <span class="fleet-stat-label">Active</span>
                                <span class="fleet-stat-value">{{ \App\Models\Riders::where('fleet_supervisor', $fleet)->where('status', 1)->count() }}</span>
                            </div>
                            <div class="fleet-stat inactive">
                                <i class="fleet-stat-icon ti ti-user-x"></i>
                                <span class="fleet-stat-label">Inactive</span>
                                <span class="fleet-stat-value">{{ \App\Models\Riders::where('fleet_supervisor', $fleet)->where('status', 3)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Enhanced Filter Section -->
        <div class="filter-section mb-4">
            <div class="row">
                <div class="col-lg-8">
                    <div class="filter-cards">
                        <!-- Status Filter Card -->
                        <div class="filter-card">
                            <div class="filter-header">
                                <div class="filter-icon">
                                    <i class="ti ti-filter"></i>
                                </div>
                                <div class="filter-title">
                                    <h6 class="mb-0">Status Filter</h6>
                                    <small class="text-muted">Filter riders by status</small>
                                </div>
                            </div>
                            <div class="filter-body">
                                <form method="GET" action="{{ route('riders.index') }}" id="statusFilterForm">
                                    <div class="select">
                                        <select class="form-control select2" id="rider_status" name="rider_status[]" multiple onchange="this.form.submit()">
                                            <option value="absconder" {{ in_array('absconder', request('rider_status', [])) ? 'selected' : '' }}>Absconder</option>
                                            <option value="followup" {{ in_array('followup', request('rider_status', [])) ? 'selected' : '' }}>Follow Up</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Balance Filter Card -->
                        <div class="filter-card">
                            <div class="filter-header">
                                <div class="filter-icon">
                                    <i class="ti ti-cash"></i>
                                </div>
                                <div class="filter-title">
                                    <h6 class="mb-0">Recovery Riders</h6>
                                    <small class="text-muted">Riders with outstanding balance</small>
                                </div>
                            </div>
                            <div class="filter-body">
                                <button type="button" class="balance-filter-btn {{ request('balance_filter') == 'greater_than_zero' ? 'checked' : '' }}"
                                    data-balance_filter="greater_than_zero"
                                    id="balanceFilterBtn">
                                    <div class="balance-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Active</span>
                                            <span class="stat-value">{{ \App\Models\Riders::where('status', 1)->whereHas('account', function($q) { $q->whereRaw('(SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM transactions WHERE account_id = accounts.id) > 0'); })->count() }}</span>
                                        </div>
                                        <div class="stat-divider"></div>
                                        <div class="stat-item">
                                            <span class="stat-label">Inactive</span>
                                            <span class="stat-value">{{ \App\Models\Riders::where('status', 3)->whereHas('account', function($q) { $q->whereRaw('(SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM transactions WHERE account_id = accounts.id) > 0'); })->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="filter-indicator">
                                        <i class="ti ti-check"></i>
                                    </div>
                                </button>

                                <!-- Balance Filter Dropdown -->
                                <div class="balance-dropdown" id="balanceDropdown">
                                    <div class="dropdown-header">
                                        <h6 class="mb-0">Balance Filter Results</h6>
                                        <button type="button" class="btn-close" onclick="closeBalanceDropdown()">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="filter-summary">
                                            <div class="summary-item">
                                                <span class="summary-label">Filter Applied:</span>
                                                <span class="summary-value">Balance > 0</span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="summary-label">Total Results:</span>
                                                <span class="summary-value" id="totalBalanceResults">{{ \App\Models\Riders::whereHas('account', function($q) { $q->whereRaw('(SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM transactions WHERE account_id = accounts.id) > 0'); })->count() }}</span>
                                            </div>
                                        </div>
                                        <div class="quick-actions">
                                            <button type="button" class="quick-action-btn" onclick="exportBalanceResults()">
                                                <i class="ti ti-download"></i>
                                                Export Results
                                            </button>
                                            <button type="button" class="quick-action-btn" onclick="clearBalanceFilter()">
                                                <i class="ti ti-x"></i>
                                                Clear Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="action-section">
                        <div class="action-header">
                            <h6 class="mb-0">Quick Actions</h6>
                            <small class="text-muted">Manage riders efficiently</small>
                        </div>
                        <div class="action-buttons">
                            @can('rider_create')
                            <div class="action-dropdown-container">
                                <button class="action-dropdown-btn" id="addRiderDropdownBtn">
                                    <i class="ti ti-plus"></i>
                                    <span>Add Rider</span>
                                    <i class="ti ti-chevron-down"></i>
                                </button>
                                <div class="action-dropdown-menu" id="addRiderDropdown">
                                    <a class="action-dropdown-item" href="{{ route('riders.create') }}">
                                        <i class="ti ti-user-plus"></i>
                                        <div>
                                            <div class="action-dropdown-item-text">Create New Rider</div>
                                            <div class="action-dropdown-item-desc">Add a new rider to the system</div>
                                        </div>
                                    </a>
                                    <a class="action-dropdown-item show-modal" href="javascript:void(0);" data-size="sm" data-title="Import Today Attendance" data-action="{{ route('rider.attendance_import') }}">
                                        <i class="ti ti-calendar-check"></i>
                                        <div>
                                            <div class="action-dropdown-item-text">Import Today Attendance</div>
                                            <div class="action-dropdown-item-desc">Import attendance data for today</div>
                                        </div>
                                    </a>
                                    <a class="action-dropdown-item show-modal" href="javascript:void(0);" data-size="sm" data-title="Import Rider Activities" data-action="{{ route('rider.activities_import') }}">
                                        <i class="ti ti-activity"></i>
                                        <div>
                                            <div class="action-dropdown-item-text">Import Activities</div>
                                            <div class="action-dropdown-item-desc">Import rider activity data</div>
                                        </div>
                                    </a>
                                    <a class="action-dropdown-item" href="{{ route('rider.exportRiders') }}">
                                        <i class="ti ti-file-export"></i>
                                        <div>
                                            <div class="action-dropdown-item-text">Export Riders</div>
                                            <div class="action-dropdown-item-desc">Export rider data to Excel</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            @endcan
                        </div>
                        <div id="filterSidebar" class="filter-sidebar" style="z-index: 1111;">
                            <div class="filter-header">
                                <h5>Filter Riders</h5>
                                <button type="button" class="btn-close" id="closeSidebar"></button>
                            </div>
                            <div class="filter-body" id="searchTopbody">
                                <form id="filterForm" action="{{ route('riders.index') }}" method="GET">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="id">Rider Id</label>
                                            <input type="number" name="rider_id" class="form-control" placeholder="Filter By Rider ID" value="{{ request('rider_id') }}">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="name">Rider Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Filter By Name" value="{{ request('name') }}">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="fleet_supervisor">Filter by Fleet SuperVisor</label>
                                            <select class="form-control " id="fleet_supervisor" name="fleet_supervisor">
                                                @php
                                                $supervisorRow = DB::table('dropdowns')
                                                ->where('label', 'Fleet Supervisor')
                                                ->whereNotNull('values')
                                                ->first();
                                                $fleetSupervisors = [];
                                                if ($supervisorRow && $supervisorRow->values) {
                                                $fleetSupervisors = json_decode($supervisorRow->values, true);
                                                }
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($fleetSupervisors as $supervisor)
                                                <option value="{{ $supervisor }}" {{ request('fleet_supervisor') == $supervisor ? 'selected' : '' }}>{{ $supervisor }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="hub">Filter by HUB</label>
                                            <select class="form-control " id="hub" name="hub">
                                                @php
                                                $emirateHubs = DB::table('riders')
                                                ->whereNotNull('designation')
                                                ->where('designation', '!=', '')
                                                ->select('designation')
                                                ->distinct()
                                                ->pluck('designation');
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($emirateHubs as $hub)
                                                <option value="{{ $hub }}" {{ request('hub') == $hub ? 'selected' : '' }}>{{ $hub }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="customer_id">Filter by Customer</label>
                                            <select class="form-control " id="customer_id" name="customer_id">
                                                @php
                                                $customerIds = DB::table('riders')
                                                ->whereNotNull('customer_id')
                                                ->where('customer_id', '!=', '')
                                                ->pluck('customer_id')
                                                ->unique();

                                                $customers = DB::table('customers')
                                                ->whereIn('id', $customerIds)
                                                ->select('id', 'name')
                                                ->get();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="bike">Bike Number</label>
                                            <input type="text" name="branded_plate_no" value="{{ request('bike') }}" class="form-control" placeholder="Filter By Bike Number">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="designation">Filter by Designation</label>
                                            <select class="form-control " id="designation" name="designation">
                                                @php
                                                $emiratedesignation = DB::table('riders')
                                                ->whereNotNull('designation')
                                                ->where('designation', '!=', '')
                                                ->select('designation')
                                                ->distinct()
                                                ->pluck('designation');
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($emiratedesignation as $des)
                                                <option value="{{ $des }}" {{ request('designation') == $des ? 'selected' : '' }}>{{ $des }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="attandence">Filter by Attandence</label>
                                            <select class="form-control " id="attendance" name="attendance">
                                                @php
                                                $attandence = DB::table('riders')
                                                ->whereNotNull('attendance')
                                                ->where('attendance', '!=', '')
                                                ->select('attendance')
                                                ->distinct()
                                                ->pluck('attendance');
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($attandence as $att)
                                                <option value="{{ $att }}" {{ request('attandence') == $att ? 'selected' : '' }}>{{ $att }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- <div class="form-group col-md-12">
                                    <label for="status">Filter by Status</label>
                                    <select class="form-control " id="status" name="status">
                                        <option value="" selected>Select</option>
                                        <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="3" {{ request('status') == 3 ? 'selected' : '' }}>In Active</option>
                                    </select>
                                </div> -->
                                        <div class="form-group col-md-12">
                                            <label for="bike_assignment_status">Filter by Bike Assignment</label>
                                            <select class="form-control " id="bike_assignment_status" name="bike_assignment_status">
                                                <option value="" selected>Select</option>
                                                <option value="Active" {{ request('bike_assignment_status') == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ request('bike_assignment_status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="quick_search">Quick Search</label>
                                            <input type="text" name="quick_search" id="quickSearchSidebar" class="form-control" placeholder="Quick Search..." value="{{ request('quick_search') }}">
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <button type="submit" class="btn btn-primary pull-right mt-3"><i class="fa fa-filter mx-2"></i> Filter Data</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="filter-overlay" id="filterOverlay"></div>

                        {{-- Include Column Control Panel --}}
                        @php
                        $tableColumns = [
                        ['data' => 'rider_id', 'title' => 'Rider ID'],
                        ['data' => 'name', 'title' => 'Name'],
                        ['data' => 'company_contact', 'title' => 'Contact'],
                        ['data' => 'fleet_supervisor', 'title' => 'Fleet Supv'],
                        ['data' => 'emirate_hub', 'title' => 'Hub'],
                        ['data' => 'customer_id', 'title' => 'Customer'],
                        ['data' => 'designation', 'title' => 'Desig'],
                        ['data' => 'bike', 'title' => 'Bike'],
                        ['data' => 'status', 'title' => 'Status'],
                        ['data' => 'shift', 'title' => 'Shift'],
                        ['data' => 'attendance', 'title' => 'ATTN'],
                        ['data' => 'orders_sum', 'title' => 'Orders'],
                        ['data' => 'days', 'title' => 'Days'],
                        ['data' => 'balance', 'title' => 'Balance'],
                        ['data' => 'action', 'title' => 'Actions'],
                        ['data' => 'search', 'title' => 'Search'],
                        ['data' => 'control', 'title' => 'Control']
                        ];
                        @endphp

                        @include('components.column-control-panel', [
                        'tableColumns' => $tableColumns,
                        'exportRoute' => route('rider.exportCustomizableRiders'),
                        'tableIdentifier' => 'riders_table'
                        ])
                    </div>
                </div>
            </div>
</section>
<div class="content px-0">
    @include('flash::message')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title">
                <h3>Riders</h3>
            </div>
            <div class="card-search">
                <input type="text" id="quickSearch" name="quick_search" class="form-control" placeholder="Quick Search..." value="{{ request('quick_search') }}">
            </div>
        </div>
        <div class="card-body table-responsive px-2 py-0" id="table-data">
            @include('riders.table', ['data' => $data])
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#fleet_supervisor').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Fleet SuperVisor",
            allowClear: true, // ✅ cross icon enable
        });
        $('#hub').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By HUB",
            allowClear: true, // ✅ cross icon enable
        });
        $('#customer_id').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Customer",
            allowClear: true, // ✅ cross icon enable
        });
        $('#designation').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Designation",
            allowClear: true, // ✅ cross icon enable
        });
        $('#bike_assignment_status').select2({
            dropdownParent: $('#searchTopbody'),
            allowClear: true, // ✅ cross icon enable
            placeholder: "Filter By status",
        });
        $('#attendance').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Attandence",
            allowClear: true, // ✅ cross icon enable
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        // Sidebar open/close
        $(document).on('click', '#openFilterSidebar, .openFilterSidebar', function() {
            $('#filterSidebar').addClass('open');
            $('#filterOverlay').addClass('show');
        });
        $('#closeSidebar, #filterOverlay').on('click', function() {
            $('#filterSidebar').removeClass('open');
            $('#filterOverlay').removeClass('show');
        });
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            $('#loading-overlay').show();
            $('#filterSidebar').removeClass('open');
            $('#filterOverlay').removeClass('show');
            const loaderStartTime = Date.now();
            let filteredFields = $(this).serializeArray().filter(field => field.name !== '_token' && field.value.trim() !== '');
            let formData = $.param(filteredFields);
            $.ajax({
                url: "{{ route('riders.index') }}",
                type: "GET",
                data: formData,
                success: function(data) {
                    $('#table-data').html(data.tableData);
                    let newUrl = "{{ route('riders.index') }}" + (formData ? '?' + formData : '');
                    history.pushState(null, '', newUrl);
                    if (filteredFields.length > 0) {
                        $('#clearFilterBtn').show();
                    } else {
                        $('#clearFilterBtn').hide();
                    }

                    // Reapply column control settings after table update
                    if (window.ColumnController) {
                        setTimeout(() => {
                            window.ColumnController.reapplySettings();
                            window.ColumnController.initializeDropdowns();
                        }, 100);
                    }

                    const elapsed = Date.now() - loaderStartTime;
                    const remaining = 1000 - elapsed;
                    setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    const elapsed = Date.now() - loaderStartTime;
                    const remaining = 1000 - elapsed;
                    setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                }
            });
        });
        // Quick search input (main)
        $('#quickSearch').on('keyup', function(e) {
            if (e.keyCode === 13 || $(this).val().length === 0) {
                // Set the sidebar quick search too
                $('#quickSearchSidebar').val($(this).val());
                $('#filterForm').submit();
            }
        });
        // Quick search input (sidebar)
        $('#quickSearchSidebar').on('keyup', function(e) {
            if (e.keyCode === 13 || $(this).val().length === 0) {
                // Set the main quick search too
                $('#quickSearch').val($(this).val());
                $('#filterForm').submit();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Fleet Supervisor Accordion Toggle
        $('#fleetSupervisorToggle').on('click', function() {
            const accordion = $('#fleetSupervisorAccordion');
            const toggle = $(this);

            if (accordion.hasClass('expanded')) {
                accordion.removeClass('expanded').addClass('collapsed');
                toggle.addClass('collapsed');
            } else {
                accordion.removeClass('collapsed').addClass('expanded');
                toggle.removeClass('collapsed');
            }
        });

        // Add Rider Dropdown Toggle
        $('#addRiderDropdownBtn').on('click', function(e) {
            e.stopPropagation();
            const dropdown = $('#addRiderDropdown');
            const btn = $(this);

            if (dropdown.hasClass('show')) {
                dropdown.removeClass('show');
                btn.removeClass('open');
            } else {
                // Close other dropdowns
                $('.action-dropdown-menu').removeClass('show');
                $('.action-dropdown-btn').removeClass('open');
                // Show this dropdown
                dropdown.addClass('show');
                btn.addClass('open');
            }
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.action-dropdown-container').length) {
                $('.action-dropdown-menu').removeClass('show');
                $('.action-dropdown-btn').removeClass('open');
            }
        });

        $('.fleet-supervisor-card').on('click', function() {
            const fleet_supervisor = $(this).data('fleet_supervisor');
            const loaderStartTime = Date.now();
            $('#loading-overlay').show();

            // Check if this card is already active
            const isCurrentlyActive = $(this).hasClass('active');

            if (isCurrentlyActive) {
                // If already active, deselect it (remove all filters)
                $('.fleet-supervisor-card').removeClass('active');
                $('.balance-filter-btn').removeClass('active');

                $.ajax({
                    url: "{{ route('riders.index') }}",
                    type: "GET",
                    data: {}, // No filters - show all riders
                    success: function(data) {
                        $('#table-data').html(data.tableData);
                        let newUrl = "{{ route('riders.index') }}";
                        history.pushState(null, '', newUrl);

                        // Reapply column control settings after table update
                        if (window.ColumnController) {
                            setTimeout(() => {
                                window.ColumnController.reapplySettings();
                                window.ColumnController.initializeDropdowns();
                            }, 100);
                        }

                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    },
                    error: function() {
                        console.error(error);
                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    }
                });
            } else {
                // If not active, select it and apply fleet supervisor filter
                $('.fleet-supervisor-card').removeClass('active');
                $('.balance-filter-btn').removeClass('active');
                $(this).addClass('active');

                $.ajax({
                    url: "{{ route('riders.index') }}",
                    type: "GET",
                    data: {
                        fleet_supervisor: fleet_supervisor
                    },
                    success: function(data) {
                        $('#table-data').html(data.tableData);
                        let newUrl = "{{ route('riders.index') }}" + '?fleet_supervisor=' + encodeURIComponent(fleet_supervisor);
                        history.pushState(null, '', newUrl);

                        // Reapply column control settings after table update
                        if (window.ColumnController) {
                            setTimeout(() => {
                                window.ColumnController.reapplySettings();
                                window.ColumnController.initializeDropdowns();
                            }, 100);
                        }

                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    },
                    error: function() {
                        console.error(error);
                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    }
                });
            }
        });

        $('.balance-filter-btn').on('click', function() {
            const balance_filter = $(this).data('balance_filter');
            const loaderStartTime = Date.now();
            $('#loading-overlay').show();

            // Check if this button is already active or checked
            const isCurrentlyActive = $(this).hasClass('active');
            const isCurrentlyChecked = $(this).hasClass('checked');

            if (isCurrentlyActive || isCurrentlyChecked) {
                // If already active/checked, deselect it (remove all filters)
                $('.fleet-supervisor-card').removeClass('active');
                $('.balance-filter-btn').removeClass('active checked');

                $.ajax({
                    url: "{{ route('riders.index') }}",
                    type: "GET",
                    data: {}, // No filters - show all riders
                    success: function(data) {
                        $('#table-data').html(data.tableData);
                        let newUrl = "{{ route('riders.index') }}";
                        history.pushState(null, '', newUrl);

                        // Reapply column control settings after table update
                        if (window.ColumnController) {
                            setTimeout(() => {
                                window.ColumnController.reapplySettings();
                                window.ColumnController.initializeDropdowns();
                            }, 100);
                        }

                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    },
                    error: function() {
                        console.error(error);
                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    }
                });
            } else {
                // If not active, select it and apply balance filter
                $('.fleet-supervisor-card').removeClass('active');
                $('.balance-filter-btn').removeClass('active checked');
                $(this).addClass('checked');

                $.ajax({
                    url: "{{ route('riders.index') }}",
                    type: "GET",
                    data: {
                        balance_filter: balance_filter
                    },
                    success: function(data) {
                        $('#table-data').html(data.tableData);
                        let newUrl = "{{ route('riders.index') }}" + '?balance_filter=' + encodeURIComponent(balance_filter);
                        history.pushState(null, '', newUrl);

                        // Reapply column control settings after table update
                        if (window.ColumnController) {
                            setTimeout(() => {
                                window.ColumnController.reapplySettings();
                                window.ColumnController.initializeDropdowns();
                            }, 100);
                        }

                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    },
                    error: function() {
                        console.error(error);
                        const elapsed = Date.now() - loaderStartTime;
                        const remaining = 1000 - elapsed;
                        setTimeout(() => $('#loading-overlay').hide(), remaining > 0 ? remaining : 0);
                    }
                });
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.querySelector('#dataTableBuilder');
        const headers = table.querySelectorAll('th.sorting');
        const tbody = table.querySelector('tbody');

        headers.forEach((header, colIndex) => {
            header.addEventListener('click', () => {
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const isAsc = header.classList.contains('sorted-asc');

                // Clear previous sort classes
                headers.forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));

                // Add new sort direction
                header.classList.add(isAsc ? 'sorted-desc' : 'sorted-asc');

                // Sort logic
                rows.sort((a, b) => {
                    let aText = a.children[colIndex]?.textContent.trim().toLowerCase();
                    let bText = b.children[colIndex]?.textContent.trim().toLowerCase();

                    const aVal = isNaN(aText) ? aText : parseFloat(aText);
                    const bVal = isNaN(bText) ? bText : parseFloat(bText);

                    if (aVal < bVal) return isAsc ? 1 : -1;
                    if (aVal > bVal) return isAsc ? -1 : 1;
                    return 0;
                });

                // Re-append sorted rows
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });
    document.querySelector('.copy-text').addEventListener('click', function() {
        const value = this.querySelector('.copy-value').textContent.trim();
        const icon = this.querySelector('i');

        navigator.clipboard.writeText(value).then(() => {
            // Icon ko tick mark me change karo
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');

            // 1.5 sec baad wapas copy icon me badal do
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 1500);
        });
    });

    // Initialize Select2 for multi-select status filter with enhanced features
    $(document).ready(function() {
        $('#rider_status').select2({
            placeholder: '🎯 Select Status to Filter',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            dropdownParent: $('body'),
            templateResult: function(data) {
                if (!data.id) return data.text;

                var $result = $(
                    '<div class="select2-result-item">' +
                    '<div class="option-content">' +
                    '<span class="option-icon">' + (data.id === 'absconder' ? '🚨' : '🔔') + '</span>' +
                    '<span class="option-text">' + data.text + '</span>' +
                    '</div>' +
                    '</div>'
                );

                return $result;
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        // Add loading animation when form is submitted
        $('#statusFilterForm').on('submit', function() {
            $('.select2-selection__choice').addClass('loading');

            // Show loading overlay
            $('#loading-overlay').show();
        });

        // Handle form submission with proper parameter handling
        $('#rider_status').on('change', function() {
            // Add ripple effect
            $('.select').addClass('ripple-effect');
            setTimeout(() => {
                $('.select').removeClass('ripple-effect');
            }, 600);

            // Add a small delay to ensure select2 values are updated
            setTimeout(function() {
                $('#statusFilterForm').submit();
            }, 100);
        });

        // Add hover effects for better UX
        $('.select').hover(
            function() {
                $(this).addClass('hover-effect');
            },
            function() {
                $(this).removeClass('hover-effect');
            }
        );

        // Add click animation
        $('.select').on('click', function() {
            $(this).addClass('click-effect');
            setTimeout(() => {
                $(this).removeClass('click-effect');
            }, 200);
        });

        // Custom scrollbar for dropdown
        $('.select2-dropdown').on('DOMNodeInserted', function() {
            $('.select2-results__options').addClass('custom-scrollbar');
        });

        // Balance Filter Functionality
        $('#balanceFilterBtn').on('click', function(e) {
            e.stopPropagation();
            const dropdown = $('#balanceDropdown');

            if (dropdown.hasClass('show')) {
                closeBalanceDropdown();
            } else {
                // Close other dropdowns
                $('.balance-dropdown').removeClass('show');
                // Show this dropdown
                dropdown.addClass('show');

                // Update dropdown content based on current filter state
                updateBalanceDropdownContent();
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.filter-body').length) {
                closeBalanceDropdown();
            }
        });

        // Balance filter button click handler
        $('.balance-filter-btn').on('click', function() {
            const balanceFilter = $(this).data('balance_filter');
            const currentUrl = new URL(window.location);

            if (currentUrl.searchParams.get('balance_filter') === balanceFilter) {
                // Remove filter if already active
                currentUrl.searchParams.delete('balance_filter');
                $(this).removeClass('active');
            } else {
                // Apply filter
                currentUrl.searchParams.set('balance_filter', balanceFilter);
                $(this).addClass('active');
            }

            // Redirect to new URL
            window.location.href = currentUrl.toString();
        });
    });

    // Add CSS for enhanced interactions
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .ripple-effect {
                animation: ripple 0.6s ease-out;
            }
            
            @keyframes ripple {
                0% { transform: scale(1); }
                50% { transform: scale(1.02); }
                100% { transform: scale(1); }
            }
            
            .hover-effect {
                animation: hover-pulse 0.3s ease-out;
            }
            
            @keyframes hover-pulse {
                0% { transform: translateY(-2px); }
                100% { transform: translateY(-2px); }
            }
            
            .click-effect {
                animation: click-bounce 0.2s ease-out;
            }
            
            @keyframes click-bounce {
                0% { transform: scale(1); }
                50% { transform: scale(0.98); }
                100% { transform: scale(1); }
            }
            
            .select2-result-item {
                display: flex;
                align-items: center;
                padding: 8px 0;
            }
            
            .option-content {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .option-icon {
                font-size: 18px;
                filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
            }
            
            .option-text {
                font-weight: 500;
                color: #374151;
            }
            
            .select2-results__option--highlighted .option-text {
                color: white;
            }
            
            .select2-results__option[aria-selected=true] .option-text {
                color: white;
            }
            
            .custom-scrollbar {
                scrollbar-width: thin;
                scrollbar-color: #3b82f6 #f1f5f9;
            }
        `)
        .appendTo('head');

    // Balance Filter Functions
    function closeBalanceDropdown() {
        $('#balanceDropdown').removeClass('show');
    }

    function updateBalanceDropdownContent() {
        const isFilterActive = $('#balanceFilterBtn').hasClass('active');
        const totalResults = $('#totalBalanceResults').text();

        if (isFilterActive) {
            $('.summary-value').first().text('Balance > 0');
            $('#totalBalanceResults').text(totalResults);
        } else {
            $('.summary-value').first().text('No filter applied');
            $('#totalBalanceResults').text('All riders');
        }
    }

    function exportBalanceResults() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('balance_filter', 'greater_than_zero');
        currentUrl.pathname = currentUrl.pathname.replace('/riders', '/rider/exportCustomizableRiders');

        // Show loading state
        showNotification('Preparing export...', 'info');

        // Redirect to export
        window.open(currentUrl.toString(), '_blank');
    }

    function clearBalanceFilter() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('balance_filter');

        // Show notification
        showNotification('Balance filter cleared', 'success');

        // Redirect to clear filter
        window.location.href = currentUrl.toString();
    }

    // Enhanced notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="ti ti-${type === 'success' ? 'check' : type === 'error' ? 'x' : 'info'}"></i>
                <span>${message}</span>
            </div>
        `;

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            info: '#3b82f6'
        };

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type] || '#3b82f6'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 300px;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
</script>
@endsection