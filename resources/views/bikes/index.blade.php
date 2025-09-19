@extends('layouts.app')

@section('title','Vehicals')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Vehicals</h3>
            </div>
            <div class="col-sm-6">
                @can('item_create')
                <a class="btn btn-primary action-btn show-modal"
                    href="javascript:void(0);" data-size="xl" data-title="Add New Vehical" data-action="{{ route('bikes.create') }}">
                    Add New
                </a>
                @endcan
                <div class="modal modal-default filtetmodal fade" id="searchModal" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-slide-top modal-full-top">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Filter Vehicals</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="searchTopbody">
                                <form id="filterForm" action="{{ route('bikes.index') }}" method="GET">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="bike_code">Filter by Code</label>
                                            <select class="form-control " id="bike_code" name="bike_code">
                                                @php
                                                $bikecode = DB::table('bikes')
                                                ->whereNotNull('bike_code')
                                                ->where('bike_code', '!=', '')
                                                ->pluck('bike_code')
                                                ->unique();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($bikecode as $code)
                                                <option value="{{ $code }}" {{ request('bike_code') == $code ? 'selected' : '' }}>{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="name">Plate</label>
                                            <input type="text" name="plate" class="form-control" placeholder="Filter By Plate" value="{{ request('plate') }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="name">Rider ID</label>
                                            <input type="text" name="rider_id" class="form-control" placeholder="Filter By Rider ID" value="{{ request('rider_id') }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="rider">Filter by Rider</label>
                                            <select class="form-control " id="rider" name="rider">
                                                @php
                                                $riderid = DB::table('bikes')
                                                ->whereNotNull('rider_id')
                                                ->where('rider_id', '!=', '')
                                                ->pluck('rider_id')
                                                ->unique();
                                                $riders = DB::table('riders')
                                                ->whereIn('id', $riderid)
                                                ->select('rider_id','id', 'name')
                                                ->get();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($riders as $rider)
                                                <option value="{{ $rider->id }}" {{ request('rider_id') == $rider->rider_id ? 'selected' : '' }}>{{ $rider->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="company">Filter by Company</label>
                                            <select class="form-control " id="company" name="company">
                                                @php
                                                $companiesid = DB::table('bikes')
                                                ->whereNotNull('company')
                                                ->where('company', '!=', '')
                                                ->pluck('company')
                                                ->unique();
                                                $companies = DB::table('leasing_companies')
                                                ->whereIn('id', $companiesid)
                                                ->select('id', 'name')
                                                ->get();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="emirates">Filter by Emirates</label>
                                            <select class="form-control " id="emirates" name="emirates">
                                                @php
                                                $emirates = DB::table('bikes')
                                                ->whereNotNull('emirates')
                                                ->where('emirates', '!=', '')
                                                ->pluck('emirates')
                                                ->unique();
                                                @endphp
                                                <option value="" selected>Select</option>
                                                @foreach($emirates as $emirate)
                                                <option value="{{ $emirate }}" {{ request('emirates') == $emirate ? 'selected' : '' }}>{{ $emirate }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="expiry_date_from">Expiry Date From</label>
                                            <input type="date" name="expiry_date_from" class="form-control" placeholder="Filter By Expiry Date From" value="{{ request('expiry_date_from') }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="expiry_date_to">Expiry Date To</label>
                                            <input type="date" name="expiry_date_to" class="form-control" placeholder="Filter By Expiry Date To" value="{{ request('expiry_date_to') }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="status">Filter by Status</label>
                                            <select class="form-control " id="status" name="status">
                                                <option value="" selected>Select</option>
                                                <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="3" {{ request('status') == 3 ? 'selected' : '' }}>In Active</option>
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
            @include('bikes.table', ['data' => $data])
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
        $('#bike_code').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Bike Code",
            allowClear: true
        });
        $('#rider').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Name",
            allowClear: true
        });
        $('#rider').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Name",
            allowClear: true
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
                url: "{{ route('bikes.index') }}",
                type: "GET",
                data: formData,
                success: function(data) {
                    $('#table-data').html(data.tableData);

                    // Update URL
                    let newUrl = "{{ route('bikes.index') }}" + (formData ? '?' + formData : '');
                    history.pushState(null, '', newUrl);


                    // Ensure loader is visible at least 3s
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