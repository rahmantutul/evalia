@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">

    <!-- Dashboard Header -->
    <div class="container">
        <div class="row align-items-center bg-secondary p-3 rounded mb-3">
            <div class="col-md-8">
                <h4 class="text-dark">Department Audio list</h4>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="badge bg-light text-dark p-2 rounded-pill shadow-sm">
                    <i class="fas fa-building"></i>
                    <span class="fw-500">Department ID: <span class="text-primary">{{ $company_id }}</span></span>
                </div>
            </div>
        </div>

    <!-- Advanced Filters -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center">
                <i class="fas fa-search-plus text-primary me-2"></i> 
                Search & Filter Analyses
            </h6>
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Source</label>
                    <select name="source" class="form-select shadow-none border-light bg-light">
                        <option value="all">All Sources</option>
                        <option value="api" {{ request('source') == 'api' ? 'selected' : '' }}>API</option>
                        <option value="avaya" {{ request('source') == 'avaya' ? 'selected' : '' }}>Avaya</option>
                        <option value="genesys" {{ request('source') == 'genesys' ? 'selected' : '' }}>Genesys</option>
                        <option value="fb" {{ request('source') == 'fb' ? 'selected' : '' }}>FB</option>
                        <option value="linkedin" {{ request('source') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="inta" {{ request('source') == 'inta' ? 'selected' : '' }}>Instagram</option>
                        <option value="tiktok" {{ request('source') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="snap" {{ request('source') == 'snap' ? 'selected' : '' }}>Snapchat</option>
                        <option value="x" {{ request('source') == 'x' ? 'selected' : '' }}>X (Twitter)</option>
                        <option value="whatsapp" {{ request('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="email" {{ request('source') == 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Channel</label>
                    <select name="channel" class="form-select shadow-none border-light bg-light">
                        <option value="all">All Channels</option>
                        <option value="Call" {{ request('channel') == 'Call' ? 'selected' : '' }}>Call</option>
                        <option value="Messaging" {{ request('channel') == 'Messaging' ? 'selected' : '' }}>Messaging</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Agent</label>
                    <select name="agent" class="form-select select2 shadow-none border-light">
                        <option value="all">All Agents</option>
                        @foreach($companyAgents as $ag)
                            <option value="{{ $ag['full_name'] }}" {{ request('agent') == $ag['full_name'] ? 'selected' : '' }}>
                                {{ $ag['full_name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Sentiment</label>
                    <select name="sentiment" class="form-select shadow-none border-light bg-light">
                        <option value="all">Any Sentiment</option>
                        <option value="Positive" {{ request('sentiment') == 'Positive' ? 'selected' : '' }}>Positive</option>
                        <option value="Neutral" {{ request('sentiment') == 'Neutral' ? 'selected' : '' }}>Neutral</option>
                        <option value="Negative" {{ request('sentiment') == 'Negative' ? 'selected' : '' }}>Negative</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted mb-1">Risk</label>
                    <select name="risk" class="form-select shadow-none border-light bg-light">
                        <option value="all">All</option>
                        <option value="High" {{ request('risk') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="No" {{ request('risk') == 'No' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-2 text-end d-flex align-items-end gap-2">
                    <a href="{{ url()->current() }}" class="btn btn-light w-50 border">Reset</a>
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

        <div class="row g-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-list-stars text-primary me-2 fs-5"></i>
                        <h5 class="mb-0 fw-600">
                            @php
                                $msgSources = ['fb', 'linkedin', 'inta', 'tiktok', 'snap', 'x', 'whatsapp', 'email'];
                                $isMsg = in_array(request('source'), $msgSources) || request('channel') == 'Messaging';
                                $isCall = in_array(request('source'), ['api', 'avaya', 'genesys']) || request('channel') == 'Call';
                            @endphp
                            
                            @if($isMsg)
                                Messaging Analysis List
                            @elseif($isCall)
                                Call Analysis List
                            @else
                                All Communication Analyses
                            @endif
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-2 px-3 py-2" 
                                data-bs-toggle="modal" data-bs-target="#audioUploadModal{{ $company_id }}">
                            <i class="fas fa-plus-circle"></i>
                            <span class="d-none d-sm-inline">New Task</span>
                        </button>
                    </div>
                </div>

                <!-- ... modal content omitted for brevity but preserved in real file ... -->

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Agent</th>
                                    <th>Supervisor</th>
                                    <th>Channel</th>
                                    <th>Outcome</th>
                                    <th style="width: 150px;">Analysis Score</th>
                                    <th>Coaching</th>
                                    <th>Sentiment</th>
                                    <th>Type</th>
                                    <th>Risk</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="taskTableBody">
                                @forelse ($taskList as $task)
                                    <tr id="task-{{ $task['id'] }}">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                {{ \Carbon\Carbon::parse($task['created_at'])->format('M d, Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($task['created_at'])->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-soft-primary text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.7rem;">
                                                    {{ strtoupper(substr($task['agent_name'] ?? 'A', 0, 1)) }}
                                                </div>
                                                <span class="fw-600 text-dark small">{{ $task['agent_name'] ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="small">{{ $task['supervisor_name'] ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-soft-{{ $task['channel'] == 'Call' ? 'info' : 'warning' }} text-{{ $task['channel'] == 'Call' ? 'info' : 'warning' }} rounded-pill px-2 py-1" style="font-size: 0.65rem;">
                                                <i class="fas fa-{{ $task['channel'] == 'Call' ? 'phone' : 'comment-alt' }} me-1"></i>
                                                {{ $task['channel'] }} / {{ strtoupper($task['source'] ?? 'API') }}
                                            </span>
                                        </td>
                                        <td class="small text-muted">{{ $task['outcome'] ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $task['score'] >= 90 ? 'success' : ($task['score'] >= 75 ? 'primary' : 'danger') }}" 
                                                         role="progressbar" style="width: {{ $task['score'] }}%"></div>
                                                </div>
                                                <span class="fw-bold text-{{ $task['score'] >= 90 ? 'success' : ($task['score'] >= 75 ? 'primary' : 'danger') }}" style="font-size: 0.75rem;">
                                                    {{ $task['score'] }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(($task['coaching_required'] ?? 'No') == 'Yes')
                                                <span class="badge bg-soft-danger text-danger rounded-pill" style="font-size: 0.6rem;">Required</span>
                                            @else
                                                <span class="text-muted small">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $sentiment = $task['sentiment'] ?? 'Neutral'; @endphp
                                            <span class="text-{{ $sentiment == 'Positive' ? 'success' : ($sentiment == 'Negative' ? 'danger' : 'muted') }} small">
                                                <i class="fas fa-{{ $sentiment == 'Positive' ? 'smile' : ($sentiment == 'Negative' ? 'frown' : 'meh') }} me-1"></i>
                                                {{ $sentiment }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $task['call_type'] ?? 'Inbound' }} / {{ $task['lang'] ?? 'Ar' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(($task['risk_flag'] ?? 'No') == 'High')
                                                <i class="fas fa-exclamation-triangle text-danger" data-bs-toggle="tooltip" title="High Risk detected"></i>
                                            @else
                                                <i class="fas fa-check-circle text-success" style="opacity: 0.3;"></i>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('user.task.details', $task['id']) }}"><i class="fas fa-eye me-2 text-primary"></i>View Analysis</a></li>
                                                    <li><a class="dropdown-item text-danger" href="{{ route('user.task.delete', $task['id']) }}" onclick="return confirm('Delete this analysis?')"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5">
                                            <img src="https://illustrations.popsy.co/amber/empty-state.svg" style="height: 150px;" class="mb-3">
                                            <h6 class="text-muted">No analyses found matching your criteria.</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $taskList->firstItem() ?? 0 }} to {{ $taskList->lastItem() ?? 0 }} of {{ $taskList->total() }} entries
                        </div>
                        <div>
                            {{ $taskList->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
        }

        // Check if there are any running tasks
        function hasRunningTasks() {
            const runningBadges = document.querySelectorAll('.badge.bg-info');
            return runningBadges.length > 0;
        }

        // Function to refresh the task list
        function refreshTaskList() {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Create a temporary DOM element to parse the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract the table body from the response
                const newTableBody = doc.getElementById('taskTableBody');
                if (newTableBody) {
                    document.getElementById('taskTableBody').innerHTML = newTableBody.innerHTML;
                    
                    // Reinitialize tooltips
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                    // Continue auto-refresh if there are still running tasks
                    if (hasRunningTasks()) {
                        setTimeout(refreshTaskList, 30000); // 30 seconds
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing task list:', error);
                // Retry after 30 seconds even if there's an error
                if (hasRunningTasks()) {
                    setTimeout(refreshTaskList, 30000);
                }
            });
        }

        // Start auto-refresh only if there are running tasks
        if (hasRunningTasks()) {
            console.log('Auto-refresh started: Running tasks detected');
            setTimeout(refreshTaskList, 30000); // First refresh after 30 seconds
        }
    });
</script>
@endpush