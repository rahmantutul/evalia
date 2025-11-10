@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-phone text-primary me-2"></i>Voice Conversations
                    </h1>
                    <p class="text-muted">Start AI-powered voice conversations with your customers and contacts</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-comments text-info me-1"></i>Live Calls
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
                <!-- Start Conversation Section -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white py-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-play-circle text-primary me-2"></i>Start New Conversation
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Live</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <form method="POST" action="{{ url('/hamsa/conversations/start') }}" id="conversationForm">
                                @csrf
                                
                                <!-- Voice Agent Selection -->
                                <div class="mb-5">
                                    <label for="agent_id" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-robot text-primary me-2"></i>Voice Agent
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg border-2" id="agent_id" name="agent_id" required>
                                        <option value="">Select a voice agent...</option>
                                        <!-- Will be populated dynamically -->
                                        <option value="agent_123">🤖 Customer Support Agent</option>
                                        <option value="agent_456">💼 Sales Representative</option>
                                        <option value="agent_789">🏥 Healthcare Assistant</option>
                                        <option value="agent_101">🎓 Education Advisor</option>
                                    </select>
                                    <div class="mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Create and configure voice agents in the Voice Agents section
                                        </small>
                                    </div>
                                </div>

                                <!-- Phone Number Input -->
                                <div class="mb-5">
                                    <label for="user_phone_number" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-mobile-alt text-primary me-2"></i>Phone Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-2">
                                            <i class="fas fa-flag text-muted"></i>
                                        </span>
                                        <input type="tel" 
                                               class="form-control border-2" 
                                               id="user_phone_number" 
                                               name="user_phone_number" 
                                               placeholder="+1 (555) 123-4567" 
                                               required
                                               pattern="[\+]?[1-9][0-9 \-\(\)\.]{7,}">
                                    </div>
                                    <div class="mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Include country code (e.g., +1 for US, +44 for UK)
                                        </small>
                                    </div>
                                </div>

                                <!-- Conversation Settings -->
                                <div class="row g-4 mb-5">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-header bg-transparent border-0 py-3">
                                                <h6 class="card-title mb-0 fw-semibold text-dark">
                                                    <i class="fas fa-cog text-primary me-2"></i>Conversation Settings
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="record_conversation" name="record_conversation" checked>
                                                            <label class="form-check-label text-dark" for="record_conversation">
                                                                Record conversation
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="send_transcript" name="send_transcript">
                                                            <label class="form-check-label text-dark" for="send_transcript">
                                                                Send transcript via SMS
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="allow_callbacks" name="allow_callbacks" checked>
                                                            <label class="form-check-label text-dark" for="allow_callbacks">
                                                                Allow callbacks
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="background_noise" name="background_noise">
                                                            <label class="form-check-label text-dark" for="background_noise">
                                                                Noise cancellation
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
                                            Calls are encrypted and secure
                                        </small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="startCallBtn">
                                            <i class="fas fa-phone me-2"></i>
                                            <span class="call-text">Start Voice Call</span>
                                            <span class="loading-text d-none">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Connecting...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Active Conversations Section -->
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white py-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-clock text-primary me-2"></i>Active Conversations
                                </h4>
                                <span class="badge bg-danger bg-opacity-10 text-danger">Live</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <div class="text-center py-5">
                                <div class="empty-state-icon mb-4">
                                    <i class="fas fa-phone-slash fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted mb-3">No Active Conversations</h4>
                                <p class="text-muted mb-4">Start a voice call to see active conversations here.</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <button class="btn btn-primary" onclick="document.getElementById('conversationForm').scrollIntoView()">
                                        <i class="fas fa-phone me-2"></i>Start First Call
                                    </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-sync-alt me-2"></i>Refresh
                                    </button>
                                </div>
                            </div>

                            <!-- Sample Active Call (Hidden by default) -->
                            <div class="active-call-sample d-none">
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="call-status-indicator bg-success rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                                                <div>
                                                    <h6 class="fw-semibold text-dark mb-1">+1 (555) 123-4567</h6>
                                                    <small class="text-muted">Customer Support Agent • 05:23</small>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-eye text-primary me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-pause text-warning me-2"></i>Pause</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-phone-slash me-2"></i>End Call</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="call-actions d-flex gap-2">
                                            <button class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-volume-up me-1"></i>Listen
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-comment me-1"></i>Send Message
                                            </button>
                                            <button class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-pause me-1"></i>Pause
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Information -->
                <div class="col-lg-4">
                    <!-- Quick Start Guide -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-rocket text-primary me-2"></i>Quick Start Guide
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="guide-steps">
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">1</div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Create Voice Agent</h6>
                                        <p class="text-muted small mb-0">Design your AI agent's personality and voice</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">2</div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Enter Phone Number</h6>
                                        <p class="text-muted small mb-0">Provide the contact's number with country code</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start mb-4">
                                    <div class="step-number bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">3</div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Configure Settings</h6>
                                        <p class="text-muted small mb-0">Adjust recording and notification preferences</p>
                                    </div>
                                </div>
                                <div class="step-item d-flex align-items-start">
                                    <div class="step-number bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">4</div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Start Conversation</h6>
                                        <p class="text-muted small mb-0">Initiate the AI-powered voice call</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-star text-warning me-2"></i>Key Features
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="features-list">
                                <div class="feature-item d-flex align-items-start mb-3">
                                    <i class="fas fa-comments text-primary mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Natural Conversations</strong>
                                        <p class="text-muted small mb-0">AI-powered natural voice interactions</p>
                                    </div>
                                </div>
                                <div class="feature-item d-flex align-items-start mb-3">
                                    <i class="fas fa-globe text-info mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Multi-language</strong>
                                        <p class="text-muted small mb-0">Support for multiple languages</p>
                                    </div>
                                </div>
                                <div class="feature-item d-flex align-items-start mb-3">
                                    <i class="fas fa-chart-line text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Real-time Analytics</strong>
                                        <p class="text-muted small mb-0">Live conversation tracking and insights</p>
                                    </div>
                                </div>
                                <div class="feature-item d-flex align-items-start">
                                    <i class="fas fa-cogs text-warning mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Customizable Behavior</strong>
                                        <p class="text-muted small mb-0">Tailor agent responses and personality</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Pro Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info border-0 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-robot text-primary mt-1 me-2"></i>
                                    <div>
                                        <strong>Agent Training</strong>
                                        <p class="small mb-0">Create and test your voice agents thoroughly before deployment</p>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-success border-0 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-mobile-alt text-success mt-1 me-2"></i>
                                    <div>
                                        <strong>Phone Format</strong>
                                        <p class="small mb-0">Always include country code for international numbers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning border-0">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-record-vinyl text-warning mt-1 me-2"></i>
                                    <div>
                                        <strong>Recording</strong>
                                        <p class="small mb-0">Enable conversation recording for quality assurance</p>
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
    const phoneInput = document.getElementById('user_phone_number');
    const startCallBtn = document.getElementById('startCallBtn');
    const conversationForm = document.getElementById('conversationForm');
    
    // Phone number formatting
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (!value.startsWith('+')) {
                value = '+' + value;
            }
        }
        e.target.value = value;
    });
    
    // Form submission with loading state
    conversationForm.addEventListener('submit', function(e) {
        // Basic phone validation
        const phoneValue = phoneInput.value.replace(/\D/g, '');
        if (phoneValue.length < 10) {
            alert('Please enter a valid phone number with country code.');
            e.preventDefault();
            return false;
        }
        
        startCallBtn.disabled = true;
        startCallBtn.querySelector('.call-text').classList.add('d-none');
        startCallBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Initialize phone input with international format hint
    phoneInput.addEventListener('focus', function() {
        if (!this.value) {
            this.placeholder = '+1 (555) 123-4567';
        }
    });
    
    phoneInput.addEventListener('blur', function() {
        this.placeholder = '+1 (555) 123-4567';
    });
});

// Function to test call functionality (for demo purposes)
function testCall() {
    const agentSelect = document.getElementById('agent_id');
    const phoneInput = document.getElementById('user_phone_number');
    
    if (!agentSelect.value) {
        alert('Please select a voice agent first.');
        return;
    }
    
    if (!phoneInput.value) {
        alert('Please enter a phone number first.');
        return;
    }
    
    // Simulate call start
    const startCallBtn = document.getElementById('startCallBtn');
    startCallBtn.disabled = true;
    startCallBtn.querySelector('.call-text').classList.add('d-none');
    startCallBtn.querySelector('.loading-text').classList.remove('d-none');
    
    // Simulate API call delay
    setTimeout(() => {
        alert('Call initiated successfully! In a real implementation, this would connect the call.');
        startCallBtn.disabled = false;
        startCallBtn.querySelector('.call-text').classList.remove('d-none');
        startCallBtn.querySelector('.loading-text').classList.add('d-none');
    }, 2000);
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

.step-item {
    transition: all 0.3s ease;
}

.step-item:hover {
    transform: translateX(5px);
}

.feature-item {
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateX(5px);
}

.empty-state-icon {
    opacity: 0.5;
}

.call-status-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
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

.step-number {
    font-size: 0.875rem;
    font-weight: 600;
}

.input-group-text {
    border-radius: 8px 0 0 8px;
}

.input-group .form-control {
    border-radius: 0 8px 8px 0;
}
</style>
@endsection