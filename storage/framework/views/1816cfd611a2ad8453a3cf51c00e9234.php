
<?php $__env->startSection('page_content'); ?>

<div class="modal modal-default filtetmodal fade" id="searchModal" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-top modal-full-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Banks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="searchTopbody">
                <form id="filterForm" action="<?php echo e(route('payments.index')); ?>" method="GET">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="bank_id">Bank ID</label>
                            <input type="text" name="bank_id" class="form-control" placeholder="Filter By Bank ID" value="<?php echo e(request('bank_id')); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="account_type">Filter by Account Type</label>
                            <select class="form-control" id="account_type" name="account_type">
                                <option value="" selected>Select</option>
                                <?php $__currentLoopData = \App\Helpers\Accounts::AccountTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type); ?>" <?php echo e(request('account_type') == $type ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" class="form-control" placeholder="Filter By Amount" value="<?php echo e(request('amount')); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date_of_payment">Date of Payment</label>
                            <input type="date" name="date_of_payment" class="form-control" value="<?php echo e(request('date_of_payment')); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="status">Filter by Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="" selected>Select</option>
                                <option value="1" <?php echo e(request('status') == 1 ? 'selected' : ''); ?>>Active</option>
                                <option value="0" <?php echo e(request('status') == 0 ? 'selected' : ''); ?>>Inactive</option>
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
<div class="content px-3">
    <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="clearfix"></div>

    <div class="card">
        <div class="card-body table-responsive px-2 py-0" id="table-data">
            <?php echo $__env->make('payments.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
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
        $('#account_type').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Account Type",
            allowClear: true
        });
        $('#branch').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By Branch",
            allowClear: true
        });
        $('#status').select2({
            dropdownParent: $('#searchTopbody'),
            placeholder: "Filter By status",
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
            let filteredFields = $(this).serializeArray().filter(field => field.name !== '_token' && field.value.trim() !== '');
            let formData = $.param(filteredFields);
            $.ajax({
                url: "<?php echo e(route('payments.index')); ?>",
                type: "GET",
                data: formData,
                success: function(data) {
                    $('#table-data').html(data.tableData);
                    let newUrl = "<?php echo e(route('payments.index')); ?>" + (formData ? '?' + formData : '');
                    history.pushState(null, '', newUrl);
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('banks.viewindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/payments/index.blade.php ENDPATH**/ ?>