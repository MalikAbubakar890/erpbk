@extends('layouts.app')

@section('title','Manual Invoice Payment')
@section('content')
<div style="display: none;" class="loading-overlay" id="loading-overlay">
    <div class="spinner-border text-primary" role="status"></div>
</div>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Manual Invoice Payment</h3>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary float-right" href="{{ route('riderInvoices.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Invoices
                </a>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Mark Rider Invoices as Paid</h3>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Filter by Rider:</label>
                    <select class="form-control" id="rider_filter" onchange="applyFilters()">
                        <option value="">All Riders</option>
                        @foreach($riders as $id => $name)
                        <option value="{{ $id }}" {{ request('rider_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Filter by Billing Month:</label>
                    <input type="month" class="form-control" id="billing_month_filter"
                        value="{{ request('billing_month') }}" onchange="applyFilters()">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
            </div>

            @if($unpaidInvoices->count() > 0)
            <form action="{{ route('riderInvoices.manualPayment') }}" method="POST" id="paymentForm">
                @csrf

                <!-- Payment Details -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="bank_account_id" class="required">Bank Account:</label>
                        <select name="bank_account_id" id="bank_account_id" class="form-control @error('bank_account_id') is-invalid @enderror" required>
                            <option value="">Select Bank Account</option>
                            @foreach($bankAccounts as $id => $name)
                            <option value="{{ $id }}" {{ old('bank_account_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="payment_date" class="required">Payment Date:</label>
                        <input type="date" name="payment_date" id="payment_date"
                            class="form-control @error('payment_date') is-invalid @enderror"
                            value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-info" onclick="selectAll()">Select All</button>
                            <button type="button" class="btn btn-warning" onclick="deselectAll()">Deselect All</button>
                        </div>
                    </div>
                </div>

                <!-- Selected Summary -->
                <div class="alert alert-info" id="selectionSummary" style="display: none;">
                    <strong>Selected:</strong> <span id="selectedCount">0</span> invoices,
                    <strong>Total Amount:</strong> <span id="selectedTotal">0.00</span>
                </div>

                <!-- Invoices Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleAll()">
                                </th>
                                <th>Invoice ID</th>
                                <th>Rider</th>
                                <th>Invoice Date</th>
                                <th>Billing Month</th>
                                <th>Amount</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaidInvoices as $invoice)
                            <tr>
                                <td>
                                    <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                        class="invoice-checkbox"
                                        data-amount="{{ $invoice->total_amount }}"
                                        onchange="updateSelection()">
                                </td>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->rider->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->inv_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->billing_month)->format('M Y') }}</td>
                                <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ $invoice->descriptions ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Submit Button -->
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                            <i class="fas fa-check-circle"></i> Mark Selected as Paid
                        </button>
                    </div>
                </div>
            </form>
            @else
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> No Unpaid Invoices Found</h5>
                <p>There are no unpaid invoices matching your current filters.</p>
                @if(request()->hasAny(['rider_id', 'billing_month']))
                <button type="button" class="btn btn-primary" onclick="clearFilters()">Clear Filters</button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        const riderId = document.getElementById('rider_filter').value;
        const billingMonth = document.getElementById('billing_month_filter').value;

        const url = new URL(window.location.href);

        if (riderId) {
            url.searchParams.set('rider_id', riderId);
        } else {
            url.searchParams.delete('rider_id');
        }

        if (billingMonth) {
            url.searchParams.set('billing_month', billingMonth);
        } else {
            url.searchParams.delete('billing_month');
        }

        window.location.href = url.toString();
    }

    function clearFilters() {
        window.location.href = "{{ route('riderInvoices.manualPayment') }}";
    }

    function selectAll() {
        const checkboxes = document.querySelectorAll('.invoice-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        document.getElementById('selectAllCheckbox').checked = true;
        updateSelection();
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.invoice-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateSelection();
    }

    function toggleAll() {
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.invoice-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    }

    function updateSelection() {
        const checkedBoxes = document.querySelectorAll('.invoice-checkbox:checked');
        const count = checkedBoxes.length;
        let total = 0;

        checkedBoxes.forEach(checkbox => {
            total += parseFloat(checkbox.dataset.amount || 0);
        });

        document.getElementById('selectedCount').textContent = count;
        document.getElementById('selectedTotal').textContent = total.toFixed(2);

        const summary = document.getElementById('selectionSummary');
        const submitBtn = document.getElementById('submitBtn');

        if (count > 0) {
            summary.style.display = 'block';
            submitBtn.disabled = false;
        } else {
            summary.style.display = 'none';
            submitBtn.disabled = true;
        }

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.invoice-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === allCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.invoice-checkbox:checked');
        const bankAccount = document.getElementById('bank_account_id').value;
        const paymentDate = document.getElementById('payment_date').value;

        if (checkedBoxes.length === 0) {
            alert('Please select at least one invoice to mark as paid.');
            e.preventDefault();
            return;
        }

        if (!bankAccount) {
            alert('Please select a bank account.');
            e.preventDefault();
            return;
        }

        if (!paymentDate) {
            alert('Please select a payment date.');
            e.preventDefault();
            return;
        }

        if (!confirm(`Are you sure you want to mark ${checkedBoxes.length} invoice(s) as paid?`)) {
            e.preventDefault();
            return;
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateSelection();
    });
</script>

<style>
    .required::after {
        content: " *";
        color: red;
    }
</style>

@endsection