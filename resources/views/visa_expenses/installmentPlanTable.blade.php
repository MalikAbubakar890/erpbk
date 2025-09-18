@push('third_party_stylesheets')
@endpush
<table class="table table-striped dataTable no-footer" id="dataTableBuilder">
    <thead class="text-center">
        <tr role="row">
            <th title="Date" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending">Date</th>
            <th title="Billing Month" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Billing Month: activate to sort column ascending">Billing Month</th>
            <th title="Amount" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Amount: activate to sort column ascending">Amount</th>
            <th title="Status" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
            <th title="Created By" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Created By: activate to sort column ascending">Created By</th>
            <th title="Updated By" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" aria-label="Updated By: activate to sort column ascending">Updated By</th>
            <th title="Action" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Action">
                Action
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $installment)
        <tr class="text-center">
            <td>
                <span id="date_display_{{ $installment->id }}">{{ \Carbon\Carbon::parse($installment->date)->format('d M Y') }}</span>
                @if($installment->status === 'pending')
                <a href="javascript:void(0);" onclick="editDate({{ $installment->id }})" class="ms-2">
                    <i class="fa fa-edit text-primary"></i>
                </a>
                @endif
                <input type="date"
                    id="date_input_{{ $installment->id }}"
                    value="{{ \Carbon\Carbon::parse($installment->date)->format('Y-m-d') }}"
                    min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                    class="form-control form-control-sm d-none"
                    onblur="saveDate({{ $installment->id }})"
                    onkeypress="if(event.keyCode==13) saveDate({{ $installment->id }})">
            </td>
            <td>
                <span id="billing_display_{{ $installment->id }}">{{ \Carbon\Carbon::parse($installment->billing_month)->format('M Y') }}</span>
                @if($installment->status === 'pending')
                <a href="javascript:void(0);" onclick="editBillingMonth({{ $installment->id }})" class="ms-2">
                    <i class="fa fa-edit text-primary"></i>
                </a>
                @endif
                <input type="month"
                    id="billing_input_{{ $installment->id }}"
                    value="{{ \Carbon\Carbon::parse($installment->billing_month)->format('Y-m') }}"
                    min="{{ \Carbon\Carbon::today()->format('Y-m') }}"
                    class="form-control form-control-sm d-none"
                    onblur="saveBillingMonth({{ $installment->id }})"
                    onkeypress="if(event.keyCode==13) saveBillingMonth({{ $installment->id }})">
            </td>
            <td>
                <span id="amount_display_{{ $installment->id }}">{{ number_format($installment->amount, 2) }}</span>
                @if($installment->status === 'pending')
                <a href="javascript:void(0);" onclick="editAmount({{ $installment->id }})" class="ms-2">
                    <i class="fa fa-edit text-primary"></i>
                </a>
                @endif
                <input type="number"
                    step="0.01"
                    id="amount_input_{{ $installment->id }}"
                    value="{{ $installment->amount }}"
                    class="form-control form-control-sm d-none"
                    onblur="saveAmount({{ $installment->id }})"
                    onkeypress="if(event.keyCode==13) saveAmount({{ $installment->id }})">
            </td>
            <td>{!! $installment->status_badge !!}</td>
            <td>
                <span id="created_by_display_{{ $installment->id }}">{{ $installment->created_by ? \App\Models\User::find($installment->created_by)->name :''}}</span>
            </td>
            <td>
                <span id="updated_by_display_{{ $installment->id }}">{{ $installment->updated_by ? \App\Models\User::find($installment->updated_by)->name :''}}</span>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1 waves-effect" type="button" id="actiondropdown{{ $installment->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-base ti ti-dots icon-md text-body-secondary"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actiondropdown{{ $installment->id }}">
                        @if($installment->status === 'pending')
                        <a href="javascript:void(0);"
                            onclick="markAsPaid({{ $installment->id }})"
                            class='dropdown-item waves-effect'>
                            <i class="fa fa-check me-2"></i> Mark as Paid
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);"
                            onclick='confirmDelete("{{ route('VisaExpense.deleteInstallment', $installment->id) }}")'
                            class='dropdown-item waves-effect text-danger'>
                            <i class="fa fa-trash me-2"></i> Delete
                        </a>
                        @else
                        <span class="dropdown-item-text text-success">
                            <i class="fa fa-check me-2"></i> Paid
                        </span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted py-4">
                <i class="fa fa-info-circle me-2"></i>
                No installment plans found. <br>
                <small>Click "Create Installment Plan" to get started.</small>
            </td>
        </tr>
        @endforelse
    </tbody>
    @if($data->count() > 0)
    <tfoot>
        <tr class="bg-light">
            <td colspan="2" class="text-end"><strong>Total Amount Reference:</strong></td>
            <td class="text-center">
                <strong>
                    @php
                    $totalAmount = $data->first()->total_amount ?? 0;
                    $currentTotal = $data->sum('amount');
                    @endphp
                    {{ number_format($totalAmount, 2) }}
                    @if(abs($totalAmount - $currentTotal) > 0.01)
                    <br><small class="text-warning">(Current: {{ number_format($currentTotal, 2) }})</small>
                    @endif
                </strong>
            </td>
            <td colspan="4"></td>
        </tr>
    </tfoot>
    @endif
</table>
{!! $data->links('pagination') !!}

<script>
    function markAsPaid(installmentId) {
        if (confirm('Are you sure you want to mark this installment as paid?')) {
            submitForm('{{ route("VisaExpense.payInstallment") }}', {
                'installment_id': installmentId
            });
        }
    }

    // Edit functions - show input field
    function editDate(installmentId) {
        document.getElementById('date_display_' + installmentId).classList.add('d-none');
        document.getElementById('date_input_' + installmentId).classList.remove('d-none');
        document.getElementById('date_input_' + installmentId).focus();
    }

    function editBillingMonth(installmentId) {
        document.getElementById('billing_display_' + installmentId).classList.add('d-none');
        document.getElementById('billing_input_' + installmentId).classList.remove('d-none');
        document.getElementById('billing_input_' + installmentId).focus();
    }

    function editAmount(installmentId) {
        document.getElementById('amount_display_' + installmentId).classList.add('d-none');
        document.getElementById('amount_input_' + installmentId).classList.remove('d-none');
        document.getElementById('amount_input_' + installmentId).focus();
        document.getElementById('amount_input_' + installmentId).select();
    }

    // Save functions - hide input and save data
    function saveDate(installmentId) {
        const newValue = document.getElementById('date_input_' + installmentId).value;
        const originalValue = document.getElementById('date_input_' + installmentId).getAttribute('data-original') || '';

        if (newValue && newValue !== originalValue) {
            // Validate that the new date is not in the past
            const selectedDate = new Date(newValue);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                alert('You cannot select a date in the past.');
                document.getElementById('date_input_' + installmentId).value = originalValue;
                return;
            }

            if (confirm('Are you sure you want to update the date? This will also update subsequent installments, voucher and transactions.')) {
                submitForm('{{ route("VisaExpense.updateInstallmentField") }}', {
                    'installment_id': installmentId,
                    'field': 'date',
                    'value': newValue,
                    'update_subsequent': true
                });
                return;
            }
        }

        // Cancel edit
        document.getElementById('date_input_' + installmentId).classList.add('d-none');
        document.getElementById('date_display_' + installmentId).classList.remove('d-none');
    }

    function saveBillingMonth(installmentId) {
        const newValue = document.getElementById('billing_input_' + installmentId).value;
        const originalValue = document.getElementById('billing_input_' + installmentId).getAttribute('data-original') || '';

        if (newValue && newValue !== originalValue) {
            // Validate that the new billing month is not in the past
            const selectedDate = new Date(newValue + '-01');
            const today = new Date();
            const currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);

            if (selectedDate < currentMonth) {
                alert('You cannot select a billing month in the past.');
                document.getElementById('billing_input_' + installmentId).value = originalValue;
                return;
            }

            if (confirm('Are you sure you want to update the billing month? This will also update subsequent installments, voucher and transactions.')) {
                submitForm('{{ route("VisaExpense.updateInstallmentField") }}', {
                    'installment_id': installmentId,
                    'field': 'billing_month',
                    'value': newValue,
                    'update_subsequent': true
                });
                return;
            }
        }

        // Cancel edit
        document.getElementById('billing_input_' + installmentId).classList.add('d-none');
        document.getElementById('billing_display_' + installmentId).classList.remove('d-none');
    }

    function saveAmount(installmentId) {
        const newValue = document.getElementById('amount_input_' + installmentId).value;
        const originalValue = document.getElementById('amount_input_' + installmentId).getAttribute('data-original') || '';

        if (newValue && newValue !== originalValue) {
            // Validate amount is positive
            if (parseFloat(newValue) <= 0) {
                alert('Amount must be greater than 0.');
                document.getElementById('amount_input_' + installmentId).value = originalValue;
                return;
            }

            if (confirm('Are you sure you want to update the amount? This will automatically adjust the remaining installments to maintain the total amount balance.')) {
                // Get rider_id from the page context (assuming it's available)
                const riderId = '{{ $account->id ?? "" }}';

                if (!riderId) {
                    alert('Error: Rider ID not found. Please refresh the page and try again.');
                    return;
                }

                submitForm('{{ route("VisaExpense.recalculateInstallments") }}', {
                    'rider_id': riderId,
                    'edited_installment_id': installmentId,
                    'new_amount': newValue
                });
                return;
            }
        }

        // Cancel edit
        document.getElementById('amount_input_' + installmentId).classList.add('d-none');
        document.getElementById('amount_display_' + installmentId).classList.remove('d-none');
    }

    function submitForm(action, data) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add data
        for (const [key, value] of Object.entries(data)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }

        // Submit form
        document.body.appendChild(form);
        form.submit();
    }

    // Store original values when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const dateInputs = document.querySelectorAll('[id^="date_input_"]');
        const billingInputs = document.querySelectorAll('[id^="billing_input_"]');
        const amountInputs = document.querySelectorAll('[id^="amount_input_"]');

        dateInputs.forEach(input => {
            input.setAttribute('data-original', input.value);

            // Add event listener for date validation
            input.addEventListener('input', function() {
                validateDateInput(this);
            });
        });

        billingInputs.forEach(input => {
            input.setAttribute('data-original', input.value);

            // Add event listener for billing month validation
            input.addEventListener('input', function() {
                validateBillingMonthInput(this);
            });
        });

        amountInputs.forEach(input => {
            input.setAttribute('data-original', input.value);

            // Add event listener for amount validation
            input.addEventListener('input', function() {
                validateAmountInput(this);
            });
        });
    });

    // Client-side validation functions
    function validateDateInput(input) {
        const selectedDate = new Date(input.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            input.style.borderColor = '#dc3545';
            input.style.backgroundColor = '#f8d7da';
            input.title = 'Cannot select a date in the past';
        } else {
            input.style.borderColor = '#28a745';
            input.style.backgroundColor = '#d4edda';
            input.title = '';
        }
    }

    function validateBillingMonthInput(input) {
        const selectedDate = new Date(input.value + '-01');
        const today = new Date();
        const currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);

        if (selectedDate < currentMonth) {
            input.style.borderColor = '#dc3545';
            input.style.backgroundColor = '#f8d7da';
            input.title = 'Cannot select a billing month in the past';
        } else {
            input.style.borderColor = '#28a745';
            input.style.backgroundColor = '#d4edda';
            input.title = '';
        }
    }

    function validateAmountInput(input) {
        const amount = parseFloat(input.value);

        if (isNaN(amount) || amount <= 0) {
            input.style.borderColor = '#dc3545';
            input.style.backgroundColor = '#f8d7da';
            input.title = 'Amount must be greater than 0';
        } else {
            input.style.borderColor = '#28a745';
            input.style.backgroundColor = '#d4edda';
            input.title = '';
        }
    }
</script>

<div class="modal modal-default filtetmodal fade" id="customoizecolmn" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-top modal-full-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Riders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="searchTopbody">
                <div style="display: none;" class="loading-overlay" id="loading-overlay">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <form id="filterForm" action="{{ route('banks.index') }}" method="GET">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input type="number" name="search" class="form-control" placeholder="Search">
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