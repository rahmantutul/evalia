@extends('user.layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/task_details.css') }}">

@endpush

@section('content')
<div class="container-fluid py-4">
  <!-- Top Call Duration Bar -->
  <div class="top-bar animate-fade">
    {{--  <div class="duration">
      <i class="bi bi-clock me-2"></i>Call Duration: {{ $data['call_duration']['call_duration'] ?? 'N/A' }}
    </div>  --}}
    <div class="duration">
      <i class="bi bi-mic me-2"></i>Talking Duration: 
      {{ $data['pause_delay_information']['talking_duration']['agent'] ?? 'N/A' }} (Agent) / 
      {{ $data['pause_delay_information']['talking_duration']['customer'] ?? 'N/A' }} (Customer)
    </div>
  </div>

  <div class="row gy-4">
    <!-- Sentiment Analysis Card -->
    <div class="col-lg-4 animate-fade delay-1">
      <div class="compact-sentiment-card h-100">
        <div class="card-header d-flex justify-content-between align-items-center mb-3">
          <h6 class="card-title mb-0">Sentiment Analysis</h6>
          <div class="info-icon" data-bs-toggle="tooltip" title="Emotional tone measurement">i</div>
        </div>

        @php
          // Initialize sentiment counters
          $agentSentiments = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
          $customerSentiments = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
          
          // Calculate agent sentiments
          if(isset($data['agent_speakers_transcriptions'])) {
            foreach($data['agent_speakers_transcriptions'] as $transcript) {
              $sentiment = $transcript['sentiment'] ?? 'Neutral';
              $agentSentiments[$sentiment]++;
            }
          }
          
          // Calculate customer sentiments
          if(isset($data['customer_speakers_transcriptions'])) {
            foreach($data['customer_speakers_transcriptions'] as $transcript) {
              $sentiment = $transcript['sentiment'] ?? 'Neutral';
              $customerSentiments[$sentiment]++;
            }
          }
          
          // Calculate totals
          $totalAgent = array_sum($agentSentiments);
          $totalCustomer = array_sum($customerSentiments);
          $total = $totalAgent + $totalCustomer;
          
          // Calculate percentages
          $positivePercent = $total > 0 ? round(($agentSentiments['Positive'] + $customerSentiments['Positive']) / $total * 100) : 0;
          $neutralPercent = $total > 0 ? round(($agentSentiments['Neutral'] + $customerSentiments['Neutral']) / $total * 100) : 0;
          $negativePercent = $total > 0 ? round(($agentSentiments['Negative'] + $customerSentiments['Negative']) / $total * 100) : 0;
        @endphp

        <div class="sentiment-line">
          <!-- Positive -->
          <div class="sentiment-box positive">
            <div class="sentiment-icon"><i class="bi bi-emoji-smile"></i></div>
            <div class="sentiment-value">{{ $positivePercent }}%</div>
            <div class="sentiment-label">Positive</div>
          </div>

          <div class="divider"></div>

          <!-- Neutral -->
          <div class="sentiment-box neutral">
            <div class="sentiment-icon"><i class="bi bi-emoji-neutral"></i></div>
            <div class="sentiment-value">{{ $neutralPercent }}%</div>
            <div class="sentiment-label">Neutral</div>
          </div>

          <div class="divider"></div>

          <!-- Negative -->
          <div class="sentiment-box negative">
            <div class="sentiment-icon"><i class="bi bi-emoji-frown"></i></div>
            <div class="sentiment-value">{{ $negativePercent }}%</div>
            <div class="sentiment-label">Negative</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 animate-fade delay-2">
      <div class="compact-speech-card h-100">
        <div class="card-header d-flex justify-content-between align-items-center mb-3">
          <h6 class="card-title mb-0">Speech Rate <small class="text-muted">(Optimal: 100-200 wpm)</small></h6>
          <div class="info-icon" data-bs-toggle="tooltip" title="Words per minute analysis">i</div>
        </div>

        <div class="speech-rate-line">
          <div class="rate-box agent">
            <div class="rate-icon"><i class="bi bi-headset"></i></div>
            <div class="rate-value">{{ round($data['pace']['agent_pace'] ?? 0) }} <span style="font-size: small">(WPM)</span></div>
            <div class="rate-label">Agent</div>
            <div class="rate-comparison">
              {!! $data['pace']['agent_pace'] > $data['pace']['customer_pace'] ? 
                  '<i class="bi bi-arrow-up text-warning"></i>' : 
                  ($data['pace']['agent_pace'] < $data['pace']['customer_pace'] ? 
                  '<i class="bi bi-arrow-down text-success"></i>' : 
                  '<i class="bi bi-dash text-muted"></i>') !!}
            </div>
          </div>

          <div class="divider"></div>

          <div class="rate-box customer">
            <div class="rate-icon"><i class="bi bi-person"></i></div>
            <div class="rate-value">{{ round($data['pace']['customer_pace'] ?? 0) }} <span style="font-size: small">(WPM)</span></div>
            <div class="rate-label">Customer</div>
            <div class="rate-comparison">
              {!! $data['pace']['customer_pace'] > $data['pace']['agent_pace'] ? 
                  '<i class="bi bi-arrow-up text-warning"></i>' : 
                  ($data['pace']['customer_pace'] < $data['pace']['agent_pace'] ? 
                  '<i class="bi bi-arrow-down text-primary"></i>' : 
                  '<i class="bi bi-dash text-muted"></i>') !!}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Voice Loudness Card -->
    <div class="col-lg-4 animate-fade delay-3">
      <div class="compact-volume-line">
         <div class="card-header d-flex justify-content-between align-items-center mb-3">
          <h6 class="card-title mb-0">Voice Loudness</h6>
          <div class="info-icon" data-bs-toggle="tooltip" title="Volume distribution during call">i</div>
        </div>
        
        <div class="volume-metrics">
          <!-- Low Volume -->
          <div class="metric-box low">
            <div class="metric-icon">
              <i class="bi bi-volume-down"></i>
            </div>
            <div class="metric-value">
              {{ $data['speaker_loudness']['agent']['lower_loudness_percentage'] ?? 0 }}%
            </div>
            <div class="metric-label">Low</div>
          </div>
          
          <!-- Optimal Volume -->
          <div class="metric-box optimal">
            <div class="metric-icon">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="metric-value">
              {{ $data['speaker_loudness']['agent']['optimal_loudness_percentage'] ?? 0 }}%
            </div>
            <div class="metric-label">Optimal</div>
          </div>
          
          <!-- High Volume -->
          <div class="metric-box high">
            <div class="metric-icon">
              <i class="bi bi-volume-up"></i>
            </div>
            <div class="metric-value">
              {{ $data['speaker_loudness']['agent']['upper_loudness_percentage'] ?? 0 }}%
            </div>
            <div class="metric-label">High</div>
          </div>
        </div>
      </div>
    </div>


    <!-- Sentiment Timeline Card -->
    <div class="col-lg-6 animate-fade delay-2">
      <div class="professional-card h-100">
        <div class="card-header">
          <span>Sentiment Timeline</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Detailed timeline of sentiment changes">i</div>
        </div>
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs nav-tabs-card" id="sentimentTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agent" type="button" role="tab">Agent</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab">Customer</button>
          </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content p-0">
          <!-- All Tab -->
          <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
            <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
              <table class="sentiment-table">
                <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                  <tr>
                    <th>Speaker</th>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Intensity</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($data['speakers_transcriptions']))
                    @foreach($data['speakers_transcriptions'] as $transcript)
                      <tr>
                        <td>
                          <span class="badge 
                            @if(($transcript['speaker'] ?? '') == 'agent') bg-warning bg-opacity-10 text-warning 
                            @elseif(($transcript['speaker'] ?? '') == 'customer') bg-info bg-opacity-10 text-info
                            @else bg-secondary bg-opacity-10 text-secondary @endif p-1">
                            {{ ucfirst($transcript['speaker'] ?? 'Unknown') }}
                          </span>
                        </td>
                        <td>
                          <span class="badge 
                            @if(($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success bg-opacity-10 text-success
                            @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger bg-opacity-10 text-danger
                            @else bg-warning bg-opacity-10 text-warning @endif p-1">
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
                      <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle me-2"></i>No sentiment data available
                      </td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
          
          <!-- Agent Tab -->
          <div class="tab-pane fade" id="agent" role="tabpanel" aria-labelledby="agent-tab">
            <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
              <table class="sentiment-table">
                <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                  <tr>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Intensity</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($data['speakers_transcriptions']))
                    @php $hasAgentData = false; @endphp
                    @foreach($data['speakers_transcriptions'] as $transcript)
                      @if(($transcript['speaker'] ?? '') == 'agent')
                        @php $hasAgentData = true; @endphp
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
                      @endif
                    @endforeach
                    @if(!$hasAgentData)
                      <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                          <i class="bi bi-info-circle me-2"></i>No agent data available
                        </td>
                      </tr>
                    @endif
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
          
          <!-- Customer Tab -->
          <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
            <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
              <table class="sentiment-table">
                <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                  <tr>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Intensity</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($data['speakers_transcriptions']))
                    @php $hasCustomerData = false; @endphp
                    @foreach($data['speakers_transcriptions'] as $transcript)
                      @if(($transcript['speaker'] ?? '') == 'customer')
                        @php $hasCustomerData = true; @endphp
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
                      @endif
                    @endforeach
                    @if(!$hasCustomerData)
                      <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                          <i class="bi bi-info-circle me-2"></i>No customer data available
                        </td>
                      </tr>
                    @endif
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
      </div>
    </div>

    <div class="col-lg-6 animate-fade delay-3">
      <div class="time-analysis-card p-3">
        <div class="card-header">
          <span>Timeline Analysis</span>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Analysis states">i</div>
        </div>
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="timeAnalysisTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="delays-tab" data-bs-toggle="tab" data-bs-target="#delays" type="button" role="tab">
              <i class="bi bi-clock-history me-1"></i> Delays
              <span class="badge bg-warning bg-opacity-10 text-warning ms-1">
                {{ isset($data['pause_delay_information']['speaker_delay_duration']) ? 
                  count(array_merge(
                    $data['pause_delay_information']['speaker_delay_duration']['agent'] ?? [],
                    $data['pause_delay_information']['speaker_delay_duration']['customer'] ?? []
                  )) : 0 }}
              </span>
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link " id="pauses-tab" data-bs-toggle="tab" data-bs-target="#pauses" type="button" role="tab">
              <i class="bi bi-pause-circle me-1"></i> Pauses
              <span class="badge bg-primary bg-opacity-10 text-primary ms-1">
                {{ isset($data['pause_delay_information']['speaker_pause_duration']) ? 
                  count(array_merge(
                    $data['pause_delay_information']['speaker_pause_duration']['agent'] ?? [],
                    $data['pause_delay_information']['speaker_pause_duration']['customer'] ?? []
                  )) : 0 }}
              </span>
            </button>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="timeAnalysisTabsContent">
          <div class="tab-pane fade" id="pauses" role="tabpanel">
            <div class="table-container" style="max-height: 400px;">
              <table class="time-analysis-table">
                <thead>
                  <tr>
                    <th width="20%">Speaker</th>
                    <th width="15%">Duration</th>
                    <th width="25%">Time</th>
                    <th width="40%">Context</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($data['pause_delay_information']['speaker_pause_duration']))
                    @php
                      $agentPauses = $data['pause_delay_information']['speaker_pause_duration']['agent'] ?? [];
                      $customerPauses = $data['pause_delay_information']['speaker_pause_duration']['customer'] ?? [];
                      $allPauses = array_merge(
                        array_map(function($item) { return $item + ['speaker' => 'Agent']; }, $agentPauses),
                        array_map(function($item) { return $item + ['speaker' => 'Customer']; }, $customerPauses)
                      );
                    @endphp

                    @forelse($allPauses as $pause)
                      <tr>
                        <td>
                          <span class="badge {{ $pause['speaker'] == 'Agent' ? 'bg-warning bg-opacity-10 text-warning  p-2' : 'bg-success bg-opacity-10 text-success' }}">
                            <i class="bi {{ $pause['speaker'] == 'Agent' ? 'bi-headset' : 'bi-person' }} me-1"></i>
                            {{ $pause['speaker'] }}
                          </span>
                        </td>
                        <td>{{ $pause['pause_duration'] ?? 'N/A' }}</td>
                        <td>{{ $pause['pause_start'] ?? 'N/A' }} - {{ $pause['pause_end'] ?? 'N/A' }}</td>
                        <td class="text-truncate" title="{{ $pause['pause_class'] ?? 'No context' }}">{{ $pause['pause_class'] ?? 'No context' }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                          <i class="bi bi-check-circle me-2"></i> No significant pauses detected
                        </td>
                      </tr>
                    @endforelse
                  @else
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-triangle me-2"></i> Pause data not available
                      </td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>

          <!-- Delays Tab -->
          <div class="tab-pane fade  show active" id="delays" role="tabpanel">
            <div class="table-container" style="max-height: 400px;">
              <table class="time-analysis-table">
                <thead>
                  <tr>
                    <th width="20%">Speaker</th>
                    <th width="15%">Duration</th>
                    <th width="25%">Time</th>
                    <th width="40%">Context</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($data['pause_delay_information']['speaker_delay_duration']))
                    @php
                      $agentDelays = $data['pause_delay_information']['speaker_delay_duration']['agent'] ?? [];
                      $customerDelays = $data['pause_delay_information']['speaker_delay_duration']['customer'] ?? [];
                      $allDelays = array_merge(
                        array_map(function($item) { return $item + ['speaker' => 'Agent']; }, $agentDelays),
                        array_map(function($item) { return $item + ['speaker' => 'Customer']; }, $customerDelays)
                      );
                    @endphp

                    @forelse($allDelays as $delay)
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
                      <tr>
                        <td>
                          <span class="badge {{ $delay['speaker'] == 'Agent' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success' }}">
                            <i class="bi {{ $delay['speaker'] == 'Agent' ? 'bi-headset' : 'bi-person' }} me-1"></i>
                            {{ $delay['speaker'] }}
                          </span>
                        </td>
                        <td>{{ $delay['delay_duration'] ?? 'N/A' }}</td>
                        <td>{{ $delay['delay_start'] ?? 'N/A' }} - {{ $delay['delay_end'] ?? 'N/A' }}</td>
                        <td class="text-truncate" title="{{ $context }}">{{ $context }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                          <i class="bi bi-check-circle me-2"></i> No significant delays detected
                        </td>
                      </tr>
                    @endforelse
                  @else
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-triangle me-2"></i> Delay data not available
                      </td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column - Call Details -->
    <div class="col-lg-6">
      <div class="d-flex flex-column gap-4 h-100">
        <!-- Call Summary -->
        <div class="professional-card animate-fade delay-2">
          <div class="card-header">
            <span>Call Summary</span>
            <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Automatically generated call summary">i</div>
          </div>
          <div class="audio-player bg-secondary rounded ">
          <!-- Hidden audio element -->
          <audio id="audioPlayer" src="{{ $data['customer_agent_audio_s3_url'] ?? '' }}"></audio>
          
          <div class="audio-controls">
            <button class="audio-btn" title="Skip Backward" onclick="skip(-10)">
              <i class="fas fa-step-backward"></i>
            </button>
            <button class="audio-btn" id="playButton" title="Play" onclick="togglePlay()">
              <i class="fas fa-play" id="playIcon"></i>
            </button>
            <button class="audio-btn" title="Skip Forward" onclick="skip(10)">
              <i class="fas fa-step-forward"></i>
            </button>
          </div>
          
          <div class="audio-progress">
            <div class="audio-progress-filled" id="progressBar"></div>
          </div>
          
          <div class="audio-time">
            <span id="currentTime">0:00</span> / 
            <span id="duration">{{ $data['call_duration']['call_duration'] ?? '0:00' }}</span>
          </div>
          
          <div class="audio-controls">
            @if(isset($data['customer_agent_audio_s3_url']))
              <a href="{{ $data['customer_agent_audio_s3_url'] }}" class="audio-btn" title="Download" download>
                <i class="fas fa-download"></i>
              </a>
            @endif
          </div>
          </div>


          <div class="summary-card premium-summary">
            <div class="summary-content">
              <!-- Detailed Summary -->
              <div class="detailed-summary">
                <div class="summary-text arabic-text">
                  {{ $data['transcription_summaries']['detail'] ?? 'No detailed summary available' }}
                </div>
              </div>

              <!-- Key Points -->
              @if(isset($data['topics']['other']) || isset($data['call_outcome']))
              <div class="key-points mt-4">
                <div class="section-title">
                  <i class="bi bi-key-fill"></i>
                  <h6>Key Discussion Points</h6>
                </div>
                <div class="points-container">
                  @if(isset($data['topics']['main']))
                    <div class="main-topic">
                      <span class="topic-badge main-badge">
                        Main Topic: &nbsp; <i class="bi bi-bookmark-fill me-1"></i>
                        {{ $data['topics']['main'][0] ?? 'Main Topic' }}
                      </span>
                    </div>
                  @endif
                  
                 <div class="compact-key-points">
                    <div class="points-line">
                      Other Topics: &nbsp; 
                      @foreach($data['topics']['other'] ?? [] as $topic)
                        <span class="point-item">
                          <i class="bi bi-check-circle-fill text-success me-1"></i>
                          {{ $topic }}
                        </span>
                        @if(!$loop->last)
                          <span class="point-divider">•</span>
                        @endif
                      @endforeach
                    </div>
                    
                    @if(isset($data['call_outcome']))
                      <div class="outcome-line mt-2">
                       <strong> Call Outcome </strong>:  &nbsp;<i class="fas fa-ticket text-secondary me-1"></i>
                         {{ implode('، ', $data['call_outcome']) }}
                      </div>
                    @endif
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Right Column - Call Details -->
    <div class="col-lg-6">
      <div class="d-flex flex-column gap-4 h-100">
        
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
        </div>
      </div>
    </div>
    <div class="col-lg-7">
       <div class="agent-performance-dashboard">
          <!-- Main Header with Overall Scores -->
          <div class="dashboard-header">
            <div class="agent-profile">
              <div class="avatar">
                <i class="fas fa-headset"></i>
              </div>
              <div class="agent-info">
                <h3>Agent Performance</h3>
                <div class="overall-score">
                  <div class="score-circle" style="--percentage: {{ $data['agent_professionalism']['total_score']['percentage'] }}%">
                    <span>{{ $data['agent_professionalism']['total_score']['percentage'] }}%</span>
                  </div>
                  <div class="score-details">
                    <div class="score-item">
                      <span class="label">Professionalism</span>
                      <span class="value">{{ $data['agent_professionalism']['total_score']['percentage'] }}%</span>
                    </div>
                    <div class="score-item">
                      <span class="label">Assessment</span>
                      <span class="value">{{ $data['agent_assessment']['total_score']['percentage'] }}%</span>
                    </div>
                    <div class="score-item">
                      <span class="label">Cooperation</span>
                      <span class="value">{{ $data['agent_cooperation']['total_score']['percentage'] }}%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Speech Characteristics -->
            <div class="speech-analysis">
              <h4>Speech Analysis</h4>
              <div class="speech-metrics">
                <div class="metric">
                  <i class="fas fa-volume-up"></i>
                  <span>{{ ucfirst($data['agent_professionalism']['speech_characteristics']['volume']['loudness_class']) }} Volume</span>
                  <div class="progress-bar">
                    <div class="progress" style="width: {{ $data['agent_professionalism']['speech_characteristics']['volume']['optimal_loudness_percentage'] }}%"></div>
                  </div>
                </div>
                <div class="metric">
                  <i class="fas fa-tachometer-alt"></i>
                  <span>{{ round($data['agent_professionalism']['speech_characteristics']['speed']) }} WPM</span>
                  <div class="progress-bar">
                    <div class="progress" style="width: 80%"></div> <!-- Adjust based on your ideal speed range -->
                  </div>
                </div>
                <div class="metric">
                  <i class="fas fa-pause"></i>
                  <span>{{ $data['agent_professionalism']['speech_characteristics']['pauses'] }} Pauses</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab Navigation -->
         <div class="tabs">
            <button class="tab-btn active" data-tab="professionalism">
              <i class="fas fa-award"></i> Professionalism
            </button>
            <button class="tab-btn" data-tab="assessment">
              <i class="fas fa-clipboard-check"></i> Skills Assessment
            </button>
            <button class="tab-btn" data-tab="cooperation">
              <i class="fas fa-handshake"></i> Cooperation
            </button>
            <button class="tab-btn" data-tab="linguistic">
              <i class="fas fa-language"></i> Linguistic Analysis
            </button>
        </div>

          <!-- Tab Contents -->
          <div class="tab-content">
            <!-- Professionalism Tab -->
            <div class="tab-pane active" id="professionalism">
              <div class="metrics-grid">
                @foreach(['customer_satisfaction', 'professionalism', 'tone_consistency', 'polite_language_usage', 'configured_standards_compliance'] as $metric)
                <div class="metric-card">
                  <div class="metric-score">{{ $data['agent_professionalism'][$metric]['score'] }}/10</div>
                  <h4>{{ ucfirst(str_replace('_', ' ', $metric)) }}</h4>
                  <div class="metric-details">
                    <p><strong>Evidence:</strong> "{{ $data['agent_professionalism'][$metric]['evidence'] }}"</p>
                    <p><strong>Reasoning:</strong> {{ $data['agent_professionalism'][$metric]['reasoning'] }}</p>
                    <p><strong>Determination:</strong> {{ $data['agent_professionalism'][$metric]['determination'] }}</p>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <!-- Skills Assessment Tab -->
            <div class="tab-pane" id="assessment">
              <div class="metrics-grid">
                @foreach(['communication', 'problem_solving', 'technical_knowledge', 'efficiency'] as $metric)
                <div class="metric-card">
                  <div class="metric-score">{{ $data['agent_assessment'][$metric]['score'] }}/10</div>
                  <h4>{{ ucfirst(str_replace('_', ' ', $metric)) }}</h4>
                  <div class="metric-details">
                    <p><strong>Evidence:</strong> "{{ $data['agent_assessment'][$metric]['evidence'] }}"</p>
                    <p><strong>Reasoning:</strong> {{ $data['agent_assessment'][$metric]['reasoning'] }}</p>
                    <p><strong>Determination:</strong> {{ $data['agent_assessment'][$metric]['determination'] }}</p>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <!-- Cooperation Tab -->
            <div class="tab-pane" id="cooperation">
              <div class="metrics-grid">
                @foreach(['agent_proactive_assistance', 'agent_responsiveness', 'agent_empathy', 'effectiveness'] as $metric)
                <div class="metric-card">
                  <div class="metric-score">{{ $data['agent_cooperation'][$metric]['score'] }}/10</div>
                  <h4>{{ ucfirst(str_replace('agent_', '', str_replace('_', ' ', $metric))) }}</h4>
                  <div class="metric-details">
                    <p><strong>Evidence:</strong> "{{ $data['agent_cooperation'][$metric]['evidence'] ?? 'N/A' }}"</p>
                    <p><strong>Reasoning:</strong> {{ $data['agent_cooperation'][$metric]['reasoning'] }}</p>
                    <p><strong>Determination:</strong> {{ $data['agent_cooperation'][$metric]['determination'] }}</p>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <!-- Linguistic Analysis Tab -->
            <div class="tab-pane" id="linguistic">
              <div class="full-width-section">
                <h3>Tone Analysis</h3>
                <div class="tone-analysis">
                  <!-- You would loop through tone_analysis data here -->
                  <div class="tone-item">
                    <span class="tone-label">Friendly</span>
                    <div class="progress-bar">
                      <div class="progress" style="width: {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['friendly'] ?? 0 }}%"></div>
                    </div>
                  </div>
                  <!-- Add other tone metrics similarly -->
                </div>
                
                <h3>Language Usage</h3>
                <div class="language-stats">
                  <div class="stat">
                    <span>Formal Language:</span>
                    <span>{{ $data['agent_professionalism']['linguistic_analysis']['formal_language_percentage'] ?? 0 }}%</span>
                  </div>
                  <div class="stat">
                    <span>Polite Phrases:</span>
                    <span>{{ $data['agent_professionalism']['polite_language_usage']['score'] }}/10</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Performance Summary -->
          <div class="performance-summary">
            <div class="summary-header">
              <i class="fas fa-{{ $data['agent_professionalism']['total_score']['percentage'] > 80 ? 'trophy' : 'check-circle' }}"></i>
              <h3>{{ $data['agent_professionalism']['total_score']['percentage'] > 80 ? 'Excellent Performance' : 'Good Performance' }}</h3>
            </div>
            <div class="summary-content">
              <p>{{ $data['agent_professionalism']['customer_satisfaction']['reasoning'] }}</p>
              <div class="summary-stats">
                <div class="stat">
                  <span>Total Score:</span>
                  <span>{{ $data['agent_professionalism']['total_score']['score'] }}/{{ $data['agent_professionalism']['total_score']['max_score'] }}</span>
                </div>
                <div class="stat">
                  <span>Customer Satisfaction:</span>
                  <span>{{ $data['agent_professionalism']['customer_satisfaction']['score'] }}/10</span>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-lg-5">
      <div class="word-frequency-card">
        <div class="card-header">
          <h5>Most Frequent Words</h5>
          <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Top words used during conversation">i</div>
        </div>

        <div class="word-frequency-container">
          <!-- Agent Words -->
          <div class="speaker-words agent-words">
            <div class="speaker-header">
              <i class="bi bi-headset"></i>
              <span>Agent</span>
            </div>
            <div class="word-cloud">
              @foreach(array_slice($data['most_common_words']['agent'] ?? [], 0, 15) as $word)
                <span class="word-tag" style="font-size: 0.8rem">
                  {{ $word['word'] }}
                  <span class="frequency-badge">{{ $word['frequency'] }}</span>
                </span>
              @endforeach
            </div>
          </div>

          <!-- Customer Words -->
          <div class="speaker-words customer-words">
            <div class="speaker-header">
              <i class="bi bi-person"></i>
              <span>Customer</span>
            </div>
            <div class="word-cloud">
              @foreach(array_slice($data['most_common_words']['customer'] ?? [], 0, 15) as $word)
                <span class="word-tag" style="font-size: 0.8rem">
                  {{ $word['word'] }}
                  <span class="frequency-badge">{{ $word['frequency'] }}</span>
                </span>
              @endforeach
            </div>
          </div>
        </div>

        <div class="card-footer">
          <small class="text-muted">Showing top 15 words per speaker</small>
        </div>
      </div>
      <div class="word-frequency-card mt-4">
        <div class="card-header">
          <h5>Knowledge Base Reference</h5>
            <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="The number of times the customer wrong response have been made">i</div>
        </div>
        <div class="accordion" id="analysisAccordion">
          @foreach($data['analysis_alignment_result_notebook'] as $index => $result)
            <div class="accordion-item shadow-sm mb-2 rounded-3 border-0">
                <h2 class="accordion-header" id="heading{{ $index }}">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                            aria-expanded="false" aria-controls="collapse{{ $index }}">
                        <span class="fw-semibold text-dark">
                            <i class="fas fa-question-circle me-2 text-primary"></i>
                            {{ Str::limit($result['question'], 80) }}
                        </span>
                        <span class="badge bg-light text-dark border ms-2">
                            {{ ucfirst($result['confidence_level']) }}
                        </span>
                    </button>
                </h2>
                <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#analysisAccordion">
                    <div class="accordion-body bg-white">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted mb-1"><i class="fas fa-lightbulb text-warning me-1"></i> Answer</div>
                                <div class="fw-medium">{{ $result['answer'] }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1"><i class="fas fa-check-circle text-success me-1"></i> Evaluation</div>
                                <span class="badge bg-outline-success rounded-pill">
                                    {{ $result['evaluation'] }}
                                </span>
                            </div>

                            <div class="col-md-6">
                                <div class="small text-muted mb-1"><i class="fas fa-book text-info me-1"></i> Knowledge Base</div>
                                <div class="fw-light">
                                    {{ $result['KBtext'] ?? 'No relevant text in knowledge base' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1"><i class="fas fa-project-diagram text-secondary me-1"></i> Matching Sections</div>
                                @foreach($result['matching_transcript_sections'] as $section)
                                    <span class="badge bg-light text-dark border">{{ $section }}</span>
                                @endforeach
                            </div>

                            <div class="col-md-4">
                                <div class="small text-muted mb-1"><i class="fas fa-notebook text-primary me-1"></i> Notebook</div>
                                <span class="fw-medium">{{ $result['notebook_name'] }}</span>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted mb-1"><i class="fas fa-tags text-info me-1"></i> Topics</div>
                                @foreach($result['matching_topics'] as $topic)
                                    <span class="badge bg-light text-dark border">{{ $topic }}</span>
                                @endforeach
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted mb-1"><i class="fas fa-chart-line text-success me-1"></i> Confidence</div>
                                <span class="badge bg-{{ $result['confidence_level'] == 'high' ? 'success' : ($result['confidence_level'] == 'medium' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($result['confidence_level']) }}
                                </span>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const audioPlayer = document.getElementById('audioPlayer');
    const playButton = document.getElementById('playButton');
    const playIcon = document.getElementById('playIcon');
    const progressBar = document.getElementById('progressBar');
    const currentTimeEl = document.getElementById('currentTime');
    const durationEl = document.getElementById('duration');

    // Update time display format
    function formatTime(seconds) {
      const minutes = Math.floor(seconds / 60);
      const secs = Math.floor(seconds % 60);
      return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    // Update progress bar
    function updateProgress() {
      const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
      progressBar.style.width = `${percent}%`;
      currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
    }

    // Set audio duration when metadata is loaded
    audioPlayer.addEventListener('loadedmetadata', function() {
      durationEl.textContent = formatTime(audioPlayer.duration);
    });

    // Update progress while playing
    audioPlayer.addEventListener('timeupdate', updateProgress);

    // Toggle play/pause
    window.togglePlay = function() {
      if (audioPlayer.paused) {
        audioPlayer.play();
        playIcon.classList.remove('fa-play');
        playIcon.classList.add('fa-pause');
        playButton.title = 'Pause';
      } else {
        audioPlayer.pause();
        playIcon.classList.remove('fa-pause');
        playIcon.classList.add('fa-play');
        playButton.title = 'Play';
      }
    };

    // Skip forward/backward
    window.skip = function(seconds) {
      audioPlayer.currentTime += seconds;
      updateProgress();
    };

    // Click on progress bar to seek
    document.querySelector('.audio-progress').addEventListener('click', function(e) {
      const percent = e.offsetX / this.offsetWidth;
      audioPlayer.currentTime = percent * audioPlayer.duration;
      updateProgress();
    });
  });
</script>

<script>
  // Toggle timestamps visibility
  document.getElementById('showTimestamps').addEventListener('change', function() {
    const timestamps = document.querySelectorAll('.timestamp');
    timestamps.forEach(ts => {
      ts.style.display = this.checked ? 'inline' : 'none';
    });
  });
</script>
<script>
document.querySelectorAll('.tabs').forEach(tabContainer => {
  const buttons = tabContainer.querySelectorAll('.tab-btn');
  const panes = tabContainer.nextElementSibling.querySelectorAll('.tab-pane');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      // reset only inside this tab group
      buttons.forEach(b => b.classList.remove('active'));
      panes.forEach(p => p.classList.remove('active'));

      // activate selected
      btn.classList.add('active');
      tabContainer.nextElementSibling
        .querySelector('#' + btn.dataset.tab)
        .classList.add('active');
    });
  });
});
</script>
@endpush