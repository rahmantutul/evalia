@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>Voice Conversion
                    </h1>
                    <p class="text-muted">Transform speech from one voice to another while preserving the original content</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-magic text-info me-1"></i>AI-Powered
                    </span>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Conversion Card -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white py-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-upload text-primary me-2"></i>Upload & Convert
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Advanced AI</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-2">
                            <!-- Success Alert -->
                            @if(session('success'))
                                <div class="alert alert-success border-0 shadow-sm mb-5">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="alert-heading mb-2">Conversion Started!</h5>
                                            <p class="mb-3">{{ session('success') }}</p>
                                            @if(session('job_id'))
                                                <div class="d-flex align-items-center bg-light p-3 rounded-2">
                                                    <i class="fas fa-tasks text-primary me-2"></i>
                                                    <strong class="text-dark me-2">Job ID:</strong>
                                                    <code class="text-dark">{{ session('job_id') }}</code>
                                                </div>
                                            @endif
                                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                                <a href="{{ url('/hamsa/jobs') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-tasks me-1"></i>Track Progress
                                                </a>
                                                <button class="btn btn-outline-secondary btn-sm" onclick="resetForm()">
                                                    <i class="fas fa-redo me-1"></i>New Conversion
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Error Alert -->
                            @if(session('error'))
                                <div class="alert alert-danger border-0 shadow-sm mb-5">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="alert-heading mb-2">Conversion Error</h5>
                                            <p class="mb-0">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ url('/hamsa/sts') }}" enctype="multipart/form-data" id="conversionForm">
                                @csrf
                                
                                <!-- File Upload Section -->
                                <div class="mb-5">
                                    <label for="audio_file" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-file-audio text-primary me-2"></i>Source Audio File
                                        <span class="text-danger">*</span>
                                    </label>
                                    
                                    <div class="file-upload-area border-2 border-dashed rounded-3 p-5 text-center bg-light">
                                        <div class="file-upload-icon mb-3">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                        </div>
                                        <h5 class="text-dark mb-2">Drop your audio file here</h5>
                                        <p class="text-muted mb-3">or click to browse</p>
                                        <input type="file" 
                                               class="form-control-file d-none" 
                                               id="audio_file" 
                                               name="audio_file" 
                                               required 
                                               accept=".mp3,.wav,.m4a,.ogg,.flac">
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('audio_file').click()">
                                            <i class="fas fa-folder-open me-2"></i>Choose Audio File
                                        </button>
                                        <div class="mt-3">
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Max file size: 100MB • Supported: MP3, WAV, M4A, OGG, FLAC
                                            </small>
                                        </div>
                                        <div id="fileInfo" class="mt-3 d-none">
                                            <div class="alert alert-info border-0 d-inline-flex align-items-center">
                                                <i class="fas fa-file-audio me-2 text-primary"></i>
                                                <span id="fileName" class="fw-semibold"></span>
                                                <button type="button" class="btn-close ms-2" onclick="clearFile()"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Voice Selection Section -->
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="target_voice" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>Target Voice
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="target_voice" name="target_voice" required>
                                                    <option value="alloy" selected>Alloy - Neutral & Professional</option>
                                                    <option value="echo">Echo - Warm & Engaging</option>
                                                    <option value="fable">Fable - Storyteller Style</option>
                                                    <option value="onyx">Onyx - Deep & Authoritative</option>
                                                    <option value="nova">Nova - Bright & Energetic</option>
                                                    <option value="shimmer">Shimmer - Soft & Gentle</option>
                                                </select>
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="playVoiceSample()">
                                                        <i class="fas fa-play-circle me-1"></i>Preview Voice
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="model" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-brain text-primary me-2"></i>AI Model
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="model" name="model">
                                                    <option value="voice-conversion-2" selected>Voice Conversion v2 (Premium)</option>
                                                    <option value="voice-conversion-1">Voice Conversion v1 (Standard)</option>
                                                </select>
                                                <div class="mt-3 pt-2 border-top">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-star text-warning me-1"></i>
                                                        <strong>v2 Premium:</strong> Higher quality, better voice preservation
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conversion Options -->
                                <div class="row g-4 mb-5">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <label class="form-label fw-semibold text-dark mb-3">
                                                    <i class="fas fa-sliders-h text-primary me-2"></i>Conversion Options
                                                </label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="preserve_emotion" name="preserve_emotion" checked>
                                                            <label class="form-check-label text-dark" for="preserve_emotion">
                                                                Preserve emotional tone
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="enhance_audio" name="enhance_audio">
                                                            <label class="form-check-label text-dark" for="enhance_audio">
                                                                Enhance audio quality
                                                            </label>
                                                        </div>
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
                                            Your files are processed securely
                                        </small>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <a href="{{ url('/hamsa/jobs') }}" class="btn btn-outline-secondary px-4">
                                            <i class="fas fa-tasks me-2"></i>View Jobs
                                        </a>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="convertBtn">
                                            <i class="fas fa-exchange-alt me-2"></i>
                                            <span class="convert-text">Convert Voice</span>
                                            <span class="loading-text d-none">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Converting...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Information -->
                <div class="col-lg-4">
                    <!-- How it Works Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-info-circle text-primary me-2"></i>How It Works
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="process-steps">
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Upload Audio</h6>
                                        <p class="text-muted small mb-0">Select your source audio file</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-icon bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-voice"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Choose Voice</h6>
                                        <p class="text-muted small mb-0">Select target voice style</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">AI Processing</h6>
                                        <p class="text-muted small mb-0">AI converts voice while preserving content</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start">
                                    <div class="step-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-download"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Get Results</h6>
                                        <p class="text-muted small mb-0">Download converted audio file</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Use Cases Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Use Cases
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="use-case-item d-flex align-items-start mb-3">
                                <i class="fas fa-film text-primary mt-1 me-2"></i>
                                <div>
                                    <strong class="text-dark">Voice Dubbing</strong>
                                    <p class="text-muted small mb-0">Replace voices in videos and podcasts</p>
                                </div>
                            </div>
                            <div class="use-case-item d-flex align-items-start mb-3">
                                <i class="fas fa-globe text-info mt-1 me-2"></i>
                                <div>
                                    <strong class="text-dark">Content Localization</strong>
                                    <p class="text-muted small mb-0">Adapt content for different regions</p>
                                </div>
                            </div>
                            <div class="use-case-item d-flex align-items-start mb-3">
                                <i class="fas fa-flask text-success mt-1 me-2"></i>
                                <div>
                                    <strong class="text-dark">Voice Experimentation</strong>
                                    <p class="text-muted small mb-0">Test different voice styles</p>
                                </div>
                            </div>
                            <div class="use-case-item d-flex align-items-start">
                                <i class="fas fa-user-cog text-warning mt-1 me-2"></i>
                                <div>
                                    <strong class="text-dark">Content Personalization</strong>
                                    <p class="text-muted small mb-0">Customize audio for audience preferences</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Voice Samples Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-headphones text-primary me-2"></i>Voice Characteristics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="voice-samples">
                                <div class="voice-sample-item d-flex align-items-start mb-3 p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-balance-scale"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Alloy</h6>
                                        <p class="text-muted small mb-0">Neutral, professional voice for business</p>
                                    </div>
                                </div>
                                
                                <div class="voice-sample-item d-flex align-items-start mb-3 p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-sun"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Echo</h6>
                                        <p class="text-muted small mb-0">Warm, resonant voice for engagement</p>
                                    </div>
                                </div>
                                
                                <div class="voice-sample-item d-flex align-items-start p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-dark bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-microphone"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Onyx</h6>
                                        <p class="text-muted small mb-0">Deep, authoritative voice for impact</p>
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
    const fileInput = document.getElementById('audio_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const convertBtn = document.getElementById('convertBtn');
    const conversionForm = document.getElementById('conversionForm');
    
    // File input change handler
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileInfo.classList.remove('d-none');
            
            // Validate file size (100MB)
            const maxSize = 100 * 1024 * 1024; // 100MB in bytes
            if (file.size > maxSize) {
                alert('File size exceeds 100MB limit. Please choose a smaller file.');
                clearFile();
                return;
            }
            
            // Validate file type
            const validTypes = ['.mp3', '.wav', '.m4a', '.ogg', '.flac'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if (!validTypes.includes(fileExtension)) {
                alert('Please select a valid audio file (MP3, WAV, M4A, OGG, FLAC).');
                clearFile();
                return;
            }
        }
    });
    
    // Form submission with loading state
    conversionForm.addEventListener('submit', function() {
        if (fileInput.files.length === 0) {
            alert('Please select an audio file to convert.');
            return false;
        }
        
        convertBtn.disabled = true;
        convertBtn.querySelector('.convert-text').classList.add('d-none');
        convertBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Clear file selection
    window.clearFile = function() {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
    };
    
    // Reset form
    window.resetForm = function() {
        conversionForm.reset();
        clearFile();
        window.location.href = '#';
    };
    
    // Drag and drop functionality
    const uploadArea = document.querySelector('.file-upload-area');
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary', 'bg-primary bg-opacity-5');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary bg-opacity-5');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary bg-opacity-5');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
});

// Voice sample playback (placeholder)
function playVoiceSample() {
    const voiceSelect = document.getElementById('target_voice');
    const selectedVoice = voiceSelect.options[voiceSelect.selectedIndex].text;
    alert(`Playing sample for ${selectedVoice}. In a real implementation, this would play a voice sample.`);
}
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

.file-upload-area {
    border-style: dashed !important;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #3b82f6 !important;
    background-color: rgba(59, 130, 246, 0.05) !important;
}

.step-icon {
    flex-shrink: 0;
}

.voice-sample-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.voice-sample-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
}

.use-case-item {
    transition: all 0.3s ease;
}

.use-case-item:hover {
    transform: translateX(5px);
}

.border-dashed {
    border-style: dashed !important;
}

.step-item {
    transition: all 0.3s ease;
}

.step-item:hover {
    transform: translateX(5px);
}
</style>
@endsection