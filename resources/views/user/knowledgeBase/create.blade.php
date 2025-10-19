@extends('user.layouts.app')
@push('styles')
<style>
    :root {
        --primary-color: #0a66c2;
        --secondary-color: #004182;
        --accent-color: #4895ef;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
    }
    
    .tagify {
        --tag-bg: var(--primary-color);
        --tag-hover: var(--secondary-color);
        --tag-text-color: white;
        --tags-border-color: #e0e0e0;
        --tag-remove-btn-color: white;
        padding: 0.5rem;
        border-radius: 8px;
    }
    
    .tagify__input {
        padding: 0.25rem 0.5rem !important;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
</style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-8 offset-lg-2">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-database me-2"></i> Create Knowledge Base Entry
                        </h4>
                        <a href="{{ route('user.knowledgeBase.list') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- API Errors --}}
                    @if ($errors->has('api_error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-circle me-2"></i> API Error:</strong>
                            {{ $errors->first('api_error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                   {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('user.knowledgeBase.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="knowledgeBaseForm">
                        @csrf
                        <div class="mb-3">
                            <label for="company_id" class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                    <option value="">-- Select Company --</option>
                                    @if(isset($companies) && is_array($companies))
                                        @foreach($companies as $company)
                                            @php
                                                $companyId = '';
                                                $companyName = 'Unknown';
                                                
                                                if (is_array($company)) {
                                                    $companyId = $company['id'] ?? $company['company_id'] ?? '';
                                                    $companyName = $company['name'] ?? $company['company_name'] ?? $companyId;
                                                } elseif (is_object($company)) {
                                                    $companyId = $company->id ?? $company->company_id ?? '';
                                                    $companyName = $company->name ?? $company->company_name ?? $companyId;
                                                }
                                            @endphp
                                            @if($companyId)
                                                <option value="{{ $companyId }}" {{ old('company_id') == $companyId ? 'selected' : '' }}>
                                                    {{ $companyName }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    @error('company_id') {{ $message }} @else Please select a company. @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">File <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file"
                                class="form-control @error('file') is-invalid @enderror" 
                                accept=".txt,.pdf,.doc,.docx,.csv,.xlsx,.xls" required>
                            <div class="form-text">Allowed files: txt, pdf, doc, docx, csv, xlsx, xls (Max: 10MB)</div>
                            <div class="invalid-feedback">
                                @error('file') {{ $message }} @else Please upload a valid file. @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="topics" class="form-label fw-semibold">Topics</label>
                            <input type="text" class="form-control @error('topics') is-invalid @enderror" 
                                   id="topics" name="topics" 
                                   placeholder="Type and press enter to add topics"
                                   value="{{ old('topics') }}">
                            <div class="form-text">Add multiple topics by typing and pressing Enter</div>
                            @error('topics')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5"
                                class="form-control @error('description') is-invalid @enderror" 
                                placeholder="Enter knowledge base description" required>{{ old('description') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @else Please enter a description. @enderror
                            </div>
                        </div>

                        {{-- Hidden run_embedding field with default value --}}
                        <input type="hidden" name="run_embedding" value="0">

                        <div class="d-flex justify-content-end pt-3">
                            <button type="reset" class="btn btn-outline-secondary me-3">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                <i class="fas fa-plus me-1"></i> Create Knowledge Base
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
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('knowledgeBaseForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!form) {
        console.error('Form not found');
        return;
    }

    // Initialize Tagify
    const topicsInput = document.getElementById('topics');
    if (topicsInput) {
        const tagify = new Tagify(topicsInput, {
            duplicates: false,
            dropdown: { 
                enabled: 0,
                maxItems: 10
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        });

        // Handle form submission to ensure Tagify values are properly included
        form.addEventListener('submit', function(e) {
            // Tagify automatically updates the original input value
            // so we don't need to do anything extra here
            console.log('Topics value before submit:', topicsInput.value);
        });
    }

    // Regular form submission (let Laravel handle it)
    form.addEventListener('submit', function(event) {
        // Basic validation
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';
            
            // Re-enable button if form submission fails (fallback)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        }
        
        // Form will submit normally to Laravel backend
        // Laravel will then make the API call to the external service
    });

    // Reset form validation on reset
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        
        // Reset Tagify if it exists
        if (window.tagify) {
            window.tagify.removeAllTags();
        }
        
        // Re-enable submit button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i> Create Knowledge Base';
        }
    });

    // File input validation
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = [
                'text/plain',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/csv',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            const allowedExtensions = ['.txt', '.pdf', '.doc', '.docx', '.csv', '.xlsx', '.xls'];

            if (file) {
                // Check file size
                if (file.size > maxSize) {
                    this.setCustomValidity('File size must be less than 10MB');
                    this.reportValidity();
                    this.value = '';
                    return;
                }

                // Check file type
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                    this.setCustomValidity('Please select a valid file type (txt, pdf, doc, docx, csv, xlsx, xls)');
                    this.reportValidity();
                    this.value = '';
                    return;
                }

                this.setCustomValidity('');
            }
        });
    }
});
</script>
@endpush