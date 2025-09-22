@extends('user.layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-8 offset-lg-2">
            <!-- Card with light blue background and blue border -->
            <div class="card shadow-sm" style="background-color: rgba(225, 240, 255, 0.3); border: 2px solid #0d6efd;">
                <!-- Card header with blue background -->
                <div class="card-header" style="background-color: #e3f2fd; border-bottom: 2px solid #0d6efd;">
                    <div class="col d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 text-primary">
                            <i class="fas fa-user-edit me-2"></i>
                            Edit Agent
                        </h4>
                        <a href="{{ route('user.agent.list') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                <!-- Card body with light blue background -->
                <div class="card-body p-4" style="background-color: rgba(240, 248, 255, 0.5);">
                    {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 4px solid #dc3545;">
                                <strong><i class="fas fa-exclamation-circle me-2"></i> Please check the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Success Message --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-left: 4px solid #198754;">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    <form action="{{ route('user.agent.update', $agentData['agent_id']) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="agent_id" value="{{ $agentData['agent_id'] }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agent_name" class="form-label fw-semibold text-dark">Agent Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-blue"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" name="agent_name" id="agent_name"
                                        class="form-control"
                                        value="{{ old('agent_name', $agentData['agent_name']) }}"
                                        placeholder="Enter agent name" required
                                        style="background-color: rgba(255, 255, 255, 0.7);">
                                    <div class="invalid-feedback">
                                        Please provide a valid agent name.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold text-dark">Agent Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-blue"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" name="email" id="email"
                                        class="form-control"
                                        value="{{ old('email', $agentData['email']) }}"
                                        placeholder="Enter agent email" required
                                        style="background-color: rgba(255, 255, 255, 0.7);">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="phone_number" class="form-label fw-semibold text-dark">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-blue"><i class="fas fa-phone text-primary"></i></span>
                                    <input type="text" name="phone_number" id="phone_number"
                                        class="form-control"
                                        value="{{ old('phone_number', $agentData['phone_number']) }}"
                                        placeholder="Enter phone number" required
                                        style="background-color: rgba(255, 255, 255, 0.7);">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="company_id" class="form-label fw-semibold text-dark">Company <span class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-select" required
                                    style="background-color: rgba(255, 255, 255, 0.7);">
                                    <option value="">— Select a Company —</option>
                                    @if(isset($companies) && count($companies))
                                        @foreach($companies as $comp)
                                            <option value="{{ $comp['id'] }}"
                                                {{ (old('company_id', $agentData['company_id']) == $comp['id']) ? 'selected' : '' }}>
                                                {{ $comp['name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                            <label for="supervisor_id" class="form-label fw-semibold text-dark">Supervisor (Optional)</label>
                            <select name="supervisor_id" id="supervisor_id" 
                                class="form-select {{ $errors->has('supervisor_id') ? 'is-invalid' : '' }}"
                                style="background-color: rgba(255, 255, 255, 0.7);">
                                <option value="">— Select a Supervisor —</option>
                                @if(isset($agents[0]['agents']) && count($agents[0]['agents']))
                                    @foreach($agents[0]['agents'] as $agentOption)
                                        @php
                                            $optionId = $agentOption['id'] ?? $agentOption['agent_id'];
                                            $optionName = $agentOption['agent_name'] ?? $agentOption['name'];
                                        @endphp
                                        <option value="{{ $optionId }}" 
                                            {{ old('supervisor_id', $agentData['supervisor_id'] ?? '') == $optionId ? 'selected' : '' }}>
                                            {{ $optionName }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('supervisor_id'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('supervisor_id') }}
                                </div>
                            @endif
                        </div>

                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-dark">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                placeholder="Enter description" style="background-color: rgba(255, 255, 255, 0.7);">{{ old('description', $agentData['description']) }}</textarea>
                        </div>
{{--  
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" value="true" class="form-check-input"
                                {{ $agentData['is_active'] ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Active Agent</label>
                        </div>  --}}

                        <div class="d-flex justify-content-end pt-3">
                            <button type="reset" class="btn btn-outline-secondary me-3">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Update Agent
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
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endsection
