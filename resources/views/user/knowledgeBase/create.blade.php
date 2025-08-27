@extends('user.layouts.app')

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
                    <form action="{{ route('user.knowledgeBase.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                       <div class="mb-3">
                            <label for="company_id" class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <select name="company_id" id="company_id" class="form-select {{ $errors->has('company_id') ? 'is-invalid' : '' }}" required>
                                    <option value="">-- Select Company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company['id'] ?? $company['company_id'] }}" 
                                            {{ old('company_id') == ($company['id'] ?? $company['company_id']) ? 'selected' : '' }}>
                                            {{ $company['name'] ?? $company['company_id'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a company.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">File <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file"
                                class="form-control {{ $errors->has('file') ? 'is-invalid' : '' }}" required>
                            <div class="invalid-feedback">Please upload a file.</div>
                        </div>

                        <div class="mb-4">
                            <label for="data" class="form-label fw-semibold">Data <span class="text-danger">*</span></label>
                            <textarea name="data" id="data" rows="5"
                                class="form-control {{ $errors->has('data') ? 'is-invalid' : '' }}" placeholder="Enter knowledge base data" required>{{ old('data') }}</textarea>
                            <div class="invalid-feedback">Please enter valid data.</div>
                        </div>

                        <div class="d-flex justify-content-end pt-3">
                            <button type="reset" class="btn btn-outline-secondary me-3">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
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

@section('scripts')
@endsection
