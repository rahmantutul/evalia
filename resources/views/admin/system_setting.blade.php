@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">System Settings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Update System Settings</h4>
                    <hr>
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Company Information -->
                        <div class="mb-4">
                            <h5>Company Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" name="company_name" 
                                               value="{{ old('company_name', $settings->company_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company Tagline</label>
                                        <input type="text" class="form-control" name="company_tagline" 
                                               value="{{ old('company_tagline', $settings->company_tagline) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company Logo</label>
                                        <input type="file" class="form-control" name="company_logo" id="company_logo" onchange="previewImage(this, 'logo-preview')">
                                        
                                        @if($settings->company_logo)
                                        <div class="mt-2">
                                            <h6>Current Logo:</h6>
                                            <img src="{{ asset('storage/' . $settings->company_logo) }}" 
                                                 id="logo-preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 100px; max-width: 200px;">
                                        </div>
                                        @else
                                        <div class="mt-2">
                                            <img src="https://via.placeholder.com/200x100?text=No+Logo" 
                                                 id="logo-preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 100px; max-width: 200px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Favicon</label>
                                        <input type="file" class="form-control" name="favicon" id="favicon" onchange="previewImage(this, 'favicon-preview')">
                                        
                                        @if($settings->favicon)
                                        <div class="mt-2">
                                            <h6>Current Favicon:</h6>
                                            <img src="{{ asset('storage/' . $settings->favicon) }}" 
                                                 id="favicon-preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 50px; max-width: 50px;">
                                        </div>
                                        @else
                                        <div class="mt-2">
                                            <img src="https://via.placeholder.com/50x50?text=No+Favicon" 
                                                 id="favicon-preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 50px; max-width: 50px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pricing Title</label>
                                        <input type="text" class="form-control" name="pricing_title" 
                                            value="{{ old('pricing_title', $settings->pricing_title) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="mb-3">
                                        <label class="form-label">Pricing Subtitle</label>
                                        <input type="text" class="form-control" name="pricing_subtitle" 
                                            value="{{ old('pricing_subtitle', $settings->pricing_subtitle) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Offer Text</label>
                                        <input type="text" class="form-control" name="offer_text" value="{{ old('offer_text', $settings->offer_text) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Welcome Text</label>
                                        <input type="text" class="form-control" name="dashboard_text" 
                                            value="{{ old('dashboard_text', $settings->dashboard_text) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="mb-3">
                                        <label class="form-label">Subtext</label>
                                        <input type="text" class="form-control" name="dashboard_subtext" 
                                            value="{{ old('dashboard_subtext', $settings->dashboard_subtext) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
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
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection