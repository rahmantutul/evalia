@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Change Password</h4>
                        <a href="{{ route('users.show', $user['id']) }}" class="btn btn-sm btn-secondary">Back to Details</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.change-password', $user['id']) }}" method="POST">
                        @csrf

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password *</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" required>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                                <input type="password" class="form-control" 
                                       id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Change Password</button>
                                <a href="{{ route('users.show', $user['id']) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection