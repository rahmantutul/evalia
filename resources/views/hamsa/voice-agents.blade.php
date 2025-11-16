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
                                    {{-- Voice Testing Modal --}}
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
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" 
                                                            onclick="openVoiceTest('{{ $agent['id'] }}', '{{ $agent['agentName'] }}', '{{ $agent['lang'] }}')">
                                                                <i class="fas fa-play text-success me-2"></i>Test Agent
                                                            </a>
                                                        </li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>Edit</a></li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" 
                                                            onclick="openCloneModal('{{ $agent['id'] }}', '{{ $agent['agentName'] }}')">
                                                                <i class="fas fa-copy text-info me-2"></i>Duplicate
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Voice Testing Modal --}}
                                    <div id="voiceTestModal" class="voice-modal-overlay">
                                        <div class="voice-modal">
                                            <div class="agent-info">
                                                <h4 id="modalAgentName">Agent Name</h4>
                                                <span class="badge" id="modalAgentLang">EN</span>
                                            </div>
                                            
                                            <div class="voice-status">
                                                <div class="voice-animation">
                                                    <div class="voice-circle" id="voiceCircle">
                                                        <i class="fas fa-microphone voice-icon" id="voiceIcon"></i>
                                                    </div>
                                                </div>
                                                <div class="status-text" id="statusText">Ready to Start</div>
                                                <div class="status-subtext" id="statusSubtext">Click start to begin conversation</div>
                                            </div>
                                            
                                            <div class="voice-controls">
                                                <button class="btn-voice btn-start" id="startCallBtn" onclick="startVoiceCall()">
                                                    <i class="fas fa-phone"></i>
                                                    Start Call
                                                </button>
                                                <button class="btn-voice btn-end" id="endCallBtn" onclick="endVoiceCall()" style="display: none;">
                                                    <i class="fas fa-phone-slash"></i>
                                                    End Call
                                                </button>
                                                <button class="btn-voice btn-close-modal" onclick="closeVoiceTest()">
                                                    <i class="fas fa-times"></i>
                                                    Close
                                                </button>
                                            </div>
                                        </div>
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
                                        <p class="text-muted small mb-0">Match voice tone to agents purpose</p>
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
                                        <strong class="text-dark">Consider case</strong>
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
                <p>Voice agents  AI to handle conversations with users. Follow these best practices:</p>
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
    function preventCloseAction() {
        showTemporaryMessage('Please end the call first before closing');
        
        // Add shake animation to modal
        const modal = document.querySelector('.voice-modal');
        modal.classList.add('shake');
        setTimeout(() => {
            modal.classList.remove('shake');
        }, 300);
        
        // Add call-active class to overlay for visual feedback
        const overlay = document.getElementById('voiceTestModal');
        overlay.classList.add('call-active');
        setTimeout(() => {
            overlay.classList.remove('call-active');
        }, 1000);
    }

    // Then update all the prevention checks to use preventCloseAction()
    document.getElementById('voiceTestModal').addEventListener('click', function(e) {
        if (e.target === this) {
            if (!canCloseModal) {
                preventCloseAction();
                return;
            }
            window.closeVoiceTest();
        }
    });
</script>
{{-- Load Hamsa SDK from unpkg --}}
<script type="module">
    import { HamsaVoiceAgent } from 'https://cdn.jsdelivr.net/npm/@hamsa-ai/voice-agents-sdk/+esm';
    
    // Make it globally available
    window.HamsaVoiceAgent = HamsaVoiceAgent;
    
    let currentAgent = null;
    let hamsaAgent = null;
    let isCallActive = false;
    let canCloseModal = true; // Control whether modal can be closed
    
    // Your Hamsa API Key
    const HAMSA_API_KEY = 'f03dbabc-a5f1-424c-a66e-fd9080d83f2e';
    
    window.openVoiceTest = function(agentId, agentName, agentLang) {
        currentAgent = {
            id: agentId,
            name: agentName,
            lang: agentLang
        };
        
        // Update modal content
        document.getElementById('modalAgentName').textContent = agentName;
        document.getElementById('modalAgentLang').textContent = agentLang.toUpperCase();
        
        // Reset UI
        resetVoiceUI();
        
        // Show modal
        document.getElementById('voiceTestModal').classList.add('active');
    };
    
    window.closeVoiceTest = function() {
        // Only allow closing if call is not active or modal is allowed to close
        if (!canCloseModal) {
            // Show a message to the user
            showTemporaryMessage('Please end the call first before closing');
            return;
        }
        
        // End call if active
        if (isCallActive) {
            endVoiceCall();
        }
        
        // Hide modal
        document.getElementById('voiceTestModal').classList.remove('active');
        currentAgent = null;
    };
    
    function resetVoiceUI() {
        document.getElementById('statusText').textContent = 'Ready to Start';
        document.getElementById('statusSubtext').textContent = 'Click start to begin conversation';
        document.getElementById('voiceCircle').classList.remove('listening');
        document.getElementById('voiceIcon').className = 'fas fa-microphone voice-icon';
        document.getElementById('startCallBtn').style.display = 'inline-flex';
        document.getElementById('startCallBtn').disabled = false;
        document.getElementById('endCallBtn').style.display = 'none';
        isCallActive = false;
        canCloseModal = true; // Allow closing when call ends
    }
    
    function showTemporaryMessage(message) {
        // Create or get existing message element
        let messageEl = document.getElementById('tempMessage');
        if (!messageEl) {
            messageEl = document.createElement('div');
            messageEl.id = 'tempMessage';
            messageEl.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #ef4444;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            document.body.appendChild(messageEl);
        }
        
        messageEl.textContent = message;
        messageEl.style.display = 'block';
        
        // Hide after 3 seconds
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 3000);
    }
    
    window.startVoiceCall = async function() {
        if (!currentAgent) {
            alert('No agent selected');
            return;
        }
        
        try {
            // Update UI
            document.getElementById('statusText').textContent = 'Connecting...';
            document.getElementById('statusSubtext').textContent = 'Initializing voice agent';
            document.getElementById('startCallBtn').disabled = true;
            
            // Prevent modal from closing during call
            canCloseModal = false;
            
            // Initialize Hamsa SDK
            if (!hamsaAgent) {
                hamsaAgent = new HamsaVoiceAgent(HAMSA_API_KEY);
                
                // Setup event listeners
                hamsaAgent.on('callStarted', () => {
                    console.log('Call started');
                    isCallActive = true;
                    canCloseModal = false; // Prevent closing during active call
                    document.getElementById('statusText').textContent = 'Connected';
                    document.getElementById('statusSubtext').textContent = 'Agent is ready';
                    document.getElementById('startCallBtn').style.display = 'none';
                    document.getElementById('endCallBtn').style.display = 'inline-flex';
                });
                
                hamsaAgent.on('listening', () => {
                    console.log('Agent is listening');
                    document.getElementById('statusText').textContent = 'Listening';
                    document.getElementById('statusSubtext').textContent = 'Speak now...';
                    document.getElementById('voiceCircle').classList.add('listening');
                    document.getElementById('voiceIcon').className = 'fas fa-microphone voice-icon';
                });
                
                hamsaAgent.on('speaking', () => {
                    console.log('Agent is speaking');
                    document.getElementById('statusText').textContent = 'Speaking';
                    document.getElementById('statusSubtext').textContent = 'Agent is responding';
                    document.getElementById('voiceCircle').classList.remove('listening');
                    document.getElementById('voiceIcon').className = 'fas fa-volume-up voice-icon';
                    
                    // Prevent closing while agent is speaking
                    canCloseModal = false;
                });
                
                hamsaAgent.on('callEnded', () => {
                    console.log('Call ended');
                    document.getElementById('statusText').textContent = 'Call Ended';
                    document.getElementById('statusSubtext').textContent = 'Conversation completed';
                    
                    // Allow closing after call ends
                    canCloseModal = true;
                    
                    setTimeout(resetVoiceUI, 1500);
                });
                
                hamsaAgent.on('error', (error) => {
                    console.error('Hamsa error:', error);
                    // Allow closing on error
                    canCloseModal = true;
                    alert('Error: ' + (error.message || 'Failed to connect to voice agent'));
                    resetVoiceUI();
                });
            }
            
            // Start the call
            await hamsaAgent.start({
                agentId: currentAgent.id
            });
            
        } catch (error) {
            console.error('Failed to start call:', error);
            // Allow closing on error
            canCloseModal = true;
            alert('Failed to start call: ' + error.message);
            resetVoiceUI();
        }
    };
    
    window.endVoiceCall = function() {
        if (hamsaAgent && isCallActive) {
            hamsaAgent.end();
        }
        // Allow closing after manually ending call
        canCloseModal = true;
        resetVoiceUI();
    };
    
    // Close modal when clicking outside - with protection
    document.getElementById('voiceTestModal').addEventListener('click', function(e) {
        if (e.target === this) {
            // Check if we're allowed to close
            if (!canCloseModal) {
                showTemporaryMessage('Please end the call first before closing');
                return;
            }
            window.closeVoiceTest();
        }
    });
    
    // Close modal on ESC key - with protection
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('voiceTestModal').classList.contains('active')) {
            // Check if we're allowed to close
            if (!canCloseModal) {
                showTemporaryMessage('Please end the call first before closing');
                e.preventDefault(); // Prevent default ESC behavior
                return;
            }
            window.closeVoiceTest();
        }
    });
    
    // Also disable the close button during active calls
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-close-modal')) {
            // Check if we're allowed to close
            if (!canCloseModal) {
                showTemporaryMessage('Please end the call first before closing');
                e.preventDefault();
                e.stopPropagation();
                return;
            }
        }
    });
</script>
<script>
    let currentCloneAgentId = null;
    let currentCloneAgentName = null;

    // Open clone modal
    function openCloneModal(agentId, agentName) {
        currentCloneAgentId = agentId;
        currentCloneAgentName = agentName;
        
        // Update modal content
        document.getElementById('cloneOriginalName').textContent = `Cloning: ${agentName}`;
        document.getElementById('cloneAgentName').value = `${agentName} (Copy)`;
        
        // Reset UI
        document.getElementById('cloneStatus').style.display = 'none';
        document.getElementById('confirmCloneBtn').disabled = false;
        document.getElementById('cloneAgentName').disabled = false;
        
        // Show modal
        document.getElementById('cloneAgentModal').classList.add('active');
        
        // Focus on input
        setTimeout(() => {
            document.getElementById('cloneAgentName').focus();
            document.getElementById('cloneAgentName').select();
        }, 300);
    }

    // Close clone modal
    function closeCloneModal() {
        document.getElementById('cloneAgentModal').classList.remove('active');
        currentCloneAgentId = null;
        currentCloneAgentName = null;
    }

    // Confirm and clone agent
    async function confirmClone() {
        const newAgentName = document.getElementById('cloneAgentName').value.trim();
        
        if (!newAgentName) {
            alert('Please enter a name for the cloned agent');
            return;
        }
        
        if (!currentCloneAgentId) {
            alert('No agent selected for cloning');
            return;
        }
        
        try {
            // Show loading state
            document.getElementById('cloneStatus').style.display = 'block';
            document.getElementById('confirmCloneBtn').disabled = true;
            document.getElementById('cloneAgentName').disabled = true;
            
            // Make API call to clone agent
            const response = await fetch('/clone-voice-agents', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    agentId: currentCloneAgentId,
                    agentName: newAgentName
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Show success message
                showToast('Agent cloned successfully!', 'success');
                
                // Close modal
                closeCloneModal();
                
                // Reload the page to show the new agent
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } else {
                throw new Error(data.message || data.error || 'Failed to clone agent');
            }
            
        } catch (error) {
            console.error('Clone error:', error);
            
            // Show error message
            showToast(error.message || 'Failed to clone agent', 'error');
            
            // Reset UI
            document.getElementById('cloneStatus').style.display = 'none';
            document.getElementById('confirmCloneBtn').disabled = false;
            document.getElementById('cloneAgentName').disabled = false;
        }
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 300px;
            `;
            document.body.appendChild(toastContainer);
        }
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Close modal when clicking outside
    document.getElementById('cloneAgentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCloneModal();
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('cloneAgentModal').classList.contains('active')) {
            closeCloneModal();
        }
    });

    // Allow Enter key to confirm clone
    document.getElementById('cloneAgentName').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmClone();
        }
    });
</script>
@endpush
<style>
    .voice-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.2s ease;
    }
    
    .voice-modal-overlay.active {
        display: flex;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .voice-modal {
        background: white;
        border-radius: 16px;
        padding: 32px;
        max-width: 440px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .agent-info {
        text-align: center;
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .agent-info h4 {
        color: #0f172a;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 8px 0;
        letter-spacing: -0.01em;
    }
    
    .agent-info .badge {
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .voice-status {
        text-align: center;
        margin: 32px 0;
    }
    
    .voice-animation {
        width: 96px;
        height: 96px;
        margin: 0 auto 24px;
        position: relative;
    }
    
    .voice-circle {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .voice-circle.listening {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .voice-circle.listening::before,
    .voice-circle.listening::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        border: 2px solid #10b981;
        opacity: 0;
        animation: ripple 2s ease-out infinite;
    }
    
    .voice-circle.listening::before {
        width: 116px;
        height: 116px;
    }
    
    .voice-circle.listening::after {
        width: 136px;
        height: 136px;
        animation-delay: 0.6s;
    }
    
    @keyframes ripple {
        0% {
            opacity: 0.6;
            transform: scale(0.8);
        }
        100% {
            opacity: 0;
            transform: scale(1);
        }
    }
    
    .voice-icon {
        font-size: 36px;
        color: white;
    }
    
    .status-text {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
        letter-spacing: -0.01em;
    }
    
    .status-subtext {
        font-size: 13px;
        color: #64748b;
        font-weight: 400;
    }
    
    .voice-controls {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 28px;
    }
    
    .btn-voice {
        padding: 10px 24px;
        border-radius: 10px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.01em;
    }
    
    .btn-voice:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-start {
        background: #3b82f6;
        color: white;
    }
    
    .btn-start:hover:not(:disabled) {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-start:active:not(:disabled) {
        transform: translateY(0);
    }
    
    .btn-end {
        background: #ef4444;
        color: white;
    }
    
    .btn-end:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .btn-end:active {
        transform: translateY(0);
    }
    
    .btn-close-modal {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-close-modal:hover {
        background: #e2e8f0;
    }
    
    .btn-voice i {
        font-size: 13px;
    }
    /* Add these styles to your existing CSS */
    .voice-modal-overlay.call-active {
        cursor: not-allowed;
    }

    .voice-modal-overlay.call-active .voice-modal {
        pointer-events: all; /* Allow interactions inside modal */
    }

    .voice-modal-overlay.call-active::before {
        content: 'Please end the call first before closing';
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #ef4444;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        z-index: 1;
        white-space: nowrap;
    }

    /* Shake animation for modal when trying to close during call */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .voice-modal.shake {
        animation: shake 0.3s ease-in-out;
    }
</style>
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