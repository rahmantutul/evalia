@extends('admin.layouts.app')

@push('styles')
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Create New Feature</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.features.index') }}">Features</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Create New Feature</h4>
                    <p class="text-muted mb-4">Fill in the details below to create a new feature</p>

                    <form action="{{ route('features.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Feature Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key" class="form-label">Key (Slug) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                           id="key" name="key" value="{{ old('key') }}" required>
                                    <small class="text-muted">Lowercase letters and underscores only</small>
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Data Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="integer" {{ old('type') == 'integer' ? 'selected' : '' }}>Integer</option>
                                        <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                                        <option value="string" {{ old('type') == 'string' ? 'selected' : '' }}>String (Text)</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', 1) }}" 
                                           min="1" required>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="ri-save-line align-middle me-1"></i> Create Feature
                            </button>
                            <a href="{{ route('admin.features.index') }}" class="btn btn-secondary waves-effect ms-2">
                                <i class="ri-close-line align-middle me-1"></i> Cancel
                            </a>
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
        // Auto-generate key from name
        const nameInput = document.getElementById('name');
        const keyInput = document.getElementById('key');
        
        nameInput.addEventListener('blur', function() {
            if (!keyInput.value) {
                keyInput.value = this.value.toLowerCase()
                    .replace(/\s+/g, '_')      // Replace spaces with _
                    .replace(/[^\w_]+/g, '')    // Remove all non-word chars
                    .replace(/_+/g, '_');       // Replace multiple _ with single
            }
        });

        // Initialize validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // You can add additional validation here if needed
            });
        }
    });
</script>
@endpush