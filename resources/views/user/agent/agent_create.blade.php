@extends('user.layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="row mb-4 mt-3">
            <div class="col-md-12 col-lg-8 offset-lg-2">
                <div class="card shadow-sm border-0">
                     <div class="card-header">
                        <div class="col d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas {{ isset($agent) ? 'fa-user-edit' : 'fa-user-plus' }} me-2"></i>
                                {{ isset($agent) ? 'Edit Agent' : 'Create New Agent' }}
                            </h4>
                            <a href="{{ route('user.agent.list') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                   
                    <div class="card-body p-4">
                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle me-2"></i> Please check the following
                                    errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Success Message --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Form --}}
                        <form
                            action="{{ isset($agent) ? route('user.agent.update', $agent->id) : route('user.agent.store') }}"
                            method="POST" class="needs-validation" novalidate>
                            @csrf
                            @if (isset($agent))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">Agent Name <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" id="name"
                                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            value="{{ old('name', $agent->name ?? '') }}" placeholder="Enter agent name"
                                            required>
                                        <div class="invalid-feedback">
                                            Please provide a valid name.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">Agent Email <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email" id="email"
                                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                            value="{{ old('email', $agent->email ?? '') }}" placeholder="Enter agent email"
                                            required>
                                        <div class="invalid-feedback">
                                            Please provide a valid email address.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" name="phone" id="phone"
                                            class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                                            value="{{ old('phone', $agent->phone ?? '') }}"
                                            placeholder="Enter phone number">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label fw-semibold">Company</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        <input type="text" name="company" id="company"
                                            class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}"
                                            value="{{ old('company', $agent->company ?? '') }}"
                                            placeholder="Enter company name">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="position" class="form-label fw-semibold">Position</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" name="position" id="position"
                                        class="form-control {{ $errors->has('position') ? 'is-invalid' : '' }}"
                                        value="{{ old('position', $agent->position ?? '') }}"
                                        placeholder="Enter position title">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-3">
                                <button type="reset" class="btn btn-outline-secondary me-3">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas {{ isset($agent) ? 'fa-save' : 'fa-plus' }} me-1"></i>
                                    {{ isset($agent) ? 'Update Agent' : 'Create Agent' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Form validation (function () { 'use strict' var forms = document.querySelectorAll('.needs-validation') Array.prototype.slice.call(forms) .forEach(function (form) { form.addEventListener('submit', function (event) { if (!form.checkValidity()) { event.preventDefault() event.stopPropagation() } form.classList.add('was-validated') }, false) }) })() 
    </script>
@endsection
