@extends('user.layouts.app')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .qad-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .qad-logo {
            width: 40px;
            height: 40px;
            background-color: var(--qad-primary);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .qad-card {
            background: white;
            border-radius: 12px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .qad-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .qad-card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: var(--qad-dark);
        }

        .qad-metric {
            padding: 1.25rem;
        }

        .qad-metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--qad-dark);
            line-height: 1;
        }

        .qad-metric-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        .qad-metric-change {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }

        .qad-change-up {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--qad-success);
        }

        .qad-change-down {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--qad-danger);
        }

        .qad-change-neutral {
            background-color: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .qad-agent {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .qad-agent:last-child {
            border-bottom: none;
        }

        .qad-agent-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
        }

        .qad-agent-name {
            font-weight: 600;
            font-size: 0.9375rem;
        }

        .qad-agent-score {
            font-weight: 700;
            margin-left: auto;
        }

        .qad-score-high {
            color: var(--qad-success);
        }

        .qad-score-low {
            color: var(--qad-danger);
        }

        .qad-score-medium {
            color: var(--qad-warning);
        }

        .qad-progress-thin {
            height: 4px;
            border-radius: 2px;
        }

        .qad-topic-tag {
            display: inline-block;
            background-color: #e2e8f0;
            color: #475569;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 500;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .qad-export-btn {
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.8125rem;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
        }

        .qad-export-btn:hover {
            background-color: #f8fafc;
            color: var(--qad-primary);
            border-color: #cbd5e1;
        }

        .chart-container {
            position: relative;
            height: 250px;
            padding: 1rem;
        }

        @media (max-width: 767.98px) {
            .chart-container {
                height: 200px;
            }
        }
    </style>
@endpush
@section('content')
<div class="container-fluid py-3" style="background-color: #f8f9fa; min-height: 100vh;">
        <div class="container-fluid py-4">
         <div class="qad-header sticky-top py-3 mb-2">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="qad-logo me-3">QA</div>
                            <div>
                                <h1 class="h5 mb-0 fw-bold">Quality Assurance Dashboard</h1>
                                <p class="mb-0 text-muted small">Customer Support Center</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <div>
                                <a class="btn btn-outline-primary fw-600 shadow-sm text-back" href="{{ route('user.company.edit',$company['company_id']) }}">
                                    <i class="bi bi-gear me-1"></i> Settings
                                </a>
                               <button type="button" class="btn btn-outline-primary fw-600 shadow-sm text-back" data-bs-toggle="modal" data-bs-target="#audioUploadModal">
                                        <i class="fas fa-plus me-2"></i> Upload & Analyze Audio
                                </button>
                                 <button class="btn btn-outline-primary fw-600 shadow-sm text-back">
                                    <i class="bi bi-download me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>~~
        </div>
        <!-- KPI Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="qad-card h-100">
                    <div class="qad-metric">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="qad-metric-label">Calls Evaluated</div>
                            <span class="qad-metric-change qad-change-up">
                                <i class="bi bi-arrow-up"></i> 12%
                            </span>
                        </div>
                        <div class="qad-metric-value">1,248</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="qad-card h-100">
                    <div class="qad-metric">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="qad-metric-label">Quality Score</div>
                            <span class="qad-metric-change qad-change-neutral">
                                <i class="bi bi-dash"></i> 0.2%
                            </span>
                        </div>
                        <div class="qad-metric-value">87.4</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="qad-card h-100">
                    <div class="qad-metric">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="qad-metric-label">CSAT</div>
                            <span class="qad-metric-change qad-change-up">
                                <i class="bi bi-arrow-up"></i> 3.1%
                            </span>
                        </div>
                        <div class="qad-metric-value">92%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="qad-card h-100">
                    <div class="qad-metric">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="qad-metric-label">Active Agents</div>
                            <span class="qad-metric-change qad-change-down">
                                <i class="bi bi-arrow-down"></i> 2
                            </span>
                        </div>
                        <div class="qad-metric-value">42</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="qad-card h-100">
                    <div class="qad-card-header d-flex justify-content-between align-items-center">
                        <span>Performance Trend</span>
                        <button class="qad-export-btn">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                    </div>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="qad-card h-100">
                    <div class="qad-card-header d-flex justify-content-between align-items-center">
                        <span>Call Volume</span>
                        <button class="qad-export-btn">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                    </div>
                    <div class="chart-container">
                        <canvas id="callVolumeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="qad-card h-100">
                    <div class="qad-card-header d-flex justify-content-between align-items-center">
                        <span>Top Performers</span>
                        <button class="qad-export-btn">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                    </div>
                    <div class="p-2">
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">JD</div>
                            <div class="qad-agent-name">John Doe</div>
                            <div class="qad-agent-score qad-score-high">96.8</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">AS</div>
                            <div class="qad-agent-name">Alice Smith</div>
                            <div class="qad-agent-score qad-score-high">95.2</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">RJ</div>
                            <div class="qad-agent-name">Robert Johnson</div>
                            <div class="qad-agent-score qad-score-high">94.5</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">MB</div>
                            <div class="qad-agent-name">Maria Brown</div>
                            <div class="qad-agent-score qad-score-high">93.7</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">DW</div>
                            <div class="qad-agent-name">David Wilson</div>
                            <div class="qad-agent-score qad-score-high">92.9</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="qad-card h-100">
                    <div class="qad-card-header d-flex justify-content-between align-items-center">
                        <span>Needs Improvement</span>
                        <button class="qad-export-btn">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                    </div>
                    <div class="p-2">
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">TP</div>
                            <div class="qad-agent-name">Thomas Parker</div>
                            <div class="qad-agent-score qad-score-low">68.2</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">SG</div>
                            <div class="qad-agent-name">Sarah Green</div>
                            <div class="qad-agent-score qad-score-medium">72.5</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">KL</div>
                            <div class="qad-agent-name">Kevin Lee</div>
                            <div class="qad-agent-score qad-score-medium">74.1</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">AM</div>
                            <div class="qad-agent-name">Amy Martinez</div>
                            <div class="qad-agent-score qad-score-medium">76.8</div>
                        </div>
                        <div class="qad-agent">
                            <div class="qad-agent-avatar">JR</div>
                            <div class="qad-agent-name">James Robinson</div>
                            <div class="qad-agent-score qad-score-medium">78.3</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="qad-card h-100">
                    <div class="qad-card-header">Call Sentiment</div>
                    <div class="chart-container">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="qad-card h-100">
                    <div class="qad-card-header">Call Metrics</div>
                    <div class="p-3">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">Escalation Rate</span>
                                <span class="fw-bold">8.2%</span>
                            </div>
                            <div class="progress qad-progress-thin">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 8.2%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">First Call Resolution</span>
                                <span class="fw-bold">78.5%</span>
                            </div>
                            <div class="progress qad-progress-thin">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 78.5%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">Avg Resolution Time</span>
                                <span class="fw-bold">4.2 min</span>
                            </div>
                            <div class="progress qad-progress-thin">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="qad-card h-100">
                    <div class="qad-card-header">Common Topics</div>
                    <div class="p-3">
                        <div class="mb-3">
                            <span class="qad-topic-tag">Billing Questions (32%)</span>
                            <span class="qad-topic-tag">Technical Support (24%)</span>
                            <span class="qad-topic-tag">Account Changes (18%)</span>
                            <span class="qad-topic-tag">Product Info (14%)</span>
                            <span class="qad-topic-tag">Returns (8%)</span>
                            <span class="qad-topic-tag">Shipping (4%)</span>
                        </div>
                        <div class="small text-muted">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Compliance Rate</span>
                                <span class="fw-bold">89.3%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Script Adherence</span>
                                <span class="fw-bold">92.7%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row align-items-center bg-secondary p-3 rounded mb-3">
            <div class="col-md-8">
                <h3 class="fw-600 text-dark mb-2">Company Audio Analysis Dashboard</h3>
                <p class="text-primary mb-0">Analyze and evaluate customer service call</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="badge bg-light text-dark p-3 rounded-pill shadow-sm">
                    <i class="bi bi-building me-2"></i>
                    <span class="fw-500">Company ID: <span class="text-primary">{{ $company['company_id'] }}</span></span>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="row g-4">
            <!-- Recent Analyses Section -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom py-2">
                    <h5 class="mb-0 fw-600 d-flex align-items-center">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Recent Analyses
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 ps-4">Analysis ID</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Duration</th>
                                    <th class="py-2">Created</th>
                                    <th class="py-2 pe-4 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taskList as $task)
                                    <tr>
                                        <td class="ps-4 fw-500">#{{ $task['id'] }}</td>
                                        <td>
                                            <span class="badge bg-opacity-10 
                                                @if($task['status'] === 'completed') bg-success text-success 
                                                @elseif($task['status'] === 'processing') bg-warning text-warning 
                                                @else bg-danger text-danger @endif
                                                rounded-pill py-1 px-3">
                                                {{ ucfirst($task['status']) }}
                                            </span>
                                        </td>
                                        <td>@if(isset($task['duration'])) {{ $task['duration'] }} min @else -- @endif</td>
                                        <td>{{ \Carbon\Carbon::parse($task['created_at'])->format('M d, Y h:i A') }}</td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.task.details',$task['id']) }}" class="btn btn-sm btn-primary rounded-start-pill">
                                                    <i class="bi bi-eye me-1"></i> View
                                                </a>
                                                <a href="{{ route('user.task.delete',$task['id']) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this?')">
                                                    <i class="bi bi-eye"></i> Delete
                                                </a>
                                                <button class="btn btn-sm btn-secondary rounded-end-pill">
                                                    <i class="bi bi-arrow-repeat"></i>Re-run
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $taskList->firstItem() }} to {{ $taskList->lastItem() }} of {{ $taskList->total() }} entries
                        </div>
                        <div>
                            {{ $taskList->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>

<!-- Trigger Button -->
<button type="button" class="btn btn-primary rounded-pill fw-600 shadow-sm" data-bs-toggle="modal" data-bs-target="#audioUploadModal">
    <i class="fas fa-upload me-2"></i> Upload Audio
</button>

<!-- Modal -->
<div class="modal fade" id="audioUploadModal" tabindex="-1" aria-labelledby="audioUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Modal Header -->
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-600 text-black" id="audioUploadModalLabel" >
          <i class="fas fa-wave-square text-black me-2"></i> Audio Upload Form
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{ route('user.task.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row g-4 mb-4">

              {{-- Customer Audio --}}
              <div class="col-md-4">
                  <div class="card border-0 shadow-sm h-100">
                      <div class="card-header bg-white border-0 py-2">
                          <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                              <span class="bg-dark bg-opacity-10 text-primary p-2 me-2 rounded">
                                  <i class="fas fa-microphone"></i>
                              </span>
                              Customer Audio
                          </h5>
                      </div>
                      <div class="card-body d-flex flex-column">
                          <p class="text-muted small mb-4">Upload customer audio file in WAV or MP3 format</p>
                          <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                              <i class="bi bi-cloud-upload text-primary fs-1 mb-2"></i>
                              <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                              <span class="text-muted small mb-3">or click to browse</span>
                              <span class="badge bg-light text-dark px-3 py-2">Max 50MB</span>
                              <input type="file" name="customer_audio" class="d-none" accept="audio/*" required>
                          </label>
                      </div>
                  </div>
              </div>

              {{-- Agent Audio --}}
              <div class="col-md-4">
                  <div class="card border-0 shadow-sm h-100">
                      <div class="card-header bg-white border-0 py-2">
                          <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                              <span class="bg-danger bg-opacity-10 text-danger p-2 me-2 rounded">
                                  <i class="fas fa-headset fs-5"></i>
                              </span>
                              Agent Audio
                          </h5>
                      </div>
                      <div class="card-body d-flex flex-column">
                          <p class="text-muted small mb-4">Upload agent audio file in WAV or MP3 format</p>
                          <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                              <i class="bi bi-cloud-upload text-danger fs-1 mb-2"></i>
                              <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                              <span class="text-muted small mb-3">or click to browse</span>
                              <span class="badge bg-light text-dark px-3 py-2">Max 50MB</span>
                              <input type="file" name="agent_audio" class="d-none" accept="audio/*" required>
                          </label>
                      </div>
                  </div>
              </div>

              {{-- Combined Audio --}}
              <div class="col-md-4">
                  <div class="card border-0 shadow-sm h-100">
                      <div class="card-header bg-white border-0 py-2">
                          <h5 class="card-title mb-0 fw-600 d-flex align-items-center">
                              <span class="bg-success bg-opacity-10 text-success p-2 me-2 rounded">
                                  <i class="fas fa-microphone fs-5"></i>
                              </span>
                              Combined Audio
                          </h5>
                      </div>
                      <div class="card-body d-flex flex-column">
                          <p class="text-muted small mb-4">Upload pre-mixed audio file (optional)</p>
                          <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                              <i class="bi bi-cloud-upload text-success fs-1 mb-2"></i>
                              <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                              <span class="text-muted small mb-3">or click to browse</span>
                              <span class="badge bg-light text-dark px-3 py-2">Max 100MB</span>
                              <input type="file" name="combined_audio" class="d-none" accept="audio/*">
                          </label>
                      </div>
                  </div>
              </div>

          </div>

          <!-- Hidden company_id -->
          <input type="hidden" name="company_id" value="{{ $company['company_id'] }}">

          <!-- Action Buttons -->
          <div class="d-flex justify-content-center mb-2">
              <button type="submit" class="btn btn-primary px-5 py-2 me-3 rounded-pill fw-600 shadow-sm">
                  <i class="fas fa-chart-line me-2"></i> Analyze Audio
              </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>









@endsection

@push('scripts')
        <script>
        // Performance Trend Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Quality Score',
                    data: [82, 84, 83, 86, 85, 87, 89],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 80,
                        max: 100,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Call Volume Chart
        const callVolumeCtx = document.getElementById('callVolumeChart').getContext('2d');
        const callVolumeChart = new Chart(callVolumeCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Calls',
                    data: [320, 290, 350, 410, 380, 220, 180],
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Sentiment Chart
        const sentimentCtx = document.getElementById('sentimentChart').getContext('2d');
        const sentimentChart = new Chart(sentimentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [62, 28, 10],
                    backgroundColor: [
                        '#10b981',
                        '#94a3b8',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    }
                }
            }
        });
    </script>
@endpush