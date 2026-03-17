@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <!-- Sidebar Info -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold text-dark mb-0">Resource Info</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="text-center mb-4 p-4 bg-light rounded-4">
                        <i class="fas fa-book-open text-primary fa-5x mb-3"></i>
                        <h5 class="fw-bold text-dark">{{ $data->title }}</h5>
                        <p class="text-muted small mb-0">Text-based Knowledge Entry</p>
                    </div>
                    
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex justify-content-between p-3 border-0">
                            <span class="text-muted">Company</span>
                            <span class="fw-bold">{{ $data->company->company_name }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between p-3 border-0">
                            <span class="text-muted">Status</span>
                            <span class="badge {{ $data->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary' }}">
                                {{ $data->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between p-3 border-0">
                            <span class="text-muted">Created</span>
                            <span class="fw-bold">{{ $data->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold text-dark mb-2">Keywords</h6>
                        @if($data->keywords)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $data->keywords) as $kw)
                                    <span class="badge bg-light text-dark border px-3 py-2">{{ trim($kw) }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small italic">No keywords assigned.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Knowledge Content -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Knowledge Content</h5>
                        <p class="text-muted small mb-0">This information is used by the AI engine</p>
                    </div>
                    <button class="btn btn-sm btn-light border" onclick="copyKBText()" id="copyBtn">
                        <i class="fas fa-copy me-1"></i> Copy Text
                    </button>
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 rounded-4 shadow-inner" style="max-height: 70vh; overflow-y: auto; white-space: pre-wrap; font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.7;" id="kbContentText">
{{ $data->content }}
                    </div>
                </div>
            </div>

            <!-- Management Actions -->
            <div class="mt-4 text-end">
                <a href="{{ route('user.knowledgeBase.list') }}" class="btn btn-light px-4 border rounded-pill">Back to List</a>
                <a href="{{ route('user.knowledgeBase.edit', $data->id) }}" class="btn btn-warning px-4 text-white rounded-pill">Edit Entry</a>
                <a href="{{ route('user.knowledgeBase.delete', $data->id) }}" 
                   class="btn btn-danger px-4 rounded-pill"
                   onclick="return confirm('Delete this knowledge entry permanently?')">Delete</a>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-success { background-color: rgba(34, 197, 94, 0.1); color: #16a34a; }
    .bg-soft-secondary { background-color: rgba(107, 114, 128, 0.1); color: #4b5563; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
</style>
@endsection

@push('scripts')
<script>
    function copyKBText() {
        const text = document.getElementById('kbContentText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copyBtn');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-light');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-light');
            }, 2000);
        });
    }
</script>
@endpush