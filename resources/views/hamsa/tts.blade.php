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
                        
                        <div class="card-body p-3">
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
                                                <select class="form-select border-0 shadow-sm" id="voice" name="voice" required>
                                                    <option value="">Select an agent voice </option>
                                                    @foreach ($agents as $agent)
                                                        <option value="{{ $agent['voiceRecordId']}}" >{{ $agent['agentName'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="playVoiceSample()">
                                                        <i class="fas fa-play-circle me-1"></i>Play Voice Sample
                                                    </button>
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
                                        <strong class="text-dark">Punctuation</strong>
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
    ttsForm.addEventListener('submit', function(e) {
        if (textTextarea.value.length > 5000) {
            alert('Text exceeds 5000 character limit. Please shorten your text.');
            e.preventDefault();
            return false;
        }
        
        generateBtn.disabled = true;
        generateBtn.querySelector('.generate-text').classList.add('d-none');
        generateBtn.querySelector('.loading-text').classList.remove('d-none');
        
        // Show processing state immediately
        showProcessingState();
    });
    
    // Check if we have a processing job from session
    @if(session('processing') && session('job_id'))
        checkJobStatus('{{ session('job_id') }}');
    @endif
    
    // Initialize character count
    charCount.textContent = `${textTextarea.value.length}/5000 characters`;
    speedValue.textContent = `${speedSlider.value}x`;
});

// Show processing state with animation
function showProcessingState() {
    const processingHTML = `
        <div class="alert alert-info border-0 shadow-sm mb-5" id="processingAlert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="spinner-border text-info me-3"></div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">Generating Your Audio</h5>
                    <p class="mb-3" id="processingMessage">Starting audio generation process...</p>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove any existing success/error alerts
    document.querySelectorAll('.alert').forEach(alert => alert.remove());
    
    // Add processing alert after the form header
    const cardBody = document.querySelector('.card-body');
    const formHeader = cardBody.querySelector('h4').parentElement.parentElement;
    formHeader.insertAdjacentHTML('afterend', processingHTML);
}

    // Update processing message and progress
    function updateProcessingState(message, progress) {
        const processingMessage = document.getElementById('processingMessage');
        const progressBar = document.getElementById('progressBar');
        
        if (processingMessage) {
            processingMessage.textContent = message;
        }
        
        if (progressBar && progress !== undefined) {
            progressBar.style.width = `${progress}%`;
        }
    }

    // Check job status with polling
    function checkJobStatus(jobId) {
        showProcessingState();
        updateProcessingState('Your audio is being generated...', 30);
        
        let checkCount = 0;
        const maxChecks = 60; // 5 minutes maximum (5 seconds × 60 = 300 seconds)
        
        const checkInterval = setInterval(() => {
            checkCount++;
            
            // Calculate progress (starts at 30%, goes to 90% over time)
            const progress = 30 + Math.min((checkCount / maxChecks) * 60, 60);
            updateProcessingState('Processing your audio...', progress);
            
            fetch(`/hamsa/tts/status?job_id=${jobId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Status check:', data);
                    
                    if (data.status === 'COMPLETED') {
                        clearInterval(checkInterval);
                        updateProcessingState('Audio ready! Finalizing...', 100);
                        
                        // Small delay to show completion
                        setTimeout(() => {
                            showSuccessResult(data.audio_url, data.job_data);
                        }, 1000);
                        
                    } else if (data.status === 'FAILED' || data.status === 'ERROR') {
                        clearInterval(checkInterval);
                        showErrorResult(data.error || 'Audio generation failed');
                        
                    } else if (data.status === 'pending' || data.status === 'PROCESSING') {
                        // Continue polling
                        updateProcessingState(data.message || 'Processing your audio...', progress);
                        
                    } else if (data.status === 'error') {
                        clearInterval(checkInterval);
                        showErrorResult(data.message || 'Error checking status');
                    }
                    
                    // Stop polling after max attempts
                    if (checkCount >= maxChecks) {
                        clearInterval(checkInterval);
                        showErrorResult('Audio generation is taking longer than expected. Please check back later.');
                    }
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                    if (checkCount >= maxChecks) {
                        clearInterval(checkInterval);
                        showErrorResult('Unable to check audio status. Please refresh the page.');
                    }
                });
        }, 5000); // Check every 5 seconds
    }

    // Show success result
    function showSuccessResult(audioUrl, jobData) {
        const processingAlert = document.getElementById('processingAlert');
        
        const successHTML = `
            <div class="alert alert-success border-0 shadow-sm mb-5">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading mb-2">Audio Generated Successfully!</h5>
                        <p class="mb-3">Your text has been converted to speech successfully.</p>
                        
                        ${audioUrl ? `
                        <div class="mt-4 p-4 bg-light rounded-3 border">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-music text-primary me-2"></i>
                                <strong class="text-dark">Your Generated Audio:</strong>
                            </div>
                            <audio controls class="w-100 rounded-2 mb-3" id="resultAudio">
                                <source src="${audioUrl}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="${audioUrl}" class="btn btn-success btn-sm" download>
                                    <i class="fas fa-download me-1"></i>Download MP3
                                </a>
                                <button class="btn btn-outline-primary btn-sm" onclick="shareAudio('${audioUrl}')">
                                    <i class="fas fa-share me-1"></i>Share
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="playAudio()">
                                    <i class="fas fa-play me-1"></i>Play Again
                                </button>
                            </div>
                        </div>
                        ` : '<p class="text-warning">Audio URL not available</p>'}
                    </div>
                </div>
            </div>
        `;
        
        if (processingAlert) {
            processingAlert.innerHTML = successHTML;
            processingAlert.className = 'alert alert-success border-0 shadow-sm mb-5';
        }
        
        // Re-enable the generate button
        const generateBtn = document.getElementById('generateBtn');
        if (generateBtn) {
            generateBtn.disabled = false;
            generateBtn.querySelector('.generate-text').classList.remove('d-none');
            generateBtn.querySelector('.loading-text').classList.add('d-none');
        }
    }

    // Show error result
    function showErrorResult(errorMessage) {
        const processingAlert = document.getElementById('processingAlert');
        
        const errorHTML = `
            <div class="alert alert-danger border-0 shadow-sm mb-5">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading mb-2">Audio Generation Failed</h5>
                        <p class="mb-0">${errorMessage}</p>
                    </div>
                </div>
            </div>
        `;
        
        if (processingAlert) {
            processingAlert.innerHTML = errorHTML;
            processingAlert.className = 'alert alert-danger border-0 shadow-sm mb-5';
        }
        
        // Re-enable the generate button
        const generateBtn = document.getElementById('generateBtn');
        if (generateBtn) {
            generateBtn.disabled = false;
            generateBtn.querySelector('.generate-text').classList.remove('d-none');
            generateBtn.querySelector('.loading-text').classList.add('d-none');
        }
    }

    // Audio control functions
    function playAudio() {
        const audioElement = document.getElementById('resultAudio');
        if (audioElement) {
            audioElement.currentTime = 0;
            audioElement.play();
        }
    }

    function shareAudio(audioUrl) {
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

    // Voice sample player
    function playVoiceSample() {
        const selectedVoice = document.getElementById('voice').value;
        alert(`Playing sample for ${selectedVoice}. In a real implementation, this would play an audio sample.`);
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
.form-range {
    width: 90%;
    height: 1.5rem;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: #172935;
    border-radius: 3px;
    padding: 6px;
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