@extends('user.layouts.app')
@push('styles')
<style>
    .profile-pic-wrapper {
        position: relative;
        width: 150px;
        margin: 0 auto 20px;
        cursor: pointer;
    }
    .profile-pic {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #f8f9fa;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .upload-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #6f6af8;
        color: white;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        line-height: 36px;
        text-align: center;
    }
    .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 12px;
        top: 38px;
    }
   
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Profile</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a></li>
                        <li class="breadcrumb-item active">Profile Settings</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Edit Profile</h4>
                    <p class="text-muted mb-4">Update your personal information and settings</p>

                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture -->
                        <div class="profile-pic-wrapper" onclick="document.getElementById('profile-pic-upload').click()">
                            @php
                                $avatar = auth()->user()->avatar 
                                    ? Storage::url(auth()->user()->avatar) 
                                    : asset('assets/images/default-avatar.png');
                            @endphp

                            <img src="{{ $avatar }}" 
                                class="profile-pic" 
                                id="profile-pic-preview"
                                loading="lazy"
                                width="150"
                                height="150"
                                onerror="this.src='{{ asset('assets/images/default-avatar.png') }}'">
                            <div class="upload-btn">
                                <i class="fas fa-camera"></i>
                                <input type="file" name="avatar" id="profile-pic-upload" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <h5 class="mb-3">Personal Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name <small style="color: red"> (required)</small></label>
                                        <input type="text" class="form-control" name="name" 
                                               value="{{ old('name', auth()->user()->name) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address <small style="color: red"> (required)</small></label>
                                        <input type="email" name="email" class="form-control" 
                                               value="{{ auth()->user()->email }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number <small style="color: red"> (required)</small></label>
                                        <input type="tel" class="form-control" id="phone" 
                                               name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                        <input type="hidden" name="country_code" id="country-code">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                     <div class="mb-3">
                                        <label class="form-label">Company Name <small style="color: red"> (required)</small></label>
                                        <input type="text" class="form-control" name="company" 
                                            value="{{ old('company', auth()->user()->company) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Change Section -->
                        <div class="mb-4">
                            <h5 class="mb-3">Change Password <small style="color: red"> (Change If needed)</small></h5>
                            
                            <div class="mb-3 position-relative">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" id="current_password">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password')"></i>
                            </div>
                            
                            <div class="mb-3 position-relative">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" id="new_password">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password')"></i>
                            </div>
                            
                            <div class="mb-3 position-relative">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password_confirmation')"></i>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: true,
                position: 'center'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
                showConfirmButton: true,
                position: 'center'
            });
        @endif
    });
</script>
<script>
    document.getElementById('profile-pic-upload').addEventListener('change', function(e) {
        const [file] = e.target.files;
        if (file) {
            const preview = document.getElementById('profile-pic-preview');
            preview.src = URL.createObjectURL(file);
            preview.onload = () => URL.revokeObjectURL(preview.src);
        }
    });

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling;
        field.type = field.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
</script>
@endpush