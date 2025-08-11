@extends('admin.layouts.app')

@push('styles')
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Edit Feature</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.features.index') }}">Features</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Edit Feature: {{ $feature->name }}</h4>
                    <p class="text-muted mb-4">Update the feature details below</p>

                    <form action="{{ route('admin.features.update', $feature) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Feature Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $feature->name) }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key" class="form-label">Key (Slug)</label>
                                    <input type="text" class="form-control" id="key" name="key" 
                                           value="{{ old('key', $feature->key) }}" required>
                                    <small class="text-muted">Lowercase letters and underscores only</small>
                                    @error('key')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Data Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="integer" {{ old('type', $feature->type) === 'integer' ? 'selected' : '' }}>Integer</option>
                                        <option value="boolean" {{ old('type', $feature->type) === 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                                        <option value="string" {{ old('type', $feature->type) === 'string' ? 'selected' : '' }}>String (Text)</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                           value="{{ old('sort_order', $feature->sort_order) }}" min="1" required>
                                    @error('sort_order')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update Feature</button>
                            <a href="{{ route('admin.features.index') }}" class="btn btn-secondary waves-effect ms-2">Cancel</a>
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
    // You can add any JavaScript needed for this page here
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Auto-generate key from name
        document.getElementById('name').addEventListener('blur', function() {
            const nameInput = this;
            const keyInput = document.getElementById('key');
            
            if (!keyInput.value) {
                keyInput.value = nameInput.value.toLowerCase().replace(/\s+/g, '_');
            }
        });
    });
</script>
@endpush