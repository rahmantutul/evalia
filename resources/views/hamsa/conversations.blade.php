@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-phone"></i> Voice Conversations</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    <h5>Start New Conversation</h5>
                    <form method="POST" action="{{ url('/hamsa/conversations/start') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="agent_id">Voice Agent *</label>
                            <select class="form-control" id="agent_id" name="agent_id" required>
                                <option value="">Select a voice agent...</option>
                                <!-- Will be populated dynamically -->
                                <option value="agent_123">Customer Support Agent</option>
                                <option value="agent_456">Sales Representative</option>
                            </select>
                            <small class="form-text text-muted">Create voice agents first in the Voice Agents section</small>
                        </div>

                        <div class="form-group">
                            <label for="user_phone_number">Phone Number *</label>
                            <input type="tel" class="form-control" id="user_phone_number" name="user_phone_number" 
                                   placeholder="+1234567890" required>
                            <small class="form-text text-muted">Enter phone number with country code</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-phone"></i> Start Voice Call
                        </button>
                    </form>

                    <hr>

                    <h5>Active Conversations</h5>
                    <div class="text-center py-4">
                        <i class="fas fa-phone fa-3x text-muted mb-3"></i>
                        <h5>No active conversations</h5>
                        <p class="text-muted">Start a conversation to see active calls here.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> About Voice Conversations</h5>
                </div>
                <div class="card-body">
                    <p>Start AI-powered voice conversations with your customers using voice agents.</p>
                    
                    <h6>Features:</h6>
                    <ul>
                        <li>Natural voice interactions</li>
                        <li>Multi-language support</li>
                        <li>Real-time conversation tracking</li>
                        <li>Customizable agent behavior</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> 
                        <strong>Tip:</strong> Create and train your voice agents first for best results.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection