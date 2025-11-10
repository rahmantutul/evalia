@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Job Details
                            </h5>
                            <small class="text-muted">Type: {{ $job['type'] ?? 'UNKNOWN' }}</small>
                        </div>
                        <a href="{{ url('/hamsa/jobs') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Jobs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($job) && !isset($error))
                        @php
                            $jobType = $job['type'] ?? 'UNKNOWN';
                            $jobResponse = $job['jobResponse'] ?? [];
                        @endphp

                        <!-- Basic Info Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" width="30%">Title:</td>
                                                <td><strong>{{ $job['title'] ?? 'N/A' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Type:</td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $jobType }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Status:</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($job['status'] == 'COMPLETED') bg-success
                                                        @elseif($job['status'] == 'FAILED') bg-danger
                                                        @elseif($job['status'] == 'PROCESSING') bg-info
                                                        @else bg-warning @endif">
                                                        {{ $job['status'] ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Job ID:</td>
                                                <td><code class="small">{{ $job['id'] ?? 'N/A' }}</code></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" width="30%">Cost:</td>
                                                <td><strong>{{ $job['cost'] ?? $job['totalCost'] ?? 0 }} credits</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Created:</td>
                                                <td>{{ isset($job['createdAt']) ? \Carbon\Carbon::parse($job['createdAt'])->format('M j, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Updated:</td>
                                                <td>{{ isset($job['updatedAt']) ? \Carbon\Carbon::parse($job['updatedAt'])->format('M j, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VOICE_AGENTS Specific Content -->
                        @if($jobType === 'VOICE_AGENTS')
                            <!-- Call Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">Call Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Duration:</strong> {{ $job['callDuration'] ?? 0 }} seconds<br>
                                            <strong>Started:</strong> 
                                            {{ isset($jobResponse['callStartedAt']) ? \Carbon\Carbon::parse($jobResponse['callStartedAt'])->format('M j, Y H:i:s') : 'N/A' }}<br>
                                            <strong>Ended:</strong> 
                                            {{ isset($jobResponse['callEndedAt']) ? \Carbon\Carbon::parse($jobResponse['callEndedAt'])->format('M j, Y H:i:s') : 'N/A' }}
                                        </div>
                                        <div class="col-md-8">
                                            @if(isset($job['agentDetails']))
                                                <strong>Agent:</strong> {{ $job['agentDetails']['agentName'] ?? 'N/A' }}<br>
                                                <strong>Language:</strong> {{ $job['agentDetails']['lang'] ?? 'N/A' }}<br>
                                                <strong>Voice:</strong> {{ $job['agentDetails']['ttsVoice']['name'] ?? 'N/A' }}<br>
                                                <strong>Greeting:</strong> {{ $job['agentDetails']['greetingMessage'] ?? 'N/A' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Conversation Transcript -->
                            @if(isset($jobResponse['transcription']) && !empty($jobResponse['transcription']))
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Conversation Transcript</h6>
                                    <span class="badge bg-primary">{{ count($jobResponse['transcription']) }} messages</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="conversation-container" style="max-height: 500px; overflow-y: auto;">
                                        @foreach($jobResponse['transcription'] as $index => $message)
                                            @foreach($message as $speaker => $text)
                                                @php
                                                    // Determine if this is the agent or user
                                                    $isAgent = $speaker === ($job['agentDetails']['agentName'] ?? 'Agent');
                                                    $speakerType = $isAgent ? 'Agent' : 'User';
                                                    $speakerDisplayName = $isAgent ? ($job['agentDetails']['agentName'] ?? 'Agent') : 'User';
                                                @endphp
                                                <div class="message-row p-3 {{ $isAgent ? 'bg-light' : '' }} border-bottom">
                                                    <div class="d-flex align-items-start">
                                                        <div class="message-avatar me-3">
                                                            @if($isAgent)
                                                                <span class="badge bg-success rounded-circle p-2">
                                                                    <i class="fas fa-robot"></i>
                                                                </span>
                                                            @else
                                                                <span class="badge bg-primary rounded-circle p-2">
                                                                    <i class="fas fa-user"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="message-content flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <strong class="text-{{ $isAgent ? 'success' : 'primary' }}">
                                                                    {{ $speakerDisplayName }}
                                                                </strong>
                                                                <small class="text-muted">#{{ $index + 1 }}</small>
                                                            </div>
                                                            <p class="mb-0">{{ $text }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Voice Configuration -->
                            @if(isset($job['agentDetails']['ttsVoice']))
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">Voice Configuration</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Voice Name:</strong> {{ $job['agentDetails']['ttsVoice']['name'] ?? 'N/A' }}<br>
                                            <strong>Language:</strong> {{ $job['agentDetails']['ttsVoice']['language'] ?? 'N/A' }}<br>
                                            <strong>Provider:</strong> {{ $job['agentDetails']['ttsVoice']['provider'] ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tags:</strong> 
                                            @if(isset($job['agentDetails']['ttsVoice']['tags']) && is_array($job['agentDetails']['ttsVoice']['tags']))
                                                @foreach($job['agentDetails']['ttsVoice']['tags'] as $tag)
                                                    <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        <!-- TRANSCRIPTION Specific Content -->
                        @elseif($jobType === 'TRANSCRIPTION')
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">Transcription Results</h6>
                                </div>
                                <div class="card-body">

                                    <strong>Audio Input:</strong>
                                    <div class="m-2">
                                        <audio controls class="w-100">
                                            <source src="{{ $job['mediaUrl'] }}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                    @if(isset($jobResponse['detected_language']))
                                        <div class="mb-3">
                                            <strong>Detected Language:</strong> 
                                            <span class="badge bg-info">{{ strtoupper($jobResponse['detected_language']) }}</span>
                                        </div>
                                    @endif

                                    <!-- Full Transcription Text -->
                                    @if(isset($jobResponse['transcription']))
                                    <div class="mb-4">
                                        <strong>Full Transcription:</strong>
                                        <div class="mt-2 p-3 bg-light rounded" style="max-height: 300px; overflow-y: auto;">
                                            {{ $jobResponse['transcription'] }}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Speaker Diarization -->
                                    @if(isset($jobResponse['diarization']) && !empty($jobResponse['diarization']))
                                    <div>
                                        <strong>Speaker Segments:</strong>
                                        <div class="mt-2" style="max-height: 400px; overflow-y: auto;">
                                            @foreach($jobResponse['diarization'] as $segment)
                                            <div class="border-bottom pb-2 mb-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <span class="badge bg-secondary">{{ $segment['speaker'] ?? 'Unknown' }}</span>
                                                    <small class="text-muted">
                                                        {{ number_format($segment['start'] ?? 0, 2) }}s - {{ number_format($segment['end'] ?? 0, 2) }}s
                                                    </small>
                                                </div>
                                                <p class="mb-0 mt-1">{{ $segment['text'] ?? '' }}</p>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        <!-- TTS Specific Content -->
                        @elseif($jobType === 'TTS')
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">Text-to-Speech Results</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if(isset($job['voiceName']))
                                                <strong>Voice:</strong> {{ $job['voiceName'] }}<br>
                                            @endif
                                            @if(isset($job['usageTime']))
                                                <strong>Usage Time:</strong> {{ $job['usageTime'] }} seconds<br>
                                            @endif
                                            @if(isset($job['systemModelKey']))
                                                <strong>Model:</strong> {{ $job['systemModelKey'] }}<br>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Audio Player -->
                                            @if(isset($jobResponse['ttsMediaFile']) || isset($job['mediaUrl']))
                                                <strong>Audio Output:</strong>
                                                <div class="mt-2">
                                                    <audio controls class="w-100">
                                                        <source src="{{ $job['mediaUrl'] }}" type="audio/wav">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Input Text -->
                                    @if(isset($jobResponse['text']))
                                    <div class="mt-4">
                                        <strong>Input Text:</strong>
                                        <div class="mt-2 p-3 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                                            {{ $jobResponse['text'] }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        <!-- AI_CONTENT Specific Content -->
                        @elseif($jobType === 'AI_CONTENT')
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">AI Content Analysis</h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($jobResponse['detected_language']))
                                        <div class="mb-3">
                                            <strong>Detected Language:</strong> 
                                            <span class="badge bg-info">{{ strtoupper($jobResponse['detected_language']) }}</span>
                                        </div>
                                    @endif

                                    <!-- Summary -->
                                    @if(isset($jobResponse['summary']))
                                    <div class="mb-4">
                                        <strong>Summary:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {{ $jobResponse['summary'] }}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Introduction -->
                                    @if(isset($jobResponse['introduction']))
                                    <div class="mb-4">
                                        <strong>Introduction:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {{ $jobResponse['introduction'] }}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Related Job -->
                                    @if(isset($job['relevantJobId']))
                                    <div>
                                        <strong>Related Transcription Job:</strong>
                                        <code>{{ $job['relevantJobId'] }}</code>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        <!-- Default for Unknown Types -->
                        @else
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">Job Data</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 rounded small" style="max-height: 400px; overflow-y: auto;"><code>@json($job, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</code></pre>
                                </div>
                            </div>
                        @endif

                        <!-- Additional Technical Details -->
                        <div class="card mb-4">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Technical Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if(isset($job['systemModelKey']))
                                            <strong>Model:</strong> {{ $job['systemModelKey'] }}<br>
                                        @endif
                                        @if(isset($job['processingType']))
                                            <strong>Processing Type:</strong> {{ $job['processingType'] }}<br>
                                        @endif
                                        @if(isset($job['usageTime']))
                                            <strong>Usage Time:</strong> {{ $job['usageTime'] }}<br>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if(isset($job['apiKeyId']))
                                            <strong>API Key ID:</strong> <code class="small">{{ $job['apiKeyId'] }}</code><br>
                                        @endif
                                        @if(isset($job['userId']))
                                            <strong>User ID:</strong> <code class="small">{{ $job['userId'] }}</code><br>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Section -->
                        <div class="card">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex gap-2 flex-wrap align-items-center">
                                    <!-- For TTS, show both player and download -->
                                    @if($jobType === 'TTS' && (isset($jobResponse['mediaUrl']) || isset($job['mediaUrl'])))
                                        @php
                                            $audioUrl = $jobResponse['mediaUrl'] ?? $job['mediaUrl'];
                                        @endphp
                                        <div class="d-flex align-items-center bg-light p-2 rounded me-2">
                                            <span class="me-2 small text-nowrap">Listen:</span>
                                            <audio controls style="height: 35px; min-width: 200px;">
                                                <source src="{{ $audioUrl }}" type="audio/wav">
                                                <source src="{{ $audioUrl }}" type="audio/mpeg">
                                            </audio>
                                        </div>
                                        
                                        <a href="{{ $audioUrl }}" 
                                        class="btn btn-success btn-sm"
                                        target="_blank"
                                        download="tts-output-{{ $job['id'] }}.wav">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                        
                                    <!-- For other types -->
                                    @else
                                        @if(isset($job['url']) && $job['status'] === 'COMPLETED')
                                            <a href="{{ $job['url'] }}" 
                                            class="btn btn-success btn-sm" 
                                            target="_blank"
                                            download>
                                                <i class="fas fa-download me-1"></i> Download Result
                                            </a>
                                        @endif
                                        
                                        @if(isset($job['mediaUrl']) && $job['status'] === 'COMPLETED')
                                            <a href="{{ $job['mediaUrl'] }}" 
                                            class="btn btn-outline-success btn-sm" 
                                            target="_blank"
                                            download>
                                                <i class="fas fa-file-audio me-1"></i> Download Media
                                            </a>
                                        @endif
                                    @endif
                                    
                                    <!-- Back Button -->
                                    <a href="{{ url('/hamsa/jobs') }}" 
                                    class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-arrow-left me-1"></i> Back to List
                                    </a>
                                </div>
                                
                                <!-- Audio Troubleshooting Tips -->
                                @if($jobType === 'TTS')
                                <div class="mt-3 p-2 bg-warning bg-opacity-10 rounded small">
                                    <strong>Audio not playing?</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Click the download button to save the file</li>
                                        <li>Try opening the downloaded file with your media player</li>
                                        <li>The audio URL might require direct download due to security restrictions</li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>

                    @else
                        <!-- Error State -->
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                            <h5>Job Not Found</h5>
                            <p class="text-muted">{{ $error ?? 'The requested job could not be found.' }}</p>
                            <a href="{{ url('/hamsa/jobs') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Jobs
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.message-row {
    transition: background-color 0.2s ease;
}
.message-row:hover {
    background-color: #f8f9fa !important;
}
.conversation-container {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}
.conversation-container::-webkit-scrollbar {
    width: 6px;
}
.conversation-container::-webkit-scrollbar-track {
    background: #f7fafc;
}
.conversation-container::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}
.message-avatar .badge {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}
audio {
    border-radius: 8px;
    background: #f8f9fa;
    min-width: 250px;
}
</style>
@endpush