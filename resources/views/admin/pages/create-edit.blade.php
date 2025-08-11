@extends('admin.layouts.app')
@push('styles')
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ isset($admin) ? 'Edit' : 'Create' }} Admin</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ isset($admin) ? route('admin.admins.update', $admin->id) : route('admin.admins.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($admin))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $admin->name ?? '') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $admin->email ?? '') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">{{ isset($admin) ? 'New ' : '' }}Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" {{ isset($admin) ? '' : 'required' }} autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password-confirm">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" {{ isset($admin) ? '' : 'required' }} autocomplete="new-password">
                        </div>

                        <div class="form-group mb-3">
                            <label for="roleId">Role</label>
                            <select id="roleId" class="form-control @error('roleId') is-invalid @enderror" name="roleId" required>
                                <option value="1" {{ (old('roleId', $admin->roleId ?? '') == 1 ? 'selected' : '') }}>Admin</option>
                                <option value="2" {{ (old('roleId', $admin->roleId ?? '') == 2 ? 'selected' : '') }}>Sub Admin</option>
                            </select>
                            @error('roleId')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="image">Profile Image</label>
                            <input type="file" id="image" name="image" class="dropify" data-default-file="{{ isset($admin) && $admin->image ? asset('storage/'.$admin->image) : '' }}" data-height="200" />
                            @error('image')
                                <span class="text-danger" style="font-size: 0.8rem;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-0 text-end">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($admin) ? 'Update' : 'Create' }} Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('.dropify').dropify();
        });
    </script>
@endpush