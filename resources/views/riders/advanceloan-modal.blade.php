<script src="{{ asset('js/modal_custom.js') }}"></script>

<div class="modal modal-default filtetmodal fade" id="advanceloanModal" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-top modal-full-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advance Loan - {{ $rider->name }} ({{ $rider->rider_id }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" action="{{ route('VisaExpense.payfine') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rider_id" value="{{ $rider->id }}">
                    <input type="hidden" name="trans_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="trans_code" value="{{ \App\Helpers\Account::trans_code() }}">
                    <input type="hidden" name="billing_month" value="{{ date('Y-m') }}">
                    <input type="hidden" name="payment_type" value="expense">
                    <input type="hidden" name="voucher_type" value="LV">
                    <input type="hidden" name="Created_By" value="{{ Auth::user()->id }}">

                    <div class="row">
                        <!-- Date Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Date:</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Billing Month Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Billing Month:</label>
                            <input type="month" name="billing_month" class="form-control" value="{{ date('Y-m') }}" required>
                        </div>

                        <!-- Loan Type Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Loan Type:</label>
                            <select class="form-control select2" id="loan_type" name="loan_type" required>
                                <option value="">Select Loan Type</option>
                                <option value="Advance Salary">Advance Salary</option>
                                <option value="Emergency Loan">Emergency Loan</option>
                                <option value="Bike Loan">Bike Loan</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Payment Status Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Payment Status:</label>
                            <select class="form-control select2" id="payment_status" name="payment_status" required>
                                <option value="">Select Payment Status</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid" selected>Unpaid</option>
                            </select>
                        </div>

                        <!-- Debit Account Field -->
                        <div class="form-group col-sm-6">
                            <label class="readonly">Debit Account:</label>
                            <select class="form-control select2" id="rider_account" name="rider_account" readonly>
                                <option value="{{ $account->id ?? '' }}">{{ $account->name ?? 'Rider Account' }}</option>
                            </select>
                        </div>

                        <!-- Credit Account Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Credit Account:</label>
                            <select class="form-control select2" id="account_id" name="account" required>
                                <option value="">Select Credit Account</option>
                                @php
                                $bank = DB::table('accounts')->where('name', 'Bank')->first();
                                $cash = DB::table('accounts')->where('name', 'Cash in Hand')->first();
                                @endphp

                                @foreach(DB::table('accounts')
                                ->where('status', 1)
                                ->whereIn('parent_id', [$bank->id ?? 0, $cash->id ?? 0])
                                ->orderBy('id', 'asc')
                                ->get() as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Amount:</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Enter amount" required>
                        </div>

                        <!-- Repayment Period Field -->
                        <div class="form-group col-sm-6">
                            <label>Repayment Period (Months):</label>
                            <input type="number" name="repayment_period" class="form-control" placeholder="Enter repayment period" min="1" max="60">
                        </div>

                        <!-- Attachment Field -->
                        <div class="form-group col-sm-6">
                            <label class="required">Attachment</label>
                            <input type="file" name="attach_file" class="form-control" required>
                        </div>

                        <!-- Detail Field -->
                        <div class="form-group col-sm-12">
                            <label>Detail:</label>
                            <textarea name="detail" class="form-control" maxlength="500" rows="3" placeholder="Enter details about the advance loan"></textarea>
                        </div>

                        <div class="col-md-12 form-group text-center">
                            <button type="submit" class="btn btn-primary pull-right mt-3">
                                <i class="fa fa-save mx-2"></i> Submit Advance Loan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2({
            dropdownParent: $('#advanceloanModal'),
            allowClear: true
        });

        // Show modal when page loads
        $('#advanceloanModal').modal('show');

        // Handle modal close
        $('#advanceloanModal').on('hidden.bs.modal', function() {
            // Close the modal and go back
            window.history.back();
        });
    });
</script>
