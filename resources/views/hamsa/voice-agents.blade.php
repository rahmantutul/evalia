@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h2 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-headset text-primary me-2"></i>Voice Agents
                    </h1>
                    <p class="text-muted mb-0">Create and manage AI voice agents for automated conversations</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-robot text-info me-1"></i>AI-Powered
                    </span>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#helpModal">
                        <i class="fas fa-question-circle me-1"></i>Help
                    </button>
                </div>
            </div>

            <!-- Success/Error Alerts -->
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-5">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-1">Success!</h5>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-5">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-1">Error!</h5>
                            <p class="mb-0">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-5">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-1">Please fix the following errors:</h5>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                <!-- Create Agent Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-plus-circle text-primary me-2"></i>Create New Voice Agent
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Beta</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('hamsa.voice-agents.create') }}" id="agentForm">
                                @csrf
                                
                                <!-- Basic Information -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label fw-semibold text-dark mb-2">
                                                <i class="fas fa-tag text-primary me-1"></i>Agent Name
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control border @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}"
                                                   placeholder="e.g., Customer Support Agent"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted mt-1">
                                                Give your agent a descriptive name
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <!-- Voice Selection -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="voice_id" class="form-label fw-semibold text-dark mb-2">
                                                <i class="fas fa-user-circle text-primary me-1"></i>Voice
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select border @error('voice_id') is-invalid @enderror" id="voice_id" name="voice_id" required>
                                                <option value="">Select a voice...</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent['voiceRecordId'] }}" {{ old('voice_id') == $agent['voiceRecordId'] ? 'selected' : '' }}>{{ $agent['agentName'] }} ({{ $agent['lang'] }})</option>
                                                
                                                @endforeach
                                            </select>
                                            @error('voice_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Language Selection -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language" class="form-label fw-semibold text-dark mb-2">
                                                <i class="fas fa-language text-primary me-1"></i>Language
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select border @error('language') is-invalid @enderror" id="language" name="language" required>
                                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                                <option value="ar" {{ old('language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                            </select>
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted mt-1">
                                                Select the primary language for your agent
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agent Instructions -->
                                <div class="mb-4">
                                    <label for="prompt" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-comment-dots text-primary me-1"></i>Agent Instructions (Preamble)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control border @error('prompt') is-invalid @enderror" 
                                              id="prompt" 
                                              name="prompt" 
                                              rows="4" 
                                              placeholder="Describe the agent's personality, response style, and behavior guidelines. Example: 'You are a friendly customer support agent who helps users with technical issues. Be patient, empathetic, and provide clear step-by-step solutions.'"
                                              required>{{ old('prompt') }}</textarea>
                                    @error('prompt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Be specific about tone, personality, and response style
                                        </small>
                                        <small class="text-muted" id="charCount">0/4000 characters</small>
                                    </div>
                                </div>

                                <!-- Greeting Message -->
                                <div class="mb-4">
                                    <label for="greeting_message" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-hand-wave text-primary me-1"></i>Greeting Message
                                    </label>
                                    <textarea class="form-control border @error('greeting_message') is-invalid @enderror" 
                                              id="greeting_message" 
                                              name="greeting_message" 
                                              rows="2" 
                                              placeholder="e.g., Hello! Welcome to our support. How can I assist you today?">{{ old('greeting_message') }}</textarea>
                                    @error('greeting_message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Optional greeting message when conversation starts
                                        </small>
                                        <small class="text-muted" id="greetingCharCount">0/500 characters</small>
                                    </div>
                                </div>

                                <!-- Advanced Settings -->
                                <div class="mb-4">
                                    <div class="card border bg-light">
                                        <div class="card-header bg-transparent border-bottom py-2">
                                            <h6 class="card-title mb-0 fw-semibold text-dark">
                                                <i class="fas fa-cog text-primary me-1"></i>Advanced Settings
                                            </h6>
                                        </div>
                                        <div class="card-body py-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="allow_interruptions" value="false">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="checkbox" 
                                                            id="allow_interruptions" 
                                                            name="allow_interruptions" 
                                                            value="true" 
                                                            checked
                                                        >
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

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Your agent configuration is stored securely
                                        </small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                            <i class="fas fa-redo me-1"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold" id="createBtn">
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
                        <div class="card-body p-0">
                            @if (count($agents) > 0)
                                <div class="agents-list">
                                    @foreach ($agents as $agent)
                                        <div class="agent-item p-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-semibold text-dark">{{ $agent['agentName'] ?? 'Unnamed Agent' }}</h6>
                                                <span class="badge bg-light text-dark small">{{ strtoupper($agent['lang'] ?? '-') }}</span>
                                            </div>
                                            <p class="text-muted small mb-2">{{ Str::limit($agent['greetingMessage'] ?? 'No greeting message', 60) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($agent['createdAt'])->format('M j, Y') }}</small>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-play text-success me-2"></i>Test</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>Edit</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-copy text-info me-2"></i>Duplicate</a></li>
                                                        {{--  <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>  --}}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="fas fa-robot fa-3x text-light bg-secondary rounded-circle p-4"></i>
                                    </div>
                                    <h5 class="text-muted">No voice agents yet</h5>
                                    <p class="text-muted small">Create your first voice agent to get started</p>
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

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">Voice Agents Help</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Creating Effective Voice Agents</h6>
                <p>Voice agents use AI to handle conversations with users. Follow these best practices:</p>
                <ul>
                    <li>Provide clear, specific instructions about the agent's role and behavior</li>
                    <li>Define the tone and personality you want the agent to exhibit</li>
                    <li>Include examples of how to handle common scenarios</li>
                    <li>Test your agent thoroughly before deploying to users</li>
                </ul>
                <h6>Voice Selection</h6>
                <p>Choose a voice that matches your agent's purpose:</p>
                <ul>
                    <li><strong>Alloy</strong>: Professional, neutral tone for business contexts</li>
                    <li><strong>Echo</strong>: Warm, engaging for customer service</li>
                    <li><strong>Onyx</strong>: Authoritative for announcements or instructions</li>
                    <li><strong>Nova</strong>: Energetic for marketing or entertainment</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const promptTextarea = document.getElementById('prompt');
    const greetingTextarea = document.getElementById('greeting_message');
    const charCount = document.getElementById('charCount');
    const greetingCharCount = document.getElementById('greetingCharCount');
    const createBtn = document.getElementById('createBtn');
    const agentForm = document.getElementById('agentForm');
    
    // Character count update for prompt
    promptTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count}/4000 characters`;
        
        if (count > 4000) {
            charCount.classList.add('text-danger');
            createBtn.disabled = true;
        } else {
            charCount.classList.remove('text-danger');
            createBtn.disabled = false;
        }
    });
    
    // Character count update for greeting message
    greetingTextarea.addEventListener('input', function() {
        const count = this.value.length;
        greetingCharCount.textContent = `${count}/500 characters`;
        
        if (count > 500) {
            greetingCharCount.classList.add('text-danger');
            createBtn.disabled = true;
        } else {
            greetingCharCount.classList.remove('text-danger');
            createBtn.disabled = false;
        }
    });
    
    // Form submission with loading state
    agentForm.addEventListener('submit', function() {
        if (promptTextarea.value.length > 4000) {
            alert('Instructions exceed 4000 character limit. Please shorten your text.');
            return false;
        }
        
        if (greetingTextarea.value.length > 500) {
            alert('Greeting message exceeds 500 character limit. Please shorten your text.');
            return false;
        }
        
        createBtn.disabled = true;
        createBtn.querySelector('.create-text').classList.add('d-none');
        createBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Initialize character counts
    charCount.textContent = `${promptTextarea.value.length}/4000 characters`;
    greetingCharCount.textContent = `${greetingTextarea.value.length}/500 characters`;
});

// Reset form
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        document.getElementById('agentForm').reset();
        document.getElementById('charCount').textContent = '0/4000 characters';
        document.getElementById('charCount').classList.remove('text-danger');
        document.getElementById('greetingCharCount').textContent = '0/500 characters';
        document.getElementById('greetingCharCount').classList.remove('text-danger');
        document.getElementById('createBtn').disabled = false;
        document.getElementById('createBtn').querySelector('.create-text').classList.remove('d-none');
        document.getElementById('createBtn').querySelector('.loading-text').classList.add('d-none');
    }
}
</script>
@endpush

<style>
.card {
    border-radius: 10px;
}

.form-control, .form-select {
    border-radius: 6px;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.btn {
    border-radius: 6px;
    transition: all 0.2s ease;
}

.alert {
    border-radius: 10px;
}

.badge {
    border-radius: 4px;
    font-weight: 500;
}

.agent-item {
    transition: all 0.2s ease;
    border-bottom: 1px solid #e9ecef;
}

.agent-item:hover {
    background-color: #f8f9fa;
}

.agent-item:last-child {
    border-bottom: none;
}

.tip-item {
    transition: all 0.2s ease;
}

.tip-item:hover {
    transform: translateX(3px);
}

.empty-state-icon {
    opacity: 0.7;
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
    max-height: 400px;
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

.modal-content {
    border-radius: 12px;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>
@endsection