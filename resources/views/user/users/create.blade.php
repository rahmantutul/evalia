@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-8 m-auto">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Create New User</h4>
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                       id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Role *</label>
                                <select class="form-control @error('role_id') is-invalid @enderror" 
                                        id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role['id'] }}" {{ old('role_id') == $role['id'] ? 'selected' : '' }}>
                                            {{ $role['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="company_id" class="form-label">Company</label>
                                <select class="form-control @error('company_id') is-invalid @enderror" 
                                        id="company_id" name="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company['id'] }}" {{ old('company_id') == $company['id'] ? 'selected' : '' }}>
                                            {{ $company['name'] ?? $company['id'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position') }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supervisor_id" class="form-label">Supervisor</label>
                                <select class="form-control @error('supervisor_id') is-invalid @enderror" 
                                        id="supervisor_id" name="supervisor_id">
                                    <option value="">Select Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor['id'] }}" {{ old('supervisor_id') == $supervisor['id'] ? 'selected' : '' }}>
                                            {{ $supervisor['full_name'] }} ({{ $supervisor['email'] }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{--  <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active User
                                    </label>
                                </div>
                            </div>  --}}
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Create User</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection