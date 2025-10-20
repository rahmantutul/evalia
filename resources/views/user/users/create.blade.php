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
    .username-preview {
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;
    }
    .username-preview strong {
        color: #495057;
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
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1 fw-semibold" style="color: #1f2937; font-size: 1.75rem;">Create New User</h2>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">Add a new team member to your organization</p>
                            </div>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-linkedin btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                        @csrf

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
                                       value="{{ old('full_name') }}" 
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
                                       value="{{ old('email') }}" 
                                       placeholder="user@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Username Preview -->
                        <div class="mb-4">
                            <label class="form-label">Generated Username</label>
                            <div class="username-preview">
                                Username will be: <strong id="usernamePreview">Enter email above</strong>
                            </div>
                            <input type="hidden" id="username" name="username" value="{{ old('username') }}">
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
                                <select class="form-control input-focus @error('company_id') is-invalid @enderror" 
                                        id="company_id" 
                                        name="company_id">
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

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" 
                                       class="form-control input-focus @error('position') is-invalid @enderror" 
                                       id="position" 
                                       name="position" 
                                       value="{{ old('position') }}"
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
                                       value="{{ old('phone') }}"
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
                                        <option value="{{ $supervisor['id'] }}" {{ old('supervisor_id') == $supervisor['id'] ? 'selected' : '' }}>
                                            {{ $supervisor['full_name'] }} ({{ $supervisor['email'] }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="section-divider">
                            <span><i class="fas fa-lock me-2"></i>Security</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="required-mark">*</span></label>
                                <input type="password" 
                                       class="form-control input-focus @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter secure password"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="required-mark">*</span></label>
                                <input type="password" 
                                       class="form-control input-focus" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Re-enter password"
                                       required>
                                <small class="text-muted">Must match password</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-end pt-3 border-top">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-linkedin">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-linkedin">
                                <i class="fas fa-user-plus me-2"></i>Create User
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
    const emailInput = document.getElementById('email');
    const usernamePreview = document.getElementById('usernamePreview');
    const usernameInput = document.getElementById('username');
    
    // Function to generate username from email
    function generateUsername(email) {
        if (!email) return '';
        
        // Extract the part before @ and clean it up
        let username = email.split('@')[0];
        
        // Remove special characters and replace with underscores
        username = username.replace(/[^a-zA-Z0-9]/g, '_');
        
        // Remove multiple consecutive underscores
        username = username.replace(/_+/g, '_');
        
        // Remove leading/trailing underscores
        username = username.replace(/^_+|_+$/g, '');
        
        // Ensure username is at least 3 characters
        if (username.length < 3) {
            username = username + '_user';
        }
        
        return username.toLowerCase();
    }
    
    // Update username preview when email changes
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        const username = generateUsername(email);
        
        if (username) {
            usernamePreview.textContent = username;
            usernameInput.value = username;
        } else {
            usernamePreview.textContent = 'Enter email above';
            usernameInput.value = '';
        }
    });
    
    // Update on form submission
    document.getElementById('createUserForm').addEventListener('submit', function() {
        const email = emailInput.value.trim();
        const username = generateUsername(email);
        
        if (username) {
            usernameInput.value = username;
        }
    });
    
    // Initialize on page load if there's already an email value
    if (emailInput.value) {
        const username = generateUsername(emailInput.value.trim());
        if (username) {
            usernamePreview.textContent = username;
            usernameInput.value = username;
        }
    }
    
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