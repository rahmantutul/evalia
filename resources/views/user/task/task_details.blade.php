@extends('user.layouts.app')
@push('styles')
<style>
  /* Lightweight color scheme */
  :root {
    --primary: #4361ee;
    --primary-light: #e6e9ff;
    --success: #2ecc71;
    --success-light: #e8f8f0;
    --warning: #f39c12;
    --warning-light: #fef5e6;
    --danger: #e74c3c;
    --danger-light: #fdedec;
    --dark: #2c3e50;
    --light: #f8f9fa;
    --gray: #95a5a6;
  }

  /* Base styles */
  .professional-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 1.5rem;
    height: 100%;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #eee;
  }

  .professional-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .card-header {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.1rem;
  }

  /* Top bar */
  .top-bar {
    background: white;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 2rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    border: 1px solid #eee;
  }

  .duration {
    display: flex;
    align-items: center;
    color: var(--dark);
    font-weight: 500;
  }

  /* Toggle switch */
  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 180px;
    height: 34px;
    margin-bottom: 1.5rem;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--primary);
    transition: .4s;
    border-radius: 34px;
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked + .toggle-slider {
    background-color: var(--primary);
  }

  input:checked + .toggle-slider:before {
    transform: translateX(146px);
  }

  .toggle-label {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 12px;
    font-weight: 500;
  }

  .toggle-label.left {
    left: 15px;
  }

  .toggle-label.right {
    right: 15px;
  }

  /* Sentiment bar */
  .sentiment-bar-container {
    padding: 0.5rem 0;
  }

  .sentiment-bar {
    height: 10px;
    background: linear-gradient(90deg, var(--success) 40%, var(--warning) 55%, var(--danger) 80%);
    border-radius: 5px;
    margin: 1rem 0;
    position: relative;
  }

  .sentiment-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: var(--gray);
  }

  /* WPM card */
  .wpm-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--primary-light) 0%, white 100%);
  }

  .wpm-value {
    font-size: 3.5rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
    margin: 0.5rem 0;
  }

  .wpm-unit {
    color: var(--gray);
    font-size: 0.9rem;
  }

  /* Tables */
  .sentiment-table, .pause-delay-table {
    width: 100%;
    border-collapse: collapse;
  }

  .sentiment-table th, .pause-delay-table th {
    text-align: left;
    padding: 0.75rem;
    font-weight: 500;
    color: var(--gray);
    font-size: 0.85rem;
    border-bottom: 1px solid #eee;
  }

  .sentiment-table td, .pause-delay-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
  }

  /* Audio player */
  .audio-player {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem 0;
  }

  .audio-controls {
    display: flex;
    gap: 0.5rem;
  }

  .audio-btn {
    background: none;
    border: none;
    color: var(--primary);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .audio-btn:hover {
    background: var(--primary-light);
  }

  .audio-progress {
    flex-grow: 1;
    height: 6px;
    background: #eee;
    border-radius: 3px;
    position: relative;
    cursor: pointer;
  }

  .audio-progress-filled {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 30%;
    background: var(--primary);
    border-radius: 3px;
  }

  .audio-time {
    font-size: 0.8rem;
    color: var(--gray);
    min-width: 70px;
    text-align: center;
  }

  /* Summary & Transcription */
  .summary-section {
    background: var(--light);
    padding: 1rem;
    border-radius: 8px;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--dark);
    white-space: pre-line;
    margin-bottom: 1rem;
  }

  .transcription-textarea {
    width: 100%;
    min-height: 200px;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 1rem;
    font-family: inherit;
    font-size: 0.9rem;
    line-height: 1.6;
    resize: none;
    background: var(--light);
  }

  .translated-container {
    margin-top: 1rem;
    border-top: 1px solid #eee;
    padding-top: 1rem;
  }

  .translated-header {
    font-weight: 500;
    color: var(--dark);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .translated-content {
    font-size: 0.9rem;
    color: var(--gray);
    line-height: 1.6;
    white-space: pre-line;
  }

  /* Loudness bars */
  .loudness-bars {
    padding: 1rem 0;
  }

  .loudness-bar {
    margin-bottom: 1rem;
  }

  .loudness-bar-label {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .loudness-bar-fill {
    height: 10px;
    border-radius: 5px;
  }

  .loudness-low {
    width: 58%;
    background: var(--danger);
  }

  .loudness-optimal {
    width: 0%;
    background: var(--success);
  }

  .loudness-high {
    width: 41%;
    background: var(--warning);
  }

  .loudness-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: var(--gray);
    margin-top: 0.5rem;
  }

  /* Animations */
  .animate-fade {
    animation: fadeIn 0.5s ease-out forwards;
  }

  .delay-1 { animation-delay: 0.1s; opacity: 0; }
  .delay-2 { animation-delay: 0.2s; opacity: 0; }
  .delay-3 { animation-delay: 0.3s; opacity: 0; }
  .delay-4 { animation-delay: 0.4s; opacity: 0; }

  @keyframes fadeIn {
    to { opacity: 1; }
  }

  /* Tooltip icon */
  .info-icon {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--primary-light);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    cursor: help;
  }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
  <!-- Top Call Duration Bar -->
  <div class="top-bar animate-fade">
    <div class="duration">
      <i class="bi bi-clock me-2"></i>Call Duration: {{ $data['call_duration']['call_duration'] ?? 'N/A' }}
    </div>
    <div class="duration">
      <i class="bi bi-mic me-2"></i>Talking Duration: 
      {{ $data['pause_delay_information']['talking_duration']['agent'] ?? 'N/A' }} (Agent) / 
      {{ $data['pause_delay_information']['talking_duration']['customer'] ?? 'N/A' }} (Customer)
    </div>
  </div>

  {{--  <!-- Agent/Customer toggle -->
  <div class="d-flex justify-content-center animate-fade delay-1">
    <label class="toggle-switch">
      <input type="checkbox" checked>
      <span class="toggle-slider"></span>
      <span class="toggle-label left">Agent</span>
      <span class="toggle-label right">Customer</span>
    </label>
  </div>  --}}

  <div class="row gy-4">
    <!-- Sentiment Analysis Card -->
    <div class="col-lg-7 animate-fade delay-1">
      <div class="professional-card h-100">
        <div class="card-header">
          <span>Sentiment Analysis</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Measures emotional tone throughout the call">i</div>
        </div>
        <div class="sentiment-bar-container">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-primary fw-bold">Interactive Tone</div>
            <div class="badge bg-success bg-opacity-10 text-success p-2">
              <i class="bi bi-check-circle me-1"></i>
              {{ $data['speaker_sentiments']['agent'] ?? 'N/A' }} (Agent) / 
              {{ $data['speaker_sentiments']['customer'] ?? 'N/A' }} (Customer)
            </div>
          </div>
          
          <div class="sentiment-bar"></div>
          <div class="sentiment-labels">
            @php
              // Calculate sentiment percentages from transcriptions
              $agentSentiments = [
                'Positive' => 0,
                'Neutral' => 0,
                'Negative' => 0
              ];
              $customerSentiments = [
                'Positive' => 0,
                'Neutral' => 0,
                'Negative' => 0
              ];
              
              if(isset($data['agent_speakers_transcriptions'])) {
                foreach($data['agent_speakers_transcriptions'] as $transcript) {
                  $agentSentiments[$transcript['sentiment'] ?? 'Neutral']++;
                }
              }
              
              if(isset($data['customer_speakers_transcriptions'])) {
                foreach($data['customer_speakers_transcriptions'] as $transcript) {
                  $customerSentiments[$transcript['sentiment'] ?? 'Neutral']++;
                }
              }
              
              $totalAgent = array_sum($agentSentiments);
              $totalCustomer = array_sum($customerSentiments);
              $total = $totalAgent + $totalCustomer;
            @endphp
            
            <div class="sentiment-label">Positive {{ $total > 0 ? round(($agentSentiments['Positive'] + $customerSentiments['Positive']) / $total * 100) : 0 }}%</div>
            <div class="sentiment-label">Neutral {{ $total > 0 ? round(($agentSentiments['Neutral'] + $customerSentiments['Neutral']) / $total * 100) : 0 }}%</div>
            <div class="sentiment-label">Negative {{ $total > 0 ? round(($agentSentiments['Negative'] + $customerSentiments['Negative']) / $total * 100) : 0 }}%</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Words Per Minute Card -->
    <div class="col-lg-5 animate-fade delay-2">
      <div class="wpm-card h-100">
        <div class="card-header d-flex justify-content-center align-items-center gap-2 mb-3">
          <span>Speech Rate Analysis</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Words spoken per minute with optimal range">i</div>
        </div>
        <div class="mb-4 text-muted">
          <i class="bi bi-graph-up-arrow me-2"></i>Real-time speech rate monitoring
        </div>
        <div class="optimal mt-2 text-muted">
          <i class="bi bi-speedometer2 me-2"></i>Optimal Range: 100-200 wpm
        </div>
        <div class="wpm-value">{{ isset($data['pace']) ? round(max($data['pace']['agent_pace'] ?? 0, $data['pace']['customer_pace'] ?? 0)) : 0 }}</div>
        <div class="wpm-unit">Words Per Minute</div>
      </div>
    </div>

    <!-- Sentiment Timeline Card -->
    <div class="col-lg-4 animate-fade delay-2">
      <div class="professional-card h-100">
        <div class="card-header">
          <span>Sentiment Timeline</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Detailed timeline of sentiment changes">i</div>
        </div>
        <div class="sentiment-table-container">
          <table class="sentiment-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>Duration</th>
                <th>Intensity</th>
              </tr>
            </thead>
            <tbody>
              @if(isset($data['speakers_transcriptions']))
                @foreach(array_slice($data['speakers_transcriptions'], 0, 4) as $transcript)
                  <tr>
                    <td>
                      <span class="badge 
                        @if(($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success bg-opacity-10 text-success
                        @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger bg-opacity-10 text-danger
                        @else bg-warning bg-opacity-10 text-warning @endif p-2">
                        <i class="bi 
                          @if(($transcript['sentiment'] ?? 'Neutral') == 'Positive') bi-emoji-smile
                          @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bi-emoji-frown
                          @else bi-dash-circle @endif me-1"></i>
                        {{ $transcript['sentiment'] ?? 'Neutral' }}
                      </span>
                    </td>
                    <td>
                      @php
                        $start = isset($transcript['start_time']) ? strtotime("1970-01-01 " . substr($transcript['start_time'], 0, 5) . " UTC") : 0;
                        $end = isset($transcript['end_time']) ? strtotime("1970-01-01 " . substr($transcript['end_time'], 0, 5) . " UTC") : 0;
                        $duration = $end - $start;
                        echo gmdate("i:s", $duration);
                      @endphp
                    </td>
                    <td>
                      <div class="progress" style="height: 4px;">
                        <div class="progress-bar 
                          @if(($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success
                          @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger
                          @else bg-warning @endif" 
                          role="progressbar" 
                          style="width: {{ rand(50, 90) }}%">
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-2"></i>No sentiment data available
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Voice Loudness Card -->
    <div class="col-lg-4 animate-fade delay-3">
      <div class="professional-card h-100">
        <div class="card-header">
          <span>Voice Loudness Profile</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Distribution of voice volume levels during call">i</div>
        </div>
        <div class="loudness-bars">
          <div class="loudness-bar">
            <div class="loudness-bar-label">
              <i class="bi bi-volume-down"></i>Low Volume
            </div>
            <div class="loudness-bar-fill loudness-low" style="width: {{ $data['speaker_loudness']['agent']['lower_loudness_percentage'] ?? 0 }}%"></div>
          </div>
          <div class="loudness-bar">
            <div class="loudness-bar-label">
              <i class="bi bi-volume-off"></i>Optimal Volume
            </div>
            <div class="loudness-bar-fill loudness-optimal" style="width: {{ $data['speaker_loudness']['agent']['optimal_loudness_percentage'] ?? 0 }}%"></div>
          </div>
          <div class="loudness-bar">
            <div class="loudness-bar-label">
              <i class="bi bi-volume-up"></i>High Volume
            </div>
            <div class="loudness-bar-fill loudness-high" style="width: {{ $data['speaker_loudness']['agent']['upper_loudness_percentage'] ?? 0 }}%"></div>
          </div>
          
          <div class="loudness-labels">
            <span><i class="bi bi-arrow-down"></i> {{ $data['speaker_loudness']['agent']['lower_loudness_percentage'] ?? 0 }}% Low</span>
            <span><i class="bi bi-check-circle"></i> {{ $data['speaker_loudness']['agent']['optimal_loudness_percentage'] ?? 0 }}% Optimal</span>
            <span><i class="bi bi-arrow-up"></i> {{ $data['speaker_loudness']['agent']['upper_loudness_percentage'] ?? 0 }}% High</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Pause Analysis Card -->
    <div class="col-4 animate-fade delay-3">
      <div class="professional-card">
        <div class="card-header">
          <span>Pause Analysis</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Natural pauses in speech without interruption">i</div>
        </div>
        <table class="pause-delay-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Duration</th>
              <th>Start-End</th>
              <th>Context</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($data['pause_delay_information']['speaker_pause_duration']['agent']) || isset($data['pause_delay_information']['speaker_pause_duration']['customer']))
              @php
                $agentPauses = $data['pause_delay_information']['speaker_pause_duration']['agent'] ?? [];
                $customerPauses = $data['pause_delay_information']['speaker_pause_duration']['customer'] ?? [];
                $allPauses = array_merge($agentPauses, $customerPauses);
              @endphp
              
              @if(count($allPauses) > 0)
                @foreach($allPauses as $pause)
                  <tr>
                    <td>Pause</td>
                    <td>{{ $pause['pause_duration'] ?? 'N/A' }}</td>
                    <td>{{ $pause['pause_start'] ?? 'N/A' }} - {{ $pause['pause_end'] ?? 'N/A' }}</td>
                    <td>{{ $pause['pause_class'] ?? 'N/A' }}</td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-2"></i>No significant pauses detected
                  </td>
                </tr>
              @endif
            @else
              <tr>
                <td colspan="4" class="text-center text-muted py-4">
                  <i class="bi bi-info-circle me-2"></i>Pause data not available
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delay Analysis Card -->
    <div class="col-6 animate-fade delay-4">
      <div class="professional-card">
        <div class="card-header">
          <span>Delay Analysis</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Interruptions causing speech delays">i</div>
        </div>
        <div class="px-4 pt-3 pb-2 text-muted">
          <i class="bi bi-clock-history me-2"></i>Interruptions causing speech delays
        </div>
        
        <table class="pause-delay-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Duration</th>
              <th>Start-End</th>
              <th>Context</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($data['pause_delay_information']['speaker_delay_duration']['agent']) || isset($data['pause_delay_information']['speaker_delay_duration']['customer']))
              @php
                $agentDelays = $data['pause_delay_information']['speaker_delay_duration']['agent'] ?? [];
                $customerDelays = $data['pause_delay_information']['speaker_delay_duration']['customer'] ?? [];
                $allDelays = array_merge($agentDelays, $customerDelays);
              @endphp
              
              @if(count($allDelays) > 0)
                @foreach($allDelays as $delay)
                  <tr>
                    <td>Delay ({{ $delay['delay_class'] ?? 'N/A' }})</td>
                    <td>{{ $delay['delay_duration'] ?? 'N/A' }}</td>
                    <td>{{ $delay['delay_start'] ?? 'N/A' }} - {{ $delay['delay_end'] ?? 'N/A' }}</td>
                    <td>
                      @php
                        $context = 'No context available';
                        if(isset($data['speakers_transcriptions'])) {
                          foreach($data['speakers_transcriptions'] as $transcript) {
                            if(isset($transcript['start_time'], $transcript['end_time'], $delay['delay_start'], $delay['delay_end'])) {
                              $start = strtotime("1970-01-01 " . substr($transcript['start_time'], 0, 5) . " UTC");
                              $end = strtotime("1970-01-01 " . substr($transcript['end_time'], 0, 5) . " UTC");
                              $delayStart = strtotime("1970-01-01 " . substr($delay['delay_start'], 0, 5) . " UTC");
                              $delayEnd = strtotime("1970-01-01 " . substr($delay['delay_end'], 0, 5) . " UTC");
                              
                              if($start >= ($delayStart - 5) && $end <= ($delayEnd + 5)) {
                                $context = $transcript['transcript'] ?? 'No context available';
                                break;
                              }
                            }
                          }
                        }
                      @endphp
                      {{ $context }}
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-2"></i>No significant delays detected
                  </td>
                </tr>
              @endif
            @else
              <tr>
                <td colspan="4" class="text-center text-muted py-4">
                  <i class="bi bi-info-circle me-2"></i>Delay data not available
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right Column - Call Details -->
    <div class="col-lg-6">
      <div class="d-flex flex-column gap-4 h-100">
        <!-- Audio Player -->
        <div class="professional-card animate-fade delay-1">
          <div class="audio-player">
            <div class="audio-controls">
              <button class="audio-btn" title="Skip Backward">
                <i class="bi bi-skip-backward-fill"></i>
              </button>
              <button class="audio-btn" title="Play">
                <i class="bi bi-play-fill"></i>
              </button>
              <button class="audio-btn" title="Skip Forward">
                <i class="bi bi-skip-forward-fill"></i>
              </button>
            </div>
            
            <div class="audio-progress">
              <div class="audio-progress-filled"></div>
            </div>
            
            <div class="audio-time">0:00 / {{ $data['call_duration']['call_duration'] ?? '0:00' }}</div>
            
            <div class="audio-controls">
              <button class="audio-btn" title="Volume">
                <i class="bi bi-volume-up"></i>
              </button>
              @if(isset($data['customer_agent_audio_s3_url']))
                <a href="{{ $data['customer_agent_audio_s3_url'] }}" class="audio-btn" title="Download" download>
                  <i class="bi bi-download"></i>
                </a>
              @endif
            </div>
          </div>
        </div>

        <!-- Call Summary -->
        <div class="professional-card animate-fade delay-2">
          <div class="card-header">
            <span>AI Call Summary</span>
            <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Automatically generated call summary">i</div>
          </div>
          <div class="summary-section">
            {{ $data['transcription_summaries']['detail'] ?? 'No detailed summary available' }}
            
            @if(isset($data['topics']['other']) || isset($data['call_outcome']))
              <div class="mt-3">
                <strong>Key points:</strong>
                <ul>
                  @foreach($data['topics']['other'] ?? [] as $topic)
                    <li>{{ $topic }}</li>
                  @endforeach
                  @if(isset($data['call_outcome']))
                    <li>Call outcome: {{ implode(', ', $data['call_outcome']) }}</li>
                  @endif
                </ul>
              </div>
            @endif
          </div>
          
          <div class="translated-container">
            <div class="translated-header">
              <i class="bi bi-translate"></i> Translated Summary (English)
            </div>
            <div class="translated-content">
              {{ $data['transcription_summaries']['short'] ?? 'No short summary available' }}
            </div>
          </div>
        </div>

        <!-- Call Transcription -->
        <div class="professional-card animate-fade delay-3">
          <div class="card-header">
            <span>Full Call Transcription</span>
            <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Complete automated call transcription">i</div>
          </div>
          
          <div class="px-4 pt-3 pb-2">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="showTimestamps" checked>
              <label class="form-check-label" for="showTimestamps">Show timestamps</label>
            </div>
          </div>
          
          <div class="transcription-container">
            @if(isset($data['speakers_transcriptions']))
              @foreach($data['speakers_transcriptions'] as $transcript)
                <div class="transcript-line">
                  <span class="timestamp">[{{ $transcript['start_time'] ?? '00:00' }}]</span>
                  <span class="speaker {{ $transcript['speaker'] ?? 'agent' }}">{{ $transcript['speaker'] ?? 'agent' }}:</span>
                  <span class="text">{{ $transcript['transcript'] ?? 'No transcript available' }}</span>
                  <span class="sentiment-badge badge 
                    @if(($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success
                    @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger
                    @else bg-warning @endif">
                    {{ $transcript['sentiment'] ?? 'Neutral' }}
                  </span>
                </div>
              @endforeach
            @else
              <div class="text-center text-muted py-4">
                <i class="bi bi-info-circle me-2"></i>No transcription data available
              </div>
            @endif
          </div>
          
          <div class="translated-container">
            <div class="translated-header">
              <i class="bi bi-translate"></i> Translated Transcription (English)
            </div>
            <div class="translated-content">
              @if(isset($data['speakers_transcriptions']))
                @foreach(array_slice($data['speakers_transcriptions'], 0, 5) as $transcript)
                  <div>[{{ $transcript['start_time'] ?? '00:00' }}] {{ $transcript['speaker'] ?? 'agent' }}: {{ $transcript['transcript'] ?? 'No transcript available' }}</div>
                @endforeach
              @else
                <div>No translation available</div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .transcription-container {
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
  }
  .transcript-line {
    margin-bottom: 8px;
    padding: 5px;
    border-radius: 4px;
    background-color: #f8f9fa;
  }
  .transcript-line .speaker {
    font-weight: bold;
    margin-right: 5px;
  }
  .transcript-line .speaker.agent {
    color: #0d6efd;
  }
  .transcript-line .speaker.customer {
    color: #fd7e14;
  }
  .timestamp {
    color: #6c757d;
    margin-right: 5px;
    font-size: 0.9em;
  }
  .sentiment-badge {
    float: right;
    font-size: 0.7em;
    padding: 3px 6px;
  }
</style>

<script>
  // Toggle timestamps visibility
  document.getElementById('showTimestamps').addEventListener('change', function() {
    const timestamps = document.querySelectorAll('.timestamp');
    timestamps.forEach(ts => {
      ts.style.display = this.checked ? 'inline' : 'none';
    });
  });
</script>
@endsection