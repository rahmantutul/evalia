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
                <h4 class="page-title">Manage Features: {{ $plan->name }}</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.plans.index') }}">Plans</a></li>
                        <li class="breadcrumb-item active">Features</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Feature Configuration</h4>
                    <p class="text-muted mb-4">Set feature values for this plan</p>

                    <form action="{{ route('admin.plans.features.update', $plan) }}" method="POST" id="features-form">
                        @csrf
                        
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
                                    <tr>
                                        <td>{{ $feature->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $feature->type === 'boolean' ? 'primary' : ($feature->type === 'integer' ? 'info' : 'success') }}">
                                                {{ ucfirst($feature->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $currentValue = $plan->features->where('id', $feature->id)->first()->pivot->value ?? '';
                                            @endphp
                                            
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

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="ri-save-line align-middle me-1"></i> Save Changes
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
    document.getElementById('features-form').addEventListener('submit', function(e) {
    });
</script>
@endpush