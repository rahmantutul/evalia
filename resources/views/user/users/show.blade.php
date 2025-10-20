@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">User Details</h4>
                        <div class="btn-group">
                            {{--  <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('users.change-password.form', $user['id']) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-key me-1"></i>Change Password
                            </a>  --}}
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="30%">Full Name:</td>
                                            <td class="fw-bold">{{ $user['full_name'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Email:</td>
                                            <td class="fw-bold">{{ $user['email'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Username:</td>
                                            <td class="fw-bold">{{ $user['username'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Status:</td>
                                            <td>
                                                <span class="badge {{ $user['is_active'] ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $user['is_active'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Professional Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="30%">Role:</td>
                                            <td>
                                                <span class="btn btn-sm  btn-success">{{ $user['role']['name'] ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Position:</td>
                                            <td class="fw-bold">{{ $user['position'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Phone:</td>
                                            <td class="fw-bold">{{ $user['phone'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Company ID:</td>
                                            <td class="fw-bold">{{ $user['company_id'] ?? 'N/A' }}</td>
                                        </tr>
                                        @if($user['supervisor_id'])
                                        <tr>
                                            <td class="text-muted">Supervisor ID:</td>
                                            <td class="fw-bold">{{ $user['supervisor_id'] }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--  <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">System Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="20%">User ID:</td>
                                            <td class="fw-bold font-monospace">{{ $user['id'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>  --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection