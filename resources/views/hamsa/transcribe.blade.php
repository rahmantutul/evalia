@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-microphone text-primary me-2"></i>Speech to Text Transcription
                    </h1>
                    <p class="text-muted">Convert audio files to text using advanced AI speech recognition</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-waveform text-info me-1"></i>AI-Powered Transcription
                    </span>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Content Card -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white py-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-file-upload text-primary me-2"></i>Upload Audio File
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Live</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <!-- Success Alert -->
                            @if(session('success'))
                                <div class="alert alert-success border-0 shadow-sm mb-5">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="alert-heading mb-2">Transcription Started!</h5>
                                            <p class="mb-3">{{ session('success') }}</p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ url('/hamsa/jobs') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-tasks me-1"></i>View Job Progress
                                                </a>
                                                <button class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-redo me-1"></i>Upload Another
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
                                            <h5 class="alert-heading mb-2">Transcription Error</h5>
                                            <p class="mb-0">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ url('/hamsa/transcribe') }}" enctype="multipart/form-data" id="uploadForm">
                                @csrf
                                
                                <!-- File Upload Section -->
                                <div class="mb-5">
                                    <label for="audio_file" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-music text-primary me-2"></i>Audio File
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
                                            <i class="fas fa-folder-open me-2"></i>Choose File
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

                                <!-- Configuration Section -->
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="language" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-language text-primary me-2"></i>Language
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="language" name="language">
                                                    <option value="en" selected>English</option>
                                                    <option value="ar">Arabic</option>
                                                    <option value="fr">French</option>
                                                    <option value="es">Spanish</option>
                                                    <option value="de">German</option>
                                                    <option value="auto">Auto-detect</option>
                                                </select>
                                                <small class="form-text text-muted mt-2 d-block">
                                                    Select the spoken language in your audio
                                                </small>
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
                                                    <option value="whisper-2" selected>Whisper v2 (Latest)</option>
                                                    <option value="whisper-1">Whisper v1 (Legacy)</option>
                                                </select>
                                                <small class="form-text text-muted mt-2 d-block">
                                                    Choose the transcription model version
                                                </small>
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
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="uploadBtn">
                                            <i class="fas fa-play-circle me-2"></i>
                                            <span class="upload-text">Start Transcription</span>
                                            <span class="loading-text d-none">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Uploading...
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
                                <i class="fas fa-info-circle text-primary me-2"></i>How it Works
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
                                        <p class="text-muted small mb-0">Select your audio file from device</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-icon bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Configure Settings</h6>
                                        <p class="text-muted small mb-0">Choose language and AI model</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">AI Processing</h6>
                                        <p class="text-muted small mb-0">AI transcribes speech to text</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Track Progress</h6>
                                        <p class="text-muted small mb-0">Monitor job status in Jobs section</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start">
                                    <div class="step-icon bg-purple bg-opacity-10 text-purple rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-file-text"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Get Results</h6>
                                        <p class="text-muted small mb-0">Download transcribed text</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supported Languages Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-globe text-primary me-2"></i>Supported Languages
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="language-grid">
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">English</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">Arabic</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">French</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">Spanish</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">German</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">+50 more</span>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Tip:</strong> Use "Auto-detect" for multilingual audio files
                                </small>
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
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadForm = document.getElementById('uploadForm');
    
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
    uploadForm.addEventListener('submit', function() {
        if (fileInput.files.length === 0) {
            alert('Please select an audio file to upload.');
            return false;
        }
        
        uploadBtn.disabled = true;
        uploadBtn.querySelector('.upload-text').classList.add('d-none');
        uploadBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Clear file selection
    window.clearFile = function() {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
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

.language-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.border-dashed {
    border-style: dashed !important;
}
</style>
@endsection