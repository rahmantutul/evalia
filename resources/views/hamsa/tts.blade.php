@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-volume-up text-primary me-2"></i>Text to Speech
                    </h1>
                    <p class="text-muted">Convert written text into natural-sounding audio</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-robot text-info me-1"></i>AI-Powered Voices
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
                                    <i class="fas fa-keyboard text-primary me-2"></i>Create Speech from Text
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Real-time</span>
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
                                            <h5 class="alert-heading mb-2">Audio Generated Successfully!</h5>
                                            <p class="mb-3">{{ session('success') }}</p>
                                            
                                            @if(session('audio_url'))
                                                <div class="mt-4 p-4 bg-light rounded-3 border">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <i class="fas fa-music text-primary me-2"></i>
                                                        <strong class="text-dark">Your Generated Audio:</strong>
                                                    </div>
                                                    <audio controls class="w-100 rounded-2 mb-3">
                                                        <source src="{{ session('audio_url') }}" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <a href="{{ session('audio_url') }}" class="btn btn-success btn-sm" download>
                                                            <i class="fas fa-download me-1"></i>Download MP3
                                                        </a>
                                                        <button class="btn btn-outline-primary btn-sm" onclick="shareAudio()">
                                                            <i class="fas fa-share me-1"></i>Share
                                                        </button>
                                                        <button class="btn btn-outline-secondary btn-sm" onclick="playAudio()">
                                                            <i class="fas fa-play me-1"></i>Play Again
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ url('/hamsa/tts') }}" id="ttsForm">
                                @csrf
                                
                                <!-- Text Input Section -->
                                <div class="mb-5">
                                    <label for="text" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-edit text-primary me-2"></i>Text to Convert
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-lg border-2" 
                                              id="text" 
                                              name="text" 
                                              rows="6" 
                                              placeholder="Enter the text you want to convert to speech. Be descriptive for better results..."
                                              required>{{ old('text') }}</textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Maximum 5000 characters
                                        </small>
                                        <small class="text-muted" id="charCount">0/5000 characters</small>
                                    </div>
                                </div>

                                <!-- Configuration Section -->
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="voice" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>Voice Selection
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="voice" name="voice">
                                                    <option value="alloy" selected>Alloy - Neutral & Balanced</option>
                                                    <option value="echo">Echo - Warm & Resonant</option>
                                                    <option value="fable">Fable - Storyteller Style</option>
                                                    <option value="onyx">Onyx - Deep & Authoritative</option>
                                                    <option value="nova">Nova - Bright & Clear</option>
                                                    <option value="shimmer">Shimmer - Soft & Gentle</option>
                                                </select>
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="playVoiceSample()">
                                                        <i class="fas fa-play-circle me-1"></i>Play Voice Sample
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="speed" class="form-label fw-semibold text-dark mb-3">
                                                    <i class="fas fa-tachometer-alt text-primary me-2"></i>
                                                    Speech Speed: <span id="speedValue" class="text-primary fw-bold">1.0x</span>
                                                </label>
                                                <input type="range" 
                                                       class="form-range" 
                                                       id="speed" 
                                                       name="speed" 
                                                       min="0.25" 
                                                       max="4.0" 
                                                       step="0.25" 
                                                       value="1.0">
                                                <div class="d-flex justify-content-between text-muted small mt-2">
                                                    <span>Slower</span>
                                                    <span>Normal</span>
                                                    <span>Faster</span>
                                                </div>
                                                <div class="mt-3 pt-2 border-top">
                                                    <small class="form-text text-muted">
                                                        Adjust the speaking rate (0.25x to 4.0x)
                                                    </small>
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
                                            Your text is processed securely
                                        </small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="generateBtn">
                                            <i class="fas fa-play-circle me-2"></i>
                                            <span class="generate-text">Generate Speech</span>
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
                </div>
                
                <!-- Sidebar Information -->
                <div class="col-lg-4">
                    <!-- Voice Samples Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-headphones text-primary me-2"></i>Voice Characteristics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="voice-samples">
                                <div class="voice-sample-item d-flex align-items-start mb-4 p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-balance-scale"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Alloy</h6>
                                        <p class="text-muted small mb-2">Neutral, balanced voice perfect for general content</p>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0">Recommended</span>
                                    </div>
                                </div>
                                
                                <div class="voice-sample-item d-flex align-items-start mb-4 p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-sun"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Echo</h6>
                                        <p class="text-muted small mb-0">Warm, resonant voice with rich tone</p>
                                    </div>
                                </div>
                                
                                <div class="voice-sample-item d-flex align-items-start mb-4 p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Fable</h6>
                                        <p class="text-muted small mb-0">Expressive voice ideal for storytelling</p>
                                    </div>
                                </div>
                                
                                <div class="voice-sample-item d-flex align-items-start p-3 rounded-3 bg-light">
                                    <div class="voice-icon bg-dark bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-microphone"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1 text-dark">Onyx</h6>
                                        <p class="text-muted small mb-0">Deep, authoritative voice for professional content</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Better Results
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="tips-list">
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Use punctuation</strong>
                                        <p class="text-muted small mb-0">Commas and periods create natural pauses</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Break long text</strong>
                                        <p class="text-muted small mb-0">Split into paragraphs for better flow</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Check pronunciation</strong>
                                        <p class="text-muted small mb-0">Spell out unusual words phonetically</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Match voice to content</strong>
                                        <p class="text-muted small mb-0">Choose voice tone that fits your message</p>
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
    const textTextarea = document.getElementById('text');
    const charCount = document.getElementById('charCount');
    const speedSlider = document.getElementById('speed');
    const speedValue = document.getElementById('speedValue');
    const generateBtn = document.getElementById('generateBtn');
    const ttsForm = document.getElementById('ttsForm');
    
    // Character count update
    textTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count}/5000 characters`;
        
        if (count > 5000) {
            charCount.classList.add('text-danger');
            generateBtn.disabled = true;
        } else {
            charCount.classList.remove('text-danger');
            generateBtn.disabled = false;
        }
    });
    
    // Speed slider update
    speedSlider.addEventListener('input', function() {
        speedValue.textContent = `${this.value}x`;
        
        // Visual feedback for speed
        const speed = parseFloat(this.value);
        if (speed < 1.0) {
            speedValue.className = 'text-info fw-bold';
        } else if (speed > 1.0) {
            speedValue.className = 'text-warning fw-bold';
        } else {
            speedValue.className = 'text-primary fw-bold';
        }
    });
    
    // Form submission with loading state
    ttsForm.addEventListener('submit', function() {
        if (textTextarea.value.length > 5000) {
            alert('Text exceeds 5000 character limit. Please shorten your text.');
            return false;
        }
        
        generateBtn.disabled = true;
        generateBtn.querySelector('.generate-text').classList.add('d-none');
        generateBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Initialize character count
    charCount.textContent = `${textTextarea.value.length}/5000 characters`;
    
    // Initialize speed display
    speedValue.textContent = `${speedSlider.value}x`;
});

// Voice sample playback (placeholder - would need actual sample URLs)
function playVoiceSample() {
    const voiceSelect = document.getElementById('voice');
    const selectedVoice = voiceSelect.options[voiceSelect.selectedIndex].text;
    alert(`Playing sample for ${selectedVoice}. In a real implementation, this would play an audio sample.`);
}

// Audio control functions
function playAudio() {
    const audioElement = document.querySelector('audio');
    if (audioElement) {
        audioElement.currentTime = 0;
        audioElement.play();
    }
}

function shareAudio() {
    const audioUrl = "{{ session('audio_url', '') }}";
    if (audioUrl && navigator.share) {
        navigator.share({
            title: 'Generated Audio',
            text: 'Check out this AI-generated audio!',
            url: audioUrl
        });
    } else {
        alert('Share URL: ' + audioUrl);
    }
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

.voice-sample-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.voice-sample-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
}

.form-range::-webkit-slider-thumb {
    background: #3b82f6;
}

.form-range::-moz-range-thumb {
    background: #3b82f6;
}

audio {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.tip-item {
    transition: all 0.3s ease;
}

.tip-item:hover {
    transform: translateX(5px);
}
</style>
@endsection