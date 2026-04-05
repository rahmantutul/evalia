@extends('user.layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f2ef;
    }
    .linkedin-blue {
        color: #0a66c2;
    }
    .linkedin-blue-bg {
        background-color: #0a66c2;
    }
    .hover\:linkedin-blue-dark:hover {
        background-color: #004182;
    }
    .form-container {
        background-color: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(4px);
    }
    .input-focus:focus {
        border-color: #0a66c2;
        box-shadow: 0 0 0 2px rgba(10, 102, 194, 0.2);
        outline: none;
    }
    .form-control:focus {
        border-color: #0a66c2;
        box-shadow: 0 0 0 2px rgba(10, 102, 194, 0.2);
    }
    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }
    .is-invalid {
        border-color: #dc2626 !important;
    }
    .invalid-feedback {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .section-divider {
        border-top: 2px solid #e5e7eb;
        margin: 1.5rem 0;
        position: relative;
    }
    .section-divider span {
        background: white;
        padding: 0 1rem;
        position: absolute;
        top: -0.6rem;
        left: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-label {
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    .btn-linkedin {
        background-color: #0a66c2;
        color: white;
        font-weight: 500;
        padding: 0.625rem 1.5rem;
        border-radius: 0.5rem;
        border: none;
        transition: all 0.2s;
    }
    .btn-linkedin:hover {
        background-color: #004182;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(10, 102, 194, 0.2);
    }
    .btn-outline-linkedin {
        background-color: transparent;
        color: #0a66c2;
        border: 1px solid #0a66c2;
        font-weight: 500;
        padding: 0.625rem 1.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    .btn-outline-linkedin:hover {
        background-color: #f0f7ff;
        color: #004182;
        border-color: #004182;
    }
    .header-stripe {
        height: 3px;
        background: linear-gradient(90deg, #0a66c2 0%, #0077b5 100%);
    }
    .required-mark {
        color: #dc2626;
        margin-left: 2px;
    }
    .user-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #eff6ff;
        color: #1e40af;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #bfdbfe;
    }
    .info-banner {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }
    .info-banner i {
        color: #d97706;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="form-container rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header stripe -->
                <div class="header-stripe"></div>
                
                <div class="p-4 p-md-5">
                    <!-- Header -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="mb-2 fw-semibold" style="color: #1f2937; font-size: 1.75rem;">
                                    {{ $type === 'agent' ? 'Edit Agent Profile' : 'Edit User Profile' }}
                                </h2>
                                <div class="user-badge">
                                    <i class="fas fa-{{ $type === 'agent' ? 'headset' : 'user' }}"></i>
                                    <span>{{ $user['full_name'] }}</span>
                                </div>
                            </div>
                            <a href="{{ $type === 'agent' ? route('user.agents.index') : route('users.index') }}" class="btn btn-outline-linkedin btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to list
                            </a>
                        </div>
                    </div>

                    <!-- Info Banner -->
                    <div class="info-banner">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Update {{ $type === 'agent' ? 'agent' : 'user' }} information carefully. Changes will be reflected immediately across the system.
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3 fs-4 text-danger"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">System Error</h6>
                                    <p class="mb-0 small">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-hand-paper me-3 fs-4 text-danger"></i>
                                <h6 class="alert-heading fw-bold mb-0">Please Correct the Following:</h6>
                            </div>
                            <ul class="mb-0 small ps-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user['id']) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="{{ $type }}">

                        <!-- Basic Information -->
                        <div class="section-divider">
                            <span><i class="fas fa-user me-2"></i>Basic Information</span>
                        </div>

                        <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label">Full Name <span class="required-mark">*</span></label>
                                        <input type="text" 
                                            class="form-control input-focus @error('full_name') is-invalid @enderror" 
                                            id="full_name" 
                                            name="full_name" 
                                            value="{{ old('full_name', $user['full_name']) }}" 
                                            placeholder="Enter full name"
                                            required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="required-mark">*</span></label>
                                <input type="email" 
                                    class="form-control input-focus @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $user['email']) }}" 
                                    placeholder="user@example.com"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role & Organization -->
                        <div class="section-divider">
                            <span><i class="fas fa-briefcase me-2"></i>Role & Organization</span>
                        </div>

                        <!-- Rest of your existing form fields -->
                        <div class="row g-3 mb-3">
                            @if($type === 'agent')
                                <div class="col-md-6">
                                    <label for="supervisor_id" class="form-label">Supervisor <span class="text-muted fw-normal">(Optional)</span></label>
                                    <select class="form-control input-focus @error('supervisor_id') is-invalid @enderror" 
                                            id="supervisor_id" 
                                            name="supervisor_id">
                                        <option value="">Self</option>
                                        @foreach($supervisors as $supervisor)
                                            <option value="{{ $supervisor['id'] }}" {{ (old('supervisor_id', $user['supervisor_id'] ?? '') == $supervisor['id']) ? 'selected' : '' }}>
                                                {{ $supervisor['full_name'] }}   {{ $supervisor['id'] == $user['id'] ? '(Self)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Only Agents can be assigned as supervisors.</small>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label">Role <span class="required-mark">*</span></label>
                                    <select class="form-control input-focus @error('role_id') is-invalid @enderror" 
                                            id="role_id" 
                                            name="role_id" 
                                            required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role['id'] }}" {{ (old('role_id', $user['role']['id'] ?? '') == $role['id']) ? 'selected' : '' }}>
                                                {{ $role['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label for="company_id" class="form-label">Company <span class="text-muted fw-normal">(Optional)</span></label>
                                <select class="form-control input-focus @error('company_id') is-invalid @enderror" 
                                        id="company_id" 
                                        name="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company['id'] }}" {{ (old('company_id', $user['company']['id'] ?? '') == $company['id']) ? 'selected' : '' }}>
                                            {{ $company['company_name'] ?? $company['id'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" 
                                    class="form-control input-focus @error('position') is-invalid @enderror" 
                                    id="position" 
                                    name="position" 
                                    value="{{ old('position', $user['position'] ?? '') }}"
                                    placeholder="e.g. Sales Manager">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="tel" 
                                    class="form-control input-focus @error('phone') is-invalid @enderror" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone', $user['phone'] ?? '') }}"
                                    placeholder="+1 (555) 000-0000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Password Change Section -->
                        <div class="section-divider">
                            <span><i class="fas fa-lock me-2"></i>Password Change (Optional)</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" 
                                    class="form-control input-focus @error('new_password') is-invalid @enderror" 
                                    id="new_password" 
                                    name="new_password" 
                                    placeholder="Enter new password (min 6 characters)"
                                    minlength="6">
                                <div class="form-text">Leave blank to keep current password</div>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" 
                                    class="form-control input-focus" 
                                    id="new_password_confirmation" 
                                    name="new_password_confirmation" 
                                    placeholder="Confirm new password">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end pt-3 border-top">
                            <a href="{{ route('users.show', $user['id']) }}" class="btn btn-outline-linkedin">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-linkedin">
                                <i class="fas fa-save me-2"></i>Update User
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clear error styling when user starts typing
    const errorFields = document.querySelectorAll('.is-invalid');
    errorFields.forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const errorElement = this.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.style.display = 'none';
            }
        });
    });
});
</script>
@endpush