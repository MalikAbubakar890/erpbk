<?php $__env->startSection('title','Riders'); ?>

<?php $__env->startPush('third_party_stylesheets'); ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link rel="stylesheet" href="<?php echo e(asset('css/riders-styles.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
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
            <div class="fleet-supervisor-accordion expanded" id="fleetSupervisorAccordion">
                <div class="fleet-supervisor-slider-container">
                    <div class="slider-controls">
                        <button class="slider-btn prev-btn" id="prevBtn" type="button">
                            <i class="ti ti-chevron-left"></i>
                        </button>
                        <div class="slider-indicators" id="sliderIndicators"></div>
                        <button class="slider-btn next-btn" id="nextBtn" type="button">
                            <i class="ti ti-chevron-right"></i>
                        </button>
                    </div>
                    <div class="fleet-supervisor-cards slider-track" id="sliderTrack">
                        <?php
                        $dropdown = DB::table('dropdowns')->where('label', 'Fleet Supervisor')->first();
                        $fleetSupervisors = $dropdown && $dropdown->values ? json_decode($dropdown->values, true) : [];
                        ?>

                        <?php $__currentLoopData = $fleetSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $fleet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($fleet == request('fleet_supervisor') ? route('riders.index') : request()->fullUrlWithQuery(['fleet_supervisor' => $fleet])); ?>"
                            class="fleet-supervisor-card <?php if($fleet == request('fleet_supervisor')): ?> active <?php endif; ?>"
                            data-slide="<?php echo e($index); ?>">
                            <h3 class="fleet-supervisor-name"><?php echo e($fleet); ?></h3>
                            <div class="fleet-supervisor-stats">
                                <div class="fleet-stat active">
                                    <i class="fleet-stat-icon ti ti-user-check"></i>
                                    <span class="fleet-stat-label">Active</span>
                                    <span class="fleet-stat-value"><?php echo e(\App\Models\Riders::where('fleet_supervisor', $fleet)->where('status', 1)->count()); ?></span>
                                </div>
                                <div class="fleet-stat inactive">
                                    <i class="fleet-stat-icon ti ti-user-x"></i>
                                    <span class="fleet-stat-label">Inactive</span>
                                    <span class="fleet-stat-value"><?php echo e(\App\Models\Riders::where('fleet_supervisor', $fleet)->where('status', 3)->count()); ?></span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fleet Supervisor Slider Script -->
        <script>
            // Fleet Supervisor Slider with boundary checking
            setTimeout(function() {
                console.log('Initializing fleet supervisor slider...');
                const sliderTrack = document.getElementById('sliderTrack');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');

                if (sliderTrack && prevBtn && nextBtn) {
                    console.log('All elements found, initializing slider...');

                    const cards = sliderTrack.querySelectorAll('.fleet-supervisor-card');
                    const totalCards = cards.length;
                    console.log('Found', totalCards, 'cards');

                    if (totalCards === 0) {
                        console.log('No cards found');
                        return;
                    }

                    let currentIndex = 0;
                    const cardWidth = 296; // 280px card width + 16px gap
                    const maxIndex = totalCards - 1;

                    // Update button states based on current position
                    function updateButtonStates() {
                        // Disable prev button if at first card
                        if (currentIndex === 0) {
                            prevBtn.style.opacity = '0.5';
                            prevBtn.style.pointerEvents = 'none';
                            prevBtn.disabled = true;
                        } else {
                            prevBtn.style.opacity = '1';
                            prevBtn.style.pointerEvents = 'auto';
                            prevBtn.disabled = false;
                        }

                        // Disable next button if at last card
                        if (currentIndex >= maxIndex) {
                            nextBtn.style.opacity = '0.5';
                            nextBtn.style.pointerEvents = 'none';
                            nextBtn.disabled = true;
                        } else {
                            nextBtn.style.opacity = '1';
                            nextBtn.style.pointerEvents = 'auto';
                            nextBtn.disabled = false;
                        }

                        console.log('Current index:', currentIndex, 'Max index:', maxIndex);
                    }

                    // Update slider position
                    function updateSlider() {
                        const translateX = -currentIndex * cardWidth;
                        sliderTrack.style.transform = `translateX(${translateX}px)`;
                        console.log('Moving to card', currentIndex, 'translateX:', translateX);
                        updateButtonStates();
                    }

                    // Next slide - move to next card
                    function nextSlide() {
                        if (currentIndex < maxIndex) {
                            currentIndex++;
                            updateSlider();
                        } else {
                            console.log('Already at last card, cannot go forward');
                        }
                    }

                    // Previous slide - move to previous card
                    function prevSlide() {
                        if (currentIndex > 0) {
                            currentIndex--;
                            updateSlider();
                        } else {
                            console.log('Already at first card, cannot go backward');
                        }
                    }

                    // Add click handlers with boundary checking
                    nextBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Next button clicked, current index:', currentIndex);
                        nextSlide();
                    });

                    prevBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Prev button clicked, current index:', currentIndex);
                        prevSlide();
                    });

                    // Initialize slider
                    updateSlider();

                    console.log('Fleet supervisor slider initialized successfully!');
                    console.log('Total cards:', totalCards, 'Max index:', maxIndex);

                } else {
                    console.log('Missing elements:', {
                        sliderTrack: !!sliderTrack,
                        prevBtn: !!prevBtn,
                        nextBtn: !!nextBtn
                    });
                }
            }, 500);
        </script>
        <!-- Filter Tabs Section -->
        <div class="filter-tabs-section mb-4">
            <div class="container-fluid d-flex justify-content-between">
                <?php
                $activeFiltersCount = count(request('rider_status', [])) + (request('balance_filter') ? 1 : 0);

                // Helper function to toggle rider status in URL
                function toggleRiderStatus($status) {
                $currentStatuses = request('rider_status', []);
                $newStatuses = $currentStatuses;

                if (in_array($status, $currentStatuses)) {
                // Remove the status
                $newStatuses = array_diff($currentStatuses, [$status]);
                } else {
                // Add the status
                $newStatuses[] = $status;
                }

                $queryParams = request()->query();
                $queryParams['rider_status'] = array_values($newStatuses);

                return request()->fullUrlWithQuery($queryParams);
                }

                // Helper function to toggle balance filter in URL
                function toggleBalanceFilter() {
                $queryParams = request()->query();

                if (request('balance_filter') == 'greater_than_zero') {
                unset($queryParams['balance_filter']);
                } else {
                $queryParams['balance_filter'] = 'greater_than_zero';
                }

                return request()->fullUrlWithQuery($queryParams);
                }
                ?>


                <div class="filter-tabs">
                    <?php if($activeFiltersCount > 0): ?>
                    <div class="filter-status">
                        <div class="filter-info">
                            <i class="ti ti-filter"></i>
                            <span><?php echo e($activeFiltersCount); ?> filter<?php echo e($activeFiltersCount > 1 ? 's' : ''); ?> applied</span>
                            <a href="<?php echo e(route('riders.index')); ?>" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="ti ti-x"></i>
                                Clear All
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <a href="<?php echo e(route('riders.index')); ?>" class="filter-tab <?php echo e(!request('rider_status') && !request('balance_filter') ? 'active' : ''); ?>">
                        <i class="ti ti-users"></i>
                        All Riders
                    </a>
                    <a href="<?php echo e(toggleRiderStatus('absconder')); ?>" class="filter-tab <?php echo e(in_array('absconder', request('rider_status', [])) ? 'active' : ''); ?>">
                        <i class="ti ti-user-x"></i>
                        Absconder
                    </a>
                    <a href="<?php echo e(toggleRiderStatus('llicense')); ?>" class="filter-tab <?php echo e(in_array('llicense', request('rider_status', [])) ? 'active' : ''); ?>">
                        <i class="ti ti-license"></i>
                        Learning License
                    </a>
                    <a href="<?php echo e(toggleRiderStatus('followup')); ?>" class="filter-tab <?php echo e(in_array('followup', request('rider_status', [])) ? 'active' : ''); ?>">
                        <i class="ti ti-phone-call"></i>
                        Follow Up
                    </a>
                    <a href="<?php echo e(toggleBalanceFilter()); ?>" class="filter-tab <?php echo e(request('balance_filter') == 'greater_than_zero' ? 'active' : ''); ?>">
                        <i class="ti ti-cash"></i>
                        Recovery (<?php echo e(\App\Models\Riders::whereHas('account', function($q) { $q->whereRaw('(SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) FROM transactions WHERE account_id = accounts.id) > 0'); })->count()); ?>)
                    </a>
                </div>
                <div class="action-buttons">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rider_create')): ?>
                    <div class="action-dropdown-container">
                        <button class="action-dropdown-btn" id="addRiderDropdownBtn">
                            <i class="ti ti-plus"></i>
                            <span>Add Rider</span>
                            <i class="ti ti-chevron-down"></i>
                        </button>
                        <div class="action-dropdown-menu" id="addRiderDropdown">
                            <a class="action-dropdown-item" href="<?php echo e(route('riders.create')); ?>">
                                <i class="ti ti-user-plus"></i>
                                <div>
                                    <div class="action-dropdown-item-text">Create New Rider</div>
                                    <div class="action-dropdown-item-desc">Add a new rider to the system</div>
                                </div>
                            </a>
                            <a class="action-dropdown-item show-modal" href="javascript:void(0);" data-size="sm" data-title="Import Today Attendance" data-action="<?php echo e(route('rider.attendance_import')); ?>">
                                <i class="ti ti-calendar-check"></i>
                                <div>
                                    <div class="action-dropdown-item-text">Import Today Attendance</div>
                                    <div class="action-dropdown-item-desc">Import attendance data for today</div>
                                </div>
                            </a>
                            <a class="action-dropdown-item show-modal" href="javascript:void(0);" data-size="sm" data-title="Import Rider Activities" data-action="<?php echo e(route('rider.activities_import')); ?>">
                                <i class="ti ti-activity"></i>
                                <div>
                                    <div class="action-dropdown-item-text">Import Activities</div>
                                    <div class="action-dropdown-item-desc">Import rider activity data</div>
                                </div>
                            </a>
                            <a class="action-dropdown-item" href="<?php echo e(route('rider.exportRiders')); ?>">
                                <i class="ti ti-file-export"></i>
                                <div>
                                    <div class="action-dropdown-item-text">Export Riders</div>
                                    <div class="action-dropdown-item-desc">Export rider data to Excel</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div id="filterSidebar" class="filter-sidebar" style="z-index: 1111;">
            <div class="filter-header">
                <h5>Filter Riders</h5>
                <button type="button" class="btn-close" id="closeSidebar"></button>
            </div>
            <div class="filter-body" id="searchTopbody">
                <form id="filterForm" action="<?php echo e(route('riders.index')); ?>" method="GET">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="id">Rider Id</label>
                            <input type="number" name="rider_id" class="form-control" placeholder="Filter By Rider ID" value="<?php echo e(request('rider_id')); ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name">Rider Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Filter By Name" value="<?php echo e(request('name')); ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="fleet_supervisor">Filter by Fleet SuperVisor</label>
                            <select class="form-control " id="fleet_supervisor" name="fleet_supervisor">
                                <?php
                                $supervisorRow = DB::table('dropdowns')
                                ->where('label', 'Fleet Supervisor')
                                ->whereNotNull('values')
                                ->first();
                                $fleetSupervisors = [];
                                if ($supervisorRow && $supervisorRow->values) {
                                $fleetSupervisors = json_decode($supervisorRow->values, true);
                                }
                                ?>
                                <option value="" selected>Select</option>
                                <?php $__currentLoopData = $fleetSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supervisor); ?>" <?php echo e(request('fleet_supervisor') == $supervisor ? 'selected' : ''); ?>><?php echo e($supervisor); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="hub">Filter by HUB</label>
                            <select class="form-control " id="hub" name="hub">
                                <?php
                                $emirateHubs = DB::table('riders')
                                ->whereNotNull('designation')
                                ->where('designation', '!=', '')
                                ->select('designation')
                                ->distinct()
                                ->pluck('designation');
                                ?>
                                <option value="" selected>Select</option>
                                <?php $__currentLoopData = $emirateHubs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hub); ?>" <?php echo e(request('hub') == $hub ? 'selected' : ''); ?>><?php echo e($hub); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="customer_id">Filter by Customer</label>
                            <select class="form-control " id="customer_id" name="customer_id">
                                <?php
                                $customerIds = DB::table('riders')
                                ->whereNotNull('customer_id')
                                ->where('customer_id', '!=', '')
                                ->pluck('customer_id')
                                ->unique();

                                $customers = DB::table('customers')
                                ->whereIn('id', $customerIds)
                                ->select('id', 'name')
                                ->get();
                                ?>
                                <option value="" selected>Select</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>" <?php echo e(request('customer_id') == $customer->id ? 'selected' : ''); ?>><?php echo e($customer->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="bike">Bike Number</label>
                            <input type="text" name="branded_plate_no" value="<?php echo e(request('bike')); ?>" class="form-control" placeholder="Filter By Bike Number">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="designation">Filter by Designation</label>
                            <select class="form-control " id="designation" name="designation">
                                <?php
                                $emiratedesignation = DB::table('riders')
                                ->whereNotNull('designation')
                                ->where('designation', '!=', '')
                                ->select('designation')
                                ->distinct()
                                ->pluck('designation');
                                ?>
                                <option value="" selected>Select</option>
                                <?php $__currentLoopData = $emiratedesignation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $des): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($des); ?>" <?php echo e(request('designation') == $des ? 'selected' : ''); ?>><?php echo e($des); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="attandence">Filter by Attandence</label>
                            <select class="form-control " id="attendance" name="attendance">
                                <?php
                                $attandence = DB::table('riders')
                                ->whereNotNull('attendance')
                                ->where('attendance', '!=', '')
                                ->select('attendance')
                                ->distinct()
                                ->pluck('attendance');
                                ?>
                                <option value="" selected>Select</option>
                                <?php $__currentLoopData = $attandence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($att); ?>" <?php echo e(request('attandence') == $att ? 'selected' : ''); ?>><?php echo e($att); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="bike_assignment_status">Filter by Bike Assignment</label>
                            <select class="form-control " id="bike_assignment_status" name="bike_assignment_status">
                                <option value="" selected>Select</option>
                                <option value="Active" <?php echo e(request('bike_assignment_status') == 'Active' ? 'selected' : ''); ?>>Active</option>
                                <option value="Inactive" <?php echo e(request('bike_assignment_status') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="quick_search">Quick Search</label>
                            <input type="text" name="quick_search" id="quickSearchSidebar" class="form-control" placeholder="Quick Search..." value="<?php echo e(request('quick_search')); ?>">
                        </div>
                        <div class="col-md-12 form-group text-center">
                            <button type="submit" class="btn btn-primary pull-right mt-3"><i class="fa fa-filter mx-2"></i> Filter Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Filter Overlay -->
        <div id="filterOverlay" class="filter-overlay"></div>
</section>

<?php
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
?>

<?php echo $__env->make('components.column-control-panel', [
'tableColumns' => $tableColumns,
'exportRoute' => route('rider.exportCustomizableRiders'),
'tableIdentifier' => 'riders_table'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="content px-0">
    <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title">
                <h3>Riders</h3>
            </div>
            <div class="card-search">
                <input type="text" id="quickSearch" name="quick_search" class="form-control" placeholder="Quick Search..." value="<?php echo e(request('quick_search')); ?>">
            </div>
        </div>
        <div class="card-body table-responsive px-2 py-0" id="table-data">
            <div class="riders-table-container">
                <?php echo $__env->make('riders.table', ['data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="filter-loading-overlay" style="display: none;">
                <div class="filter-loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Applying filters...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
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
        // Filter sidebar functionality
        $(document).on('click', '#openFilterSidebar, .openFilterSidebar', function(e) {
            e.preventDefault();
            console.log('Filter button clicked!'); // Debug line
            $('#filterSidebar').addClass('open');
            $('#filterOverlay').addClass('show');
            return false;
        });

        $('#closeSidebar, #filterOverlay').on('click', function() {
            $('#filterSidebar').removeClass('open');
            $('#filterOverlay').removeClass('show');
        });
        $('#filterForm').on('submit', function(e) {
            // Let the form submit naturally - no need to prevent default
            $('#filterSidebar').removeClass('open');
            $('#filterOverlay').removeClass('show');
        });
        // Quick search input (main) - redirect to URL with search parameter
        $('#quickSearch').on('keyup', function(e) {
            if (e.keyCode === 13 || $(this).val().length === 0) {
                const searchValue = $(this).val();
                const url = new URL(window.location);

                if (searchValue) {
                    url.searchParams.set('quick_search', searchValue);
                } else {
                    url.searchParams.delete('quick_search');
                }

                window.location.href = url.toString();
            }
        });

        // Quick search input (sidebar) - redirect to URL with search parameter
        $('#quickSearchSidebar').on('keyup', function(e) {
            if (e.keyCode === 13 || $(this).val().length === 0) {
                const searchValue = $(this).val();
                const url = new URL(window.location);

                if (searchValue) {
                    url.searchParams.set('quick_search', searchValue);
                } else {
                    url.searchParams.delete('quick_search');
                }

                window.location.href = url.toString();
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

        // Fleet supervisor and balance filter cards now use direct links - no JavaScript needed
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

    // Status filter functionality is now handled by direct URL links

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

    // Balance filter functions are now handled by direct URL links

    // All filter functions are now handled by direct URL links - no JavaScript needed

    // Simple Fleet Supervisor Slider
    function initFleetSupervisorSlider() {
        console.log('Initializing slider...');

        const sliderTrack = document.getElementById('sliderTrack');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const indicatorsContainer = document.getElementById('sliderIndicators');

        if (!sliderTrack) {
            console.log('sliderTrack not found');
            return;
        }

        if (!prevBtn) {
            console.log('prevBtn not found');
            return;
        }

        if (!nextBtn) {
            console.log('nextBtn not found');
            return;
        }

        if (!indicatorsContainer) {
            console.log('indicatorsContainer not found');
            return;
        }

        const cards = sliderTrack.querySelectorAll('.fleet-supervisor-card');
        console.log('Found cards:', cards.length);

        if (cards.length === 0) {
            console.log('No cards found');
            return;
        }

        let currentIndex = 0;

        // Simple next function
        function nextSlide() {
            if (currentIndex < cards.length - 1) {
                currentIndex++;
                updateSlider();
            }
        }

        // Simple prev function
        function prevSlide() {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        }

        // Update slider
        function updateSlider() {
            const translateX = -currentIndex * 300; // 300px per slide
            sliderTrack.style.transform = `translateX(${translateX}px)`;

            // Update buttons
            prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
            nextBtn.style.opacity = currentIndex >= cards.length - 1 ? '0.5' : '1';
        }

        // Event listeners
        nextBtn.onclick = function() {
            nextSlide();
        };

        prevBtn.onclick = function() {
            prevSlide();
        };

        // Initialize
        updateSlider();
    }

    // Initialize slider when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing slider...');
        setTimeout(initFleetSupervisorSlider, 100);
    });

    // Also try to initialize when window loads
    window.addEventListener('load', function() {
        console.log('Window loaded, initializing slider...');
        setTimeout(initFleetSupervisorSlider, 100);
    });

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/riders/index.blade.php ENDPATH**/ ?>