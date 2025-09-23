@extends('riders.view')

@section('page_content')

{!! Form::open(['route' => 'riders.store','id'=>'formajax']) !!}
<input type="hidden" id="redirect_url" value="{{route('riders.index')}}" />
<div class="card-body">

    <div class="row">
        @include('riders.fields')
    </div>

</div>
<div class="card-footer bg-light border-top">
    <div class="d-flex justify-content-end gap-3">
        <a href="{{ route('riders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">Save Information</button>
    </div>
</div>

{!! Form::close() !!}

</div>
</div>

<style>
    /* Select2 custom styles */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: white;
    }

    .select2-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
        color: #007bff;
        z-index: 1000;
    }

    /* Notification styles */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

@endsection
@push('page-scripts')


<!-- Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for recruiter field with tagging
        $('#recruiter_select').select2({
            tags: true,
            placeholder: 'Select or type a new recruiter',
            allowClear: true,
            width: '100%',
            createTag: function(params) {
                var term = $.trim(params.term);

                if (term === '' || term === 'Select or type a new recruiter') {
                    return null;
                }

                return {
                    id: term,
                    text: term + ' (new)',
                    newTag: true
                };
            },
            templateResult: function(data) {
                if (data.newTag) {
                    return $('<span><i class="fa fa-plus"></i> Add "' + data.text.replace(' (new)', '') + '"</span>');
                }
                return data.text;
            },
            templateSelection: function(data) {
                return data.text.replace(' (new)', '');
            }
        });

        // Handle selection change to add new recruiters
        $('#recruiter_select').on('select2:select', function(e) {
            var data = e.params.data;

            if (data.newTag) {
                var recruiterName = data.text.replace(' (new)', '');

                // Show loading state
                var $select = $(this);
                var $container = $select.parent();
                $container.append('<div class="select2-loading">Adding recruiter...</div>');

                // Make AJAX request to add new recruiter
                $.ajax({
                    url: '{{ route("riders.addRecruiter") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        recruiter_name: recruiterName
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showNotification('Recruiter "' + response.recruiter_name + '" added successfully!', 'success');

                            // Update the option to remove (new) tag
                            var $option = $select.find('option[value="' + recruiterName + '"]');
                            $option.text(recruiterName);
                            $option.removeAttr('data-new-tag');

                            // Trigger change to update display
                            $select.trigger('change');
                        } else {
                            showNotification('Error: ' + response.message, 'error');
                            // Remove the invalid option
                            $select.find('option[value="' + recruiterName + '"]').remove();
                            $select.val(null).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to add recruiter';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showNotification('Error: ' + errorMessage, 'error');
                        // Remove the invalid option
                        $select.find('option[value="' + recruiterName + '"]').remove();
                        $select.val(null).trigger('change');
                    },
                    complete: function() {
                        // Remove loading state
                        $container.find('.select2-loading').remove();
                    }
                });
            }
        });

        // Function to show notifications
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
            <div class="notification-content">
                <i class="ti ti-${type === 'success' ? 'check' : 'x'}"></i>
                <span>${message}</span>
            </div>
        `;

            // Add styles
            notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 300px;
        `;

            // Add to page
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    });
</script>

@endpush