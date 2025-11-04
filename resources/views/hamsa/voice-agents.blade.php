@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-headset text-primary me-2"></i>Voice Agents
                    </h1>
                    <p class="text-muted">Create and manage AI voice agents for automated conversations</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-robot text-info me-1"></i>AI-Powered
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
                            <h5 class="alert-heading mb-2">Success!</h5>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                <!-- Create Agent Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white py-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-plus-circle text-primary me-2"></i>Create New Voice Agent
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Beta</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <form method="POST" action="{{ url('/hamsa/voice-agents') }}" id="agentForm">
                                @csrf
                                
                                <!-- Basic Information -->
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="name" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-tag text-primary me-2"></i>Agent Name
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       class="form-control border-0 shadow-sm" 
                                                       id="name" 
                                                       name="name" 
                                                       placeholder="e.g., Customer Support Agent"
                                                       required>
                                                <small class="form-text text-muted mt-2 d-block">
                                                    Give your agent a descriptive name
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="voice" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>Voice
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="voice" name="voice" required>
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
                                </div>

                                <!-- Language Selection -->
                                <div class="row g-4 mb-5">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <label for="language" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-language text-primary me-2"></i>Language
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="language" name="language" required>
                                                    <optgroup label="Popular Languages">
                                                        <option value="en" selected>English</option>
                                                        <option value="ar">Arabic</option>
                                                        <option value="fr">French</option>
                                                        <option value="es">Spanish</option>
                                                    </optgroup>
                                                    <optgroup label="Other Languages">
                                                        <option value="de">German</option>
                                                        <option value="it">Italian</option>
                                                        <option value="pt">Portuguese</option>
                                                        <option value="ja">Japanese</option>
                                                    </optgroup>
                                                </select>
                                                <small class="form-text text-muted mt-2 d-block">
                                                    Select the primary language for your agent
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agent Instructions -->
                                <div class="mb-5">
                                    <label for="prompt" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-comment-dots text-primary me-2"></i>Agent Instructions
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-lg border-2" 
                                              id="prompt" 
                                              name="prompt" 
                                              rows="4" 
                                              placeholder="Describe the agent's personality, response style, and behavior guidelines. Example: 'You are a friendly customer support agent who helps users with technical issues. Be patient, empathetic, and provide clear step-by-step solutions.'"
                                              required></textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Be specific about tone, personality, and response style
                                        </small>
                                        <small class="text-muted" id="charCount">0/2000 characters</small>
                                    </div>
                                </div>

                                <!-- Advanced Settings -->
                                <div class="row g-4 mb-5">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-header bg-transparent border-0 py-3">
                                                <h6 class="card-title mb-0 fw-semibold text-dark">
                                                    <i class="fas fa-cog text-primary me-2"></i>Advanced Settings
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="allow_interruptions" name="allow_interruptions" checked>
                                                            <label class="form-check-label text-dark" for="allow_interruptions">
                                                                Allow user interruptions
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="background_noise" name="background_noise">
                                                            <label class="form-check-label text-dark" for="background_noise">
                                                                Background noise reduction
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="emotion_detection" name="emotion_detection">
                                                            <label class="form-check-label text-dark" for="emotion_detection">
                                                                Emotion detection
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="speech_enhancement" name="speech_enhancement" checked>
                                                            <label class="form-check-label text-dark" for="speech_enhancement">
                                                                Speech enhancement
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
                                            Your agent configuration is stored securely
                                        </small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="createBtn">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            <span class="create-text">Create Voice Agent</span>
                                            <span class="loading-text d-none">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Creating...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Your Agents Section -->
                <div class="col-lg-4">
                    <!-- Your Agents Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-semibold text-dark">
                                    <i class="fas fa-list text-primary me-2"></i>Your Voice Agents
                                </h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ count($agents ?? []) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($agents))
                                <div class="agents-list">
                                    @foreach($agents as $agent)
                                    <div class="agent-item card border-0 bg-light mb-3">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="fw-semibold text-dark mb-1">{{ $agent['name'] }}</h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0">
                                                            <i class="fas fa-user-circle me-1"></i>{{ ucfirst($agent['voice']) }}
                                                        </span>
                                                        <span class="badge bg-info bg-opacity-10 text-info border-0">
                                                            <i class="fas fa-language me-1"></i>{{ strtoupper($agent['language']) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ url('/hamsa/conversations') }}"><i class="fas fa-phone text-success me-2"></i>Start Call</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>Edit</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-copy text-info me-2"></i>Duplicate</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="text-muted small mb-3">{{ Str::limit($agent['prompt'] ?? 'No instructions set', 80) }}</p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ url('/hamsa/conversations') }}" class="btn btn-success btn-sm flex-fill">
                                                    <i class="fas fa-phone me-1"></i>Call Agent
                                                </a>
                                                <button class="btn btn-outline-primary btn-sm" onclick="testAgent('{{ $agent['id'] }}')">
                                                    <i class="fas fa-play me-1"></i>Test
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="fas fa-headset fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">No voice agents yet</h6>
                                    <p class="text-muted small mb-3">Create your first voice agent to get started</p>
                                    <button class="btn btn-primary btn-sm" onclick="document.getElementById('agentForm').scrollIntoView()">
                                        <i class="fas fa-plus me-1"></i>Create Agent
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Tips Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Agent Creation Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="tips-list">
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Be specific with instructions</strong>
                                        <p class="text-muted small mb-0">Clearly define personality and response style</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Choose appropriate voice</strong>
                                        <p class="text-muted small mb-0">Match voice tone to agent's purpose</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Test thoroughly</strong>
                                        <p class="text-muted small mb-0">Test agent responses before deployment</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Consider use case</strong>
                                        <p class="text-muted small mb-0">Tailor instructions to specific scenarios</p>
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
    const createBtn = document.getElementById('createBtn');
    const agentForm = document.getElementById('agentForm');
    
    // Character count update
    promptTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count}/2000 characters`;
        
        if (count > 2000) {
            charCount.classList.add('text-danger');
            createBtn.disabled = true;
        } else {
            charCount.classList.remove('text-danger');
            createBtn.disabled = false;
        }
    });
    
    // Form submission with loading state
    agentForm.addEventListener('submit', function() {
        if (promptTextarea.value.length > 2000) {
            alert('Instructions exceed 2000 character limit. Please shorten your text.');
            return false;
        }
        
        createBtn.disabled = true;
        createBtn.querySelector('.create-text').classList.add('d-none');
        createBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Initialize character count
    charCount.textContent = `${promptTextarea.value.length}/2000 characters`;
});

// Voice sample playback (placeholder)
function playVoiceSample() {
    const voiceSelect = document.getElementById('voice');
    const selectedVoice = voiceSelect.options[voiceSelect.selectedIndex].text;
    alert(`Playing sample for ${selectedVoice}. In a real implementation, this would play a voice sample.`);
}

// Test agent functionality
function testAgent(agentId) {
    alert(`Testing agent ${agentId}. In a real implementation, this would open a test interface.`);
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
    font-weight: 500;
}

.agent-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.agent-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
}

.tip-item {
    transition: all 0.3s ease;
}

.tip-item:hover {
    transform: translateX(5px);
}

.empty-state-icon {
    opacity: 0.5;
}

.form-switch .form-check-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.dropdown-menu {
    border-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.agents-list {
    max-height: 600px;
    overflow-y: auto;
}

.optgroup {
    font-weight: 600;
    color: #6c757d;
}

.optgroup option {
    font-weight: normal;
    color: #000;
}
</style>
@endsection