@extends('layouts.app')

@section('title', 'Import Bikes')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Import Bikes</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('bikes.index') }}">Bikes</a></li>
                    <li class="breadcrumb-item active">Import</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        @include('flash::message')

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Import Bikes from Excel</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('bikes.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="file">Select Excel File</label>
                                <input type="file"
                                    class="form-control @error('file') is-invalid @enderror"
                                    id="file"
                                    name="file"
                                    accept=".xlsx,.xls"
                                    required>
                                @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Only Excel files (.xlsx, .xls) are allowed. Maximum file size: 50MB
                                </small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Import Bikes
                                </button>
                                <a href="{{ route('bikes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Bikes
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if(session()->has('import_errors'))
                <div class="card mt-3">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">Import Errors</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Import completed with errors:</strong>
                        </div>
                        <ul class="list-group">
                            @foreach(session('import_errors') as $error)
                            <li class="list-group-item text-danger">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Instructions</h3>
                    </div>
                    <div class="card-body">
                        <h5>Required Fields:</h5>
                        <ul class="list-unstyled">
                            <li><strong>Plate Number</strong> - Must be unique</li>
                        </ul>

                        <h5>Optional Fields:</h5>
                        <ul class="list-unstyled">
                            <li>Vehicle Type</li>
                            <li>Chassis Number</li>
                            <li>Color</li>
                            <li>Model</li>
                            <li>Model Type</li>
                            <li>Engine</li>
                            <li>Bike Code</li>
                            <li>Emirates</li>
                            <li>Warehouse</li>
                            <li>Status (1 = Active, 0 = Inactive)</li>
                            <li>Registration Date</li>
                            <li>Expiry Date</li>
                            <li>Insurance Expiry</li>
                            <li>Insurance Company</li>
                            <li>Policy Number</li>
                            <li>Contract Number</li>
                            <li>Traffic File Number</li>
                            <li>Rider Name (will match with existing riders)</li>
                            <li>Company Name (will match with existing companies)</li>
                            <li>Customer Name (will match with existing customers)</li>
                            <li>Notes</li>
                        </ul>

                        <div class="mt-3">
                            <a href="{{ route('bikes.download-template') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> Download Sample Template
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Important Notes</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <ul class="mb-0">
                                <li>Make sure to use the exact column headers as shown in the template</li>
                                <li>Dates should be in YYYY-MM-DD format</li>
                                <li>Status should be 1 for Active or 0 for Inactive</li>
                                <li>If a bike with the same plate number exists, it will be updated</li>
                                <li>Rider, Company, and Customer names will be matched with existing records</li>
                                <li>Empty rows will be skipped</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('third_party_stylesheets')
<style>
    .card-header.bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .list-group-item.text-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-info ul {
        margin-bottom: 0;
    }
</style>
@endpush