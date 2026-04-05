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
                            <i class="fas fa-vial text-white fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold text-dark">Stage 1: KB Identification Simulator</h4>
                            <p class="text-muted mb-0 small">Test how GPT selects Knowledge Base entries — one per Q/A pair</p>
                        </div>
                    </div>
                    <a href="{{ route('user.knowledgeBase.list') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-arrow-left me-1"></i> Back to KB List
                    </a>
                </div>

                <div class="card-body p-4 bg-light bg-opacity-10">
                    <div class="row g-4">
                        {{-- Left: Input Form --}}
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm rounded-4 p-4">
                                <h6 class="fw-bold mb-4 text-primary"><i class="fas fa-edit me-2"></i>Simulation Settings</h6>
                                
                                <form action="{{ route('user.knowledgeBase.simulator.run') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">1. Select Company</label>
                                        <select name="company_id" class="form-select border-0 bg-light py-2 shadow-sm rounded-3 @error('company_id') is-invalid @enderror">
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ (isset($selected_company) && $selected_company == $company->id) ? 'selected' : '' }}>
                                                    {{ $company->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">2. Conversation Q/A or Transcription</label>     
                                        <textarea name="query_text" class="form-control border-0 bg-light shadow-sm rounded-3 @error('query_text') is-invalid @enderror" 
                                            rows="12" placeholder="Customer: How do I return a faulty item?&#10;Agent: You can return it within 14 days with the receipt.&#10;&#10;Customer: What about store credit?&#10;Agent: We offer store credit for returns without a receipt.">{{ $query_text ?? '' }}</textarea>
                                        @error('query_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary fw-bold py-3 shadow-sm rounded-3">
                                            <i class="fas fa-play me-2"></i> Run
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Right: AI Result --}}
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h6 class="fw-bold mb-4 text-success"><i class="fas fa-magic me-2"></i>AI Matcher Result</h6>

                                @if(isset($results))
                                    @if(empty($results))
                                        <div class="text-center py-5">
                                            <div class="bg-soft-warning p-4 rounded-circle d-inline-block mb-3">
                                                <i class="fas fa-search-minus fa-3x text-warning"></i>
                                            </div>
                                            <h5 class="fw-bold">No Matches Found</h5>
                                            <p class="text-muted">The AI could not confidently match any Q/A pair to a defined Knowledge Base entry.</p>
                                        </div>
                                    @else

                                        {{-- ── Per-pair breakdown ── --}}
                                        @if(!empty($pair_results))
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-secondary mb-3">
                                                    <i class="fas fa-list-ol me-2"></i>Pair-by-Pair Analysis
                                                    <span class="badge bg-secondary ms-2">{{ count($pair_results) }} pair{{ count($pair_results) !== 1 ? 's' : '' }}</span>
                                                </h6>

                                                @foreach($pair_results as $pairIndex => $pair)
                                                    <div class="card border-0 bg-light rounded-4 mb-2 overflow-hidden pair-card">
                                                        <div class="card-header border-0 py-2 px-3 d-flex justify-content-between align-items-center
                                                            {{ empty($pair['matched_kb_ids']) ? 'bg-soft-warning' : 'bg-soft-success' }}">
                                                            <span class="fw-bold small">
                                                                <i class="fas fa-comments me-1"></i> Pair #{{ $pairIndex + 1 }}
                                                            </span>
                                                            <div class="d-flex gap-1 flex-wrap">
                                                                @if(empty($pair['matched_kb_ids']))
                                                                    <span class="badge bg-warning text-dark small">No Match</span>
                                                                @else
                                                                    @foreach($pair['matched_kb_ids'] as $kbId)
                                                                        @php
                                                                            $matchedKb = collect($kb_mapping_sent)->firstWhere('id', $kbId);
                                                                        @endphp
                                                                        <span class="badge bg-success small">
                                                                            <i class="fas fa-book me-1"></i>{{ $matchedKb['name'] ?? 'KB #'.$kbId }}
                                                                        </span>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="card-body py-2 px-3">
                                                            <pre class="mb-0 small text-dark pair-text">{{ $pair['pair_text'] }}</pre>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- ── Consolidated Selected KBs ── --}}
                                        <div class="alert alert-success border-0 rounded-3 mb-3 d-flex align-items-center">
                                            <i class="fas fa-check-circle fa-2x me-3"></i>
                                            <div>
                                                <div class="fw-bold">
                                                    {{ count($results) }} Knowledge Base{{ count($results) !== 1 ? 's' : '' }} Selected for Evaluation
                                                </div>
                                                <div class="small">These KBs will be sent to the evaluation stage.</div>
                                            </div>
                                        </div>

                                        <div class="results-list">
                                            @foreach($results as $kb)
                                                <div class="card border-0 bg-light rounded-4 mb-3 overflow-hidden">
                                                    <div class="card-header bg-soft-primary border-0 py-3 d-flex justify-content-between align-items-center">
                                                        <div class="fw-bold d-flex align-items-center">
                                                            <i class="fas fa-book me-2"></i> {{ $kb->title }}
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill px-3">SELECTED FOR EVALUATION</span>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <div class="small fw-bold text-muted text-uppercase mb-1">Keywords:</div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach(explode(',', $kb->keywords) as $kw)
                                                                    <span class="badge bg-white text-dark border small">{{ trim($kw) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="small fw-bold text-muted text-uppercase mb-1">Content Summary:</div>
                                                            <div class="small text-dark p-3 bg-white rounded-3 shadow-inner" style="max-height: 150px; overflow-y: auto; direction: auto;">
                                                                {{ $kb->content }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    @endif
                                @else
                                    <div class="text-center py-5 opacity-50">
                                        <i class="fas fa-robot fa-4x mb-3 text-light d-block"></i>
                                        <h5 class="fw-bold">Ready for Test</h5>
                                        <p class="text-muted">Fill in the conversation details on the left to test the AI's selection logic.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-soft-success { background-color: rgba(34, 197, 94, 0.1); color: #16a34a; }
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1); color: #d97706; }
    .bg-soft-info    { background-color: rgba(6, 181, 212, 0.1); color: #0891b2; }
    .shadow-inner    { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    .pair-card       { transition: box-shadow .2s; }
    .pair-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08) !important; }
    .pair-text       { white-space: pre-wrap; word-break: break-word; font-family: inherit; font-size: .82rem; }
</style>
@endsection
