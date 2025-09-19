@extends('layouts.app')

@section('title','Rider Invoices')
@section('content')
<div style="display: none;" class="loading-overlay" id="loading-overlay">
    <div class="spinner-border text-primary" role="status"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Rider Invoices</h3>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-success action-btn show-modal mx-2"
                    href="javascript:void(0);" data-size="sm" data-title="Import Rider Invoices" data-action="{{ route('rider.invoice_import') }}">
                    Import Invoices
                </a>

                <a class="btn btn-primary action-btn show-modal"
                    href="javascript:void(0);" data-size="xl" data-title="Create Rider Invoice" data-action="{{ route('riderInvoices.create') }}">
                    Create Invoice
                </a>
                <div class="modal modal-default filtetmodal fade" id="searchModal" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-slide-top modal-full-top">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Filter Rider Invoice</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="searchTopbody">
                                <form id="filterForm" action="{{ route('riderInvoices.index') }}" method="GET">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="name">ID</label>
                                            <input type="text" name="id" class="form-control" placeholder="Filter By ID" value="{{ request('id') }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="rider_id">Filter by Rider</label>
                                            <select class="form-control " id="rider_id" name="rider_id">
                                                @php
                                                $riderid = DB::table('rider_invoices')
                                                ->whereNotNull('rider_id')
                                                ->where('rider_id', '!=', '')
                                                ->pluck('rider_id')
                                                ->unique();
                                                $riders = DB::table('riders')
                                                ->whereIn('id', $riderid)
                                                ->select('rider_id', 'name', 'id')
                                                ->get();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($riders as $rider)
                                                <option value="{{ $rider->id }}" {{ request('rider_id') == $rider->id ? 'selected' : '' }}>{{ $rider->rider_id . '-' . $rider->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="billing_month">Billing Month</label>
                                            <input type="month" name="billing_month" class="form-control" placeholder="Filter By Billing Month" value="{{ request('billing_month') }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="vendor_id">Filter by Vendors</label>
                                            <select class="form-control " id="vendor_id" name="vendor_id">
                                                @php
                                                $vendorid = DB::table('rider_invoices')
                                                ->whereNotNull('vendor_id')
                                                ->where('vendor_id', '!=', '')
                                                ->pluck('vendor_id')
                                                ->unique();

                                                $vendors = DB::table('vendors')
                                                ->whereIn('id', $vendorid)
                                                ->select('id', 'name')
                                                ->get();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="zone">Filter by Zone</label>
                                            <select class="form-control " id="zone" name="zone">
                                                @php
                                                $zones = DB::table('rider_invoices')
                                                ->whereNotNull('zone')
                                                ->where('zone', '!=', '')
                                                ->pluck('zone')
                                                ->unique();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($zones as $zone)
                                                <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="performance">Filter by Performance</label>
                                            <select class="form-control " id="performance" name="performance">
                                                @php
                                                $performances = DB::table('rider_invoices')
                                                ->whereNotNull('performance')
                                                ->where('performance', '!=', '')
                                                ->pluck('performance')
                                                ->unique();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($performances as $performance)
                                                <option value="{{ $performance }}" {{ request('performance') == $performance ? 'selected' : '' }}>{{ $performance}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="status">Filter by Status</label>
                                            <select class="form-control " id="status" name="status">
                                                <option value="" selected>Select</option>
                                                <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Paid</option>
                                                <option value="0" {{ request('status') == 0 ? 'selected' : '' }}>Unpaid</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <button type="submit" class="btn btn-primary pull-right mt-3"><i class="fa fa-filter mx-2"></i> Filter Data</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="content px-3">
    @include('flash::message')
    <div class="clearfix"></div>
    <div class="card">
        <div class="card-body table-responsive px-2 py-0" id="table-data">
            @include('rider_invoices.table', [
            'data' => $data,
            'currentMonthTotal' => $currentMonthTotal
            ])
        </div>
    </div>

</div>
@endsection
@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function confirmDelete(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
    $(document).ready(function() {
        $('#rider_id').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Rider",
            allowClear: true, // ✅ cross icon enable
        });
        $('#billing_month').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Billing Month",
            allowClear: true, // ✅ cross icon enable
        });
        $('#vendor_id').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Vendor",
            allowClear: true, // ✅ cross icon enable
        });
        $('#zone').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Zone",
            allowClear: true, // ✅ cross icon enable
        });
        $('#performance').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Performance",
            allowClear: true, // ✅ cross icon enable
        });
        $('#status').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By status",
            allowClear: true, // ✅ cross icon enable
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();

            $('#loading-overlay').show();
            $('#searchModal').modal('hide');

            const loaderStartTime = Date.now();

            // Exclude _token and empty fields
            let filteredFields = $(this).serializeArray().filter(field => field.name !== '_token' && field.value.trim() !== '');
            let formData = $.param(filteredFields);

            $.ajax({
                url: "{{ route('riderInvoices.index') }}",
                type: "GET",
                data: formData,
                success: function(data) {
                    $('#table-data').html(data.tableData);

                    // 🔹 Update Current Month Total in header
                    if (data.currentMonthTotal !== undefined) {
                        $('#current-month-total').text('Current Month Total: ' + data.currentMonthTotal);
                    }

                    // Update URL
                    let newUrl = "{{ route('riderInvoices.index') }}" + (formData ? '?' + formData : '');
                    history.pushState(null, '', newUrl);

                    // Loader timing
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
</script>
@endsection