@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cog"></i> Project Settings</h3>
                </div>
                <div class="card-body">
                    @if(!empty($project))
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Project Name</strong></label>
                                    <p class="form-control-plaintext">{{ $project['name'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Project ID</strong></label>
                                    <p class="form-control-plaintext">{{ $project['id'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>API Key Status</strong></label>
                                    <p>
                                        <span class="badge badge-success">Active</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Plan Type</strong></label>
                                    <p class="form-control-plaintext">{{ $project['plan'] ?? 'Standard' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>API Base URL</strong></label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ config('services.hamsa.base_url') }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(this)">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>Usage Limits</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Monthly Limit</h6>
                                        <h4>{{ $project['monthly_limit'] ?? 'Unlimited' }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Concurrent Jobs</h6>
                                        <h4>{{ $project['concurrent_jobs'] ?? 5 }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Max File Size</h6>
                                        <h4>100MB</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Unable to load project details. Please check your API configuration.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-shield-alt"></i> Security</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Your API key is securely stored in environment variables.
                    </div>
                    
                    <h6>Best Practices:</h6>
                    <ul>
                        <li>Never commit API keys to version control</li>
                        <li>Use environment variables for configuration</li>
                        <li>Regularly rotate API keys</li>
                        <li>Monitor usage for unusual activity</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle"></i> Support</h5>
                </div>
                <div class="card-body">
                    <p>Need help with your Hamsa integration?</p>
                    <ul>
                        <li><a href="https://docs.tryhamsa.com" target="_blank">Documentation</a></li>
                        <li><a href="https://api.tryhamsa.com" target="_blank">API Reference</a></li>
                        <li><a href="mailto:support@tryhamsa.com">Email Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function copyToClipboard(button) {
    const input = button.closest('.input-group').querySelector('input');
    input.select();
    document.execCommand('copy');
    
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copied!';
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.classList.remove('btn-success');
    }, 2000);
}
</script> 
@endsection
@endsection