@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-robot text-primary me-2"></i>AI Content Generation
                    </h1>
                    <p class="text-muted">Generate high-quality content using advanced AI models</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-bolt text-warning me-1"></i>Powered by AI
                    </span>
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-5">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-2">Content Generated Successfully!</h5>
                            <div class="bg-light p-4 rounded border">
                                <strong class="d-block mb-2 text-dark">Generated Content:</strong>
                                <div class="text-dark lh-base">
                                    {{ session('generated_content') }}
                                </div>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-copy me-1"></i>Copy to Clipboard
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download me-1"></i>Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white py-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-bold text-dark">
                            <i class="fas fa-magic text-primary me-2"></i>Create New Content
                        </h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Beta</span>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    <form method="POST" action="{{ url('/hamsa/ai/generate') }}" id="aiForm">
                        @csrf
                        
                        <!-- Prompt Section -->
                        <div class="mb-5">
                            <label for="prompt" class="form-label fw-semibold text-dark mb-3">
                                <i class="fas fa-edit text-primary me-2"></i>Content Prompt
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-lg border-2" 
                                      id="prompt" 
                                      name="prompt" 
                                      rows="6" 
                                      placeholder="Describe what you want to generate. Be specific and detailed for better results..."
                                      required>{{ old('prompt') }}</textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="form-text text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Example: "Write a professional email to a client about project delay with apologies and new timeline"
                                </small>
                                <small class="text-muted" id="charCount">0 characters</small>
                            </div>
                        </div>

                        <!-- Configuration Section -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <label for="model" class="form-label fw-semibold text-dark">
                                            <i class="fas fa-brain text-primary me-2"></i>AI Model
                                        </label>
                                        <select class="form-select border-0 shadow-sm" id="model" name="model">
                                            <option value="gpt-4" selected>GPT-4 (Recommended)</option>
                                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Fast)</option>
                                            <option value="claude-2">Claude 2 (Creative)</option>
                                        </select>
                                        <small class="form-text text-muted mt-2 d-block">
                                            Choose the AI model based on your needs
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <label for="max_tokens" class="form-label fw-semibold text-dark">
                                            <i class="fas fa-ruler text-primary me-2"></i>Max Tokens
                                        </label>
                                        <input type="number" 
                                               class="form-control border-0 shadow-sm" 
                                               id="max_tokens" 
                                               name="max_tokens" 
                                               value="1000" 
                                               min="100" 
                                               max="4000">
                                        <div class="mt-2">
                                            <small class="form-text text-muted">
                                                Controls response length (100-4000 tokens)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <label for="temperature" class="form-label fw-semibold text-dark">
                                            <i class="fas fa-thermometer-half text-primary me-2"></i>Temperature
                                        </label>
                                        <input type="number" 
                                               class="form-control border-0 shadow-sm" 
                                               id="temperature" 
                                               name="temperature" 
                                               value="0.7" 
                                               min="0" 
                                               max="2" 
                                               step="0.1">
                                        <div class="mt-2">
                                            <small class="form-text text-muted">
                                                <span class="text-info">0.7</span> = Balanced creativity
                                            </small>
                                            <div class="d-flex justify-content-between text-muted small">
                                                <span>Precise</span>
                                                <span>Creative</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Your data is secure and private
                                </small>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="button" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-sync-alt me-2"></i>Clear
                                </button>
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="generateBtn">
                                    <i class="fas fa-magic me-2"></i>
                                    <span class="generate-text">Generate Content</span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Generating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Tips Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3 fw-semibold">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Better Results
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-bullseye text-primary mt-1 me-2"></i>
                                        <div>
                                            <strong>Be Specific</strong>
                                            <p class="small text-muted mb-0">Provide clear, detailed instructions</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-user-tie text-primary mt-1 me-2"></i>
                                        <div>
                                            <strong>Define Tone</strong>
                                            <p class="small text-muted mb-0">Specify formal, casual, or professional tone</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-ruler-combined text-primary mt-1 me-2"></i>
                                        <div>
                                            <strong>Set Length</strong>
                                            <p class="small text-muted mb-0">Mention desired word count or structure</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-clipboard-list text-primary mt-1 me-2"></i>
                                        <div>
                                            <strong>Include Examples</strong>
                                            <p class="small text-muted mb-0">Reference similar content you like</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const promptTextarea = document.getElementById('prompt');
    const charCount = document.getElementById('charCount');
    const generateBtn = document.getElementById('generateBtn');
    const aiForm = document.getElementById('aiForm');
    
    // Character count update
    promptTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length + ' characters';
    });
    
    // Form submission with loading state
    aiForm.addEventListener('submit', function() {
        generateBtn.disabled = true;
        generateBtn.querySelector('.generate-text').classList.add('d-none');
        generateBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Initialize character count
    charCount.textContent = promptTextarea.value.length + ' characters';
});
</script>
@endpush

<style>
.card {
    border-radius: 12px;
}

.form-control, .form-select {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.alert {
    border-radius: 12px;
}

.badge {
    border-radius: 6px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}
</style>
@endsection