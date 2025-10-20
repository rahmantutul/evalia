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
                                <h2 class="mb-2 fw-semibold" style="color: #1f2937; font-size: 1.75rem;">Edit User Profile</h2>
                                <div class="user-badge">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $user['full_name'] }}</span>
                                </div>
                            </div>
                            <a href="{{ route('users.show', $user['id']) }}" class="btn btn-outline-linkedin btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to Details
                            </a>
                        </div>
                    </div>

                    <!-- Info Banner -->
                    <div class="info-banner">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Update user information carefully. Changes will be reflected immediately across the system.
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user['id']) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')

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

                        <div class="row g-3 mb-3">
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

                            <div class="col-md-6">
                                <label for="company_id" class="form-label">Company</label>
                                <select class="form-control input-focus @error('company_id') is-invalid @enderror" 
                                        id="company_id" 
                                        name="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company['id'] }}" {{ (old('company_id', $user['company_id'] ?? '') == $company['id']) ? 'selected' : '' }}>
                                            {{ $company['name'] ?? $company['id'] }}
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
                                <label for="position" class="form-label">Position</label>
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
                                <label for="phone" class="form-label">Phone Number</label>
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

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="supervisor_id" class="form-label">Supervisor</label>
                                <select class="form-control input-focus @error('supervisor_id') is-invalid @enderror" 
                                        id="supervisor_id" 
                                        name="supervisor_id">
                                    <option value="">Select Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor['id'] }}" {{ (old('supervisor_id', $user['supervisor_id'] ?? '') == $supervisor['id']) ? 'selected' : '' }}>
                                            {{ $supervisor['full_name'] }} ({{ $supervisor['email'] }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

    // Confirm before navigation if form has changes
    const form = document.getElementById('editUserForm');
    const initialFormData = new FormData(form);
    const initialValues = {};
    
    for (let [key, value] of initialFormData.entries()) {
        initialValues[key] = value;
    }

    window.addEventListener('beforeunload', function(e) {
        const currentFormData = new FormData(form);
        let hasChanges = false;

        for (let [key, value] of currentFormData.entries()) {
            if (initialValues[key] !== value) {
                hasChanges = true;
                break;
            }
        }

        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Remove warning when form is submitted
    form.addEventListener('submit', function() {
        window.removeEventListener('beforeunload', null);
    });
});
</script>
@endpush