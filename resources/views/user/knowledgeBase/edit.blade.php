@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-4 mt-3">
        <div class="col-md-10">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header bg-white py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning p-3 rounded-4 me-3 shadow-sm">
                            <i class="fas fa-edit text-white fa-xl"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold text-dark">Edit Knowledge Entry</h4>
                            <p class="text-muted mb-0">Updating: <strong>{{ $kb->title }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-5 pb-5">
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('user.knowledgeBase.update', $kb->id) }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Company Selection -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Select Company <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="icofont-bank-alt"></i></span>
                                    <select name="company_id" class="form-select border-0 bg-light py-2" style="border-radius: 0 10px 10px 0;" required>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ $kb->company_id == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">KB Title <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-heading"></i></span>
                                    <input type="text" name="title" class="form-control border-0 bg-light py-2" value="{{ $kb->title }}" style="border-radius: 0 10px 10px 0;" required>
                                </div>
                            </div>

                            <!-- Content / Description -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Knowledge Details (Text) <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <textarea name="content" id="kbContent" class="form-control border-0 bg-light" rows="10" 
                                        style="border-radius: 15px;" maxlength="5000" required>{{ $kb->content }}</textarea>
                                    <div class="position-absolute bottom-0 end-0 p-2 text-muted small">
                                        <span id="charCount">{{ strlen($kb->content) }}</span>/5000
                                    </div>
                                </div>
                            </div>

                            <!-- Keywords -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Keywords (Search Tags)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-tags"></i></span>
                                    <input type="text" name="keywords" class="form-control border-0 bg-light py-2" 
                                        value="{{ $kb->keywords }}"
                                        placeholder="e.g. greeting, opening, introduction (comma separated)" 
                                        style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>

                            <!-- Status Toggle -->
                            <div class="col-12">
                                <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center shadow-sm">
                                    <div>
                                        <label class="form-check-label fw-bold text-dark" for="isActive">Active Status</label>
                                        <div class="small text-muted">Inactive KBs will not be used in AI evaluations.</div>
                                    </div>
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="isActive" value="1" {{ $kb->is_active ? 'checked' : '' }} style="width: 50px; height: 25px;">
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-5">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('user.knowledgeBase.list') }}" class="btn btn-light px-5 py-2 fw-bold" style="border-radius: 12px;">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 12px; transition: 0.3s;" id="submitBtn">
                                        <i class="fas fa-save me-2"></i> Update Knowledge Entry
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
        border: 1px solid #0d6efd !important;
    }

    .tagify {
        border: none !important;
        background: #f8f9fa;
        border-radius: 0 10px 10px 0 !important;
        padding: 5px 10px !important;
    }
    .tagify--focus {
        background: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    }
    .tagify__tag {
        margin: 2px !important;
    }
</style>

@endsection

@push('scripts')
<script>
    const kbContent = document.getElementById('kbContent');
    const charCount = document.getElementById('charCount');

    kbContent.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Initialize Tagify
    const keywordsInput = document.querySelector('input[name="keywords"]');
    const tagify = new Tagify(keywordsInput, {
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    });

    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Updating...';
    });
</script>
@endpush