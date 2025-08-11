@extends('admin.layouts.app')

@push('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Edit Plan: {{ $plan->name }}</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.plans.index') }}">Plans</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Plan Details</h4>
                    <p class="text-muted mb-4">Update the plan information and features below</p>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.plans.update', $plan) }}" method="POST" id="edit-plan-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Plan Information -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" step="0.01" min="0" 
                                               value="{{ old('price', $plan->price) }}" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" 
                                        value="{{ old('description', $plan->description) }}" required>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" 
                                           value="{{ old('sort_order', $plan->sort_order) }}" min="1" required>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 d-flex gap-5">
                                <div class="mb-3">
                                    <label class="form-label">Is active</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_active" name="is_active" value="1"
                                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active Plan</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Is Popular</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_popular" value="0">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_popular" name="is_popular" value="1"
                                               {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_popular">Mark as Popular</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feature Assignment Section -->
                        <div class="mb-4">
                            <h4 class="header-title">Plan Features</h4>
                            <hr>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%">Feature</th>
                                            <th width="20%">Type</th>
                                            <th width="40%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($features as $feature)
                                        @php
                                            $currentValue = $plan->features->where('id', $feature->id)->first()->pivot->value ?? '';
                                        @endphp
                                        <tr>
                                            <td>{{ $feature->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $feature->type === 'boolean' ? 'primary' : ($feature->type === 'integer' ? 'info' : 'success') }}">
                                                    {{ ucfirst($feature->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($feature->type === 'boolean')
                                                    <select name="features[{{ $feature->id }}]" class="form-select">
                                                        <option value="0" {{ $currentValue == '0' ? 'selected' : '' }}>No</option>
                                                        <option value="1" {{ $currentValue == '1' ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                @elseif($feature->type === 'integer')
                                                    <input type="number" name="features[{{ $feature->id }}]" 
                                                           value="{{ $currentValue }}" class="form-control">
                                                @else
                                                    <input type="text" name="features[{{ $feature->id }}]" 
                                                           value="{{ $currentValue }}" class="form-control">
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="ri-save-line align-middle me-1"></i> Update Plan & Features
                            </button>
                            <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary waves-effect ms-2">
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

    // Form validation
    document.getElementById('edit-plan-form').addEventListener('submit', function(e) {
        const price = document.getElementById('price').value;
        if (price < 0) {
            e.preventDefault();
            toastr.error("Price cannot be negative");
        }
    });
</script>
@endpush