@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">

                {{-- Header --}}
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary p-2 rounded-3 me-3 shadow-sm">
                            <i class="fas fa-brain text-white fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold text-dark">Knowledge Base</h4>
                            <p class="text-muted mb-0 small">Manage your AI context and company manuals</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.knowledgeBase.simulator') }}" class="btn btn-outline-info d-flex align-items-center px-4 py-2"
                                style="border-radius: 10px;">
                            <i class="fas fa-vial me-2"></i> RAG Simulator
                        </a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                        <a href="{{ route('user.knowledgeBase.create') }}"
                           class="btn btn-primary d-flex align-items-center px-4 py-2" style="border-radius: 10px;">
                            <i class="fas fa-plus me-2"></i> Create New KB
                        </a>
                        @endif
                    </div>
                </div>

                {{-- =================================================== --}}
                {{-- RAG Simulator Modal                                   --}}
                {{-- =================================================== --}}
                <div class="modal fade" id="ragSimulatorModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 95%; width: 95%;">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-4">
                                    {{-- Left: Input --}}
                                    <div class="col-md-4">
                                        <div class="bg-light rounded-4 p-4 h-100">
                                            <h6 class="fw-bold mb-3"><i class="fas fa-keyboard text-primary me-2"></i>Your Query</h6>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">1. Company</label>
                                                <select id="simCompany" class="form-select border-0 py-2 shadow-sm">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">2. Question / Transcription Text</label>
                                                <textarea id="simQuery" class="form-control border-0 shadow-sm" rows="6"
                                                    placeholder="e.g. كيف يتعامل الموظف مع العميل الغاضب?&#10;or: How should an agent handle a refund request?"></textarea>
                                            </div>
                                            <div class="d-grid">
                                                <button type="button" id="simBtn" onclick="runSimulation()"
                                                        class="btn btn-primary fw-bold py-2" style="border-radius: 10px;">
                                                    <span id="simBtnText"><i class="fas fa-search me-2"></i>Search KB</span>
                                                    <span id="simBtnLoader" class="d-none">
                                                        <span class="spinner-border spinner-border-sm me-2"></span>Searching...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right: Results --}}
                                    <div class="col-md-8">
                                        {{-- Keywords extracted --}}
                                        <div id="simKeywordsBox" class="d-none mb-3 p-3 bg-light rounded-4 border-start border-4 border-primary">
                                            <div class="small fw-bold text-dark mb-2">
                                                <i class="fas fa-tags text-primary me-1"></i>
                                                Keywords Extracted from your query:
                                            </div>
                                            <div id="simKeywords" class="d-flex flex-wrap gap-1"></div>
                                        </div>

                                        {{-- No keywords warning --}}
                                        <div id="simNoKeywords" class="d-none mb-3 alert alert-warning border-0 rounded-4">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>No meaningful keywords found in your query.</strong><br>
                                            <small>
                                                Your query only contained <strong>stop words</strong> (common words like
                                                "how", "are", "you", "ما", "هل", "كيف"). These are filtered out because they
                                                appear everywhere and don't help find relevant content.<br><br>
                                                <strong>Try instead:</strong> Use specific words from your topic, like
                                                <em>"refund damaged product"</em> or <em>"استرجاع منتج تالف"</em>.
                                            </small>
                                        </div>

                                        {{-- No match --}}
                                        <div id="simNoMatch" class="d-none mb-3 alert alert-secondary border-0 rounded-4">
                                            <i class="fas fa-search-minus me-2"></i>
                                            <strong>Keywords found, but no matching sections in the KB.</strong><br>
                                            <small>
                                                The system searched all uploaded documents but your keywords don't appear in any section.
                                                This means the KB doesn't cover this topic — GPT would need to answer from general knowledge.
                                            </small>
                                        </div>

                                        {{-- Matched context --}}
                                        <div id="simResult" class="d-none">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label small fw-bold text-success mb-0">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Filtered KB Context
                                                </label>
                                                <button class="btn btn-sm btn-light border" onclick="copySimResult()">
                                                    <i class="fas fa-copy me-1"></i> Copy
                                                </button>
                                            </div>
                                            <div class="bg-dark text-light p-4 rounded-3 small shadow-inner"
                                                 style="max-height: 550px; overflow-y: auto; font-family: 'Fira Code', 'Courier New', monospace; white-space: pre-wrap; direction: auto; font-size: 13px; line-height: 1.6;"
                                                 id="simResultContent"></div>
                                            <div class="mt-2 text-muted small">
                                                <i class="fas fa-bolt text-warning me-1"></i>
                                            </div>
                                        </div>

                                        {{-- Placeholder --}}
                                        <div id="simPlaceholder" class="text-center py-5 text-muted">
                                            <i class="fas fa-robot fa-3x mb-3 text-light d-block"></i>
                                            Enter a query on the left and click <strong>Search KB</strong> to see the result.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="card-body bg-light border-top border-bottom py-3 px-4">
                    <form action="{{ route('user.knowledgeBase.list') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Filter by Company</label>
                            <select name="company_id" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
                                <option value="all">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                <div class="alert alert-success border-0 m-3 rounded-3">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger border-0 m-3 rounded-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
                @endif

                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">ID</th>
                                    <th class="py-3">Knowledge Resource</th>
                                    <th class="py-3">Company</th>
                                    <th class="py-3" style="width: 250px;">Keywords</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Created</th>
                                    <th class="text-end pe-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($knowledgeBase as $entry)
                                <tr class="border-bottom">
                                    <td class="ps-4 fw-semibold text-muted">#{{ $entry->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 p-2 me-3 bg-soft-primary">
                                                <i class="fas fa-book fa-lg"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $entry->title }}</div>
                                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ Str::limit($entry->content, 60) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info px-3 py-2" style="border-radius: 8px;">
                                            <i class="icofont-bank-alt me-1"></i> {{ $entry->company->company_name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($entry->keywords)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(explode(',', $entry->keywords) as $kw)
                                                    <span class="badge bg-light text-dark border small" style="font-size: 10px;">{{ trim($kw) }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small italic">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($entry->is_active)
                                            <span class="badge bg-soft-success text-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary px-2 py-1">
                                                <i class="fas fa-pause-circle me-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="fw-bold text-dark">{{ $entry->created_at->format('d M Y') }}</small>
                                        <div class="text-muted" style="font-size: 11px;">{{ $entry->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2 pe-3">
                                            <a href="{{ route('user.knowledgeBase.details', $entry->id) }}"
                                               class="btn btn-sm btn-icon btn-light border shadow-sm"
                                               data-bs-toggle="tooltip" title="View Extracted Text">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                            <a href="{{ route('user.knowledgeBase.edit', $entry->id) }}"
                                               class="btn btn-sm btn-icon btn-light border shadow-sm"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>
                                            <a href="{{ route('user.knowledgeBase.delete', $entry->id) }}"
                                               class="btn btn-sm btn-icon btn-light border shadow-sm"
                                               data-bs-toggle="tooltip" title="Delete"
                                               onclick="return confirm('Delete this knowledge resource permanently?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-brain fa-4x text-light mb-3 d-block"></i>
                                        <h5 class="fw-bold">No Knowledge Base Found</h5>
                                        <p class="text-muted">Create knowledge entries to provide AI context for evaluations.</p>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                        <a href="{{ route('user.knowledgeBase.create') }}" class="btn btn-primary px-4 mt-2" style="border-radius: 10px;">
                                            <i class="fas fa-plus me-1"></i> Create Your First KB
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($knowledgeBase->total() > 0)
                    <div class="d-flex justify-content-between align-items-center p-4 bg-light border-top">
                        <div class="text-muted small">
                            Showing {{ $knowledgeBase->firstItem() }} to {{ $knowledgeBase->lastItem() }}
                            of {{ $knowledgeBase->total() }} entries
                        </div>
                        <div>{{ $knowledgeBase->appends(request()->input())->links('pagination::bootstrap-5') }}</div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-danger   { background-color: rgba(239,68,68,0.1);    color: #ef4444; }
    .bg-soft-primary  { background-color: rgba(59,130,246,0.1);   color: #3b82f6; }
    .bg-soft-info     { background-color: rgba(6,182,212,0.1);    color: #0891b2; }
    .bg-soft-success  { background-color: rgba(34,197,94,0.1);    color: #16a34a; }
    .bg-soft-secondary{ background-color: rgba(107,114,128,0.1);  color: #4b5563; }
    .btn-icon { width:32px; height:32px; display:flex; align-items:center; justify-content:center; padding:0; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        .forEach(el => new bootstrap.Tooltip(el));

    // Clear simulator state when modal closes
    document.getElementById('ragSimulatorModal').addEventListener('hidden.bs.modal', function () {
        resetSim();
    });
});

function resetSim() {
    ['simKeywordsBox','simNoKeywords','simNoMatch','simResult'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    document.getElementById('simPlaceholder').classList.remove('d-none');
    document.getElementById('simKeywords').innerHTML = '';
    document.getElementById('simResultContent').textContent = '';
}

function runSimulation() {
    const query     = document.getElementById('simQuery').value.trim();
    const companyId = document.getElementById('simCompany').value;
    const btn       = document.getElementById('simBtn');

    // Reset all result panels
    ['simKeywordsBox','simNoKeywords','simNoMatch','simResult'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    document.getElementById('simPlaceholder').classList.add('d-none');

    if (!query || query.length < 2) {
        document.getElementById('simNoKeywords').classList.remove('d-none');
        return;
    }

    // Loading state
    btn.disabled = true;
    document.getElementById('simBtnText').classList.add('d-none');
    document.getElementById('simBtnLoader').classList.remove('d-none');

    fetch("{{ route('user.knowledgeBase.searchTest') }}", {
        method : 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : '{{ csrf_token() }}'
        },
        body: JSON.stringify({ query: query, company_id: companyId })
    })
    .then(res => res.json())
    .then(data => {
        // 1. Always show keywords panel
        const keywords = data.keywords_found || [];
        if (keywords.length > 0) {
            const kwBox = document.getElementById('simKeywords');
            kwBox.innerHTML = keywords.map(k =>
                `<span class="badge text-dark rounded-pill px-3 py-1">${k}</span>`
            ).join('');
            document.getElementById('simKeywordsBox').classList.remove('d-none');
        } else {
            // No keywords extracted at all
            document.getElementById('simNoKeywords').classList.remove('d-none');
            return;
        }

        // 2. Show result or "no match"
        if (!data.matched || !data.result) {
            document.getElementById('simNoMatch').classList.remove('d-none');
        } else {
            document.getElementById('simResultContent').textContent = data.result;
            document.getElementById('simResult').classList.remove('d-none');
        }
    })
    .catch(err => {
        document.getElementById('simNoMatch').classList.remove('d-none');
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        document.getElementById('simBtnText').classList.remove('d-none');
        document.getElementById('simBtnLoader').classList.add('d-none');
    });
}

function copySimResult() {
    const text = document.getElementById('simResultContent').textContent;
    navigator.clipboard.writeText(text).then(() => alert('Context copied!'));
}
</script>
@endpush