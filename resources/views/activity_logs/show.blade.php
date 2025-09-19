@extends('layouts.app')
@section('title', 'Activity Log Details')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Activity Log Details</h4>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Logs
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $activityLog->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Action:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $activityLog->action == 'created' ? 'success' : ($activityLog->action == 'updated' ? 'warning' : ($activityLog->action == 'deleted' ? 'danger' : 'info')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $activityLog->action)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Module:</strong></td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($activityLog->module_name) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Model:</strong></td>
                                            <td>
                                                @if($activityLog->model_type && $activityLog->model_id)
                                                <span class="text-muted">{{ $activityLog->model_type }} #{{ $activityLog->model_id }}</span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>IP Address:</strong></td>
                                            <td>{{ $activityLog->ip_address ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Timestamp:</strong></td>
                                            <td>{{ $activityLog->created_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">User Information</h6>
                                </div>
                                <div class="card-body">
                                    @if($activityLog->user)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-lg me-3">
                                            <span class="avatar-initial rounded-circle bg-primary fs-4">{{ substr($activityLog->user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $activityLog->user->name }}</h6>
                                            <small class="text-muted">{{ $activityLog->user->email }}</small>
                                        </div>
                                    </div>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>User ID:</strong></td>
                                            <td>{{ $activityLog->user->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $activityLog->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $activityLog->user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                    @else
                                    <div class="text-center text-muted">
                                        <i class="ti ti-user-off ti-lg mb-2"></i>
                                        <p>System Action</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activityLog->changes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Changes Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if(isset($activityLog->changes['old']) && isset($activityLog->changes['new']))
                                        <!-- Updated record -->
                                        <div class="col-md-6">
                                            <h6 class="text-danger mb-3">Previous Values</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    @foreach($activityLog->changes['old'] as $key => $value)
                                                    <tr>
                                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                                        <td class="text-muted">{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-success mb-3">New Values</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    @foreach($activityLog->changes['new'] as $key => $value)
                                                    <tr>
                                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                                        <td class="text-muted">{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                        @elseif(isset($activityLog->changes['new']))
                                        <!-- Created record -->
                                        <div class="col-12">
                                            <h6 class="text-success mb-3">New Record Data</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    @foreach($activityLog->changes['new'] as $key => $value)
                                                    <tr>
                                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                                        <td class="text-muted">{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                        @elseif(isset($activityLog->changes['old']))
                                        <!-- Deleted record -->
                                        <div class="col-12">
                                            <h6 class="text-danger mb-3">Deleted Record Data</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    @foreach($activityLog->changes['old'] as $key => $value)
                                                    <tr>
                                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                                        <td class="text-muted">{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                        @else
                                        <!-- Custom changes -->
                                        <div class="col-12">
                                            <h6 class="text-info mb-3">Custom Data</h6>
                                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($activityLog->changes, JSON_PRETTY_PRINT) }}</code></pre>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($activityLog->model_type && $activityLog->model_id)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Related Model</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <p class="mb-0">
                                            <strong>Model:</strong> {{ $activityLog->model_type }}<br>
                                            <strong>ID:</strong> {{ $activityLog->model_id }}
                                        </p>
                                        <p class="mb-0 mt-2">
                                            <em>This action was performed on the above model record.</em>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection