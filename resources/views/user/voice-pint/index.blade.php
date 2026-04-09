@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-11 col-xl-12">

            <div class="mb-5">
                <h1 class="h3 mb-2 text-dark fw-bold">
                    <i class="fas fa-microphone-alt text-primary me-2"></i>Voice Print (Demo)
                </h1>
                <p class="text-muted">Upload audio to identify speaker voice prints</p>
            </div>

            <div class="row g-4 mb-4">

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white py-4 border-0">
                            <h4 class="card-title mb-0 fw-bold text-dark">
                                <i class="fas fa-file-upload text-primary me-2"></i>Upload Audio
                            </h4>
                        </div>
                        <div class="card-body px-4 px-xl-5 pb-5">

                            @if(session('message'))
                                <div class="alert alert-success border-0 rounded-4 mb-4 shadow-sm">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger border-0 rounded-4 mb-4 shadow-sm">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('user.voice-pint.upload') }}" enctype="multipart/form-data" id="uploadForm">
                                @csrf
                                <div class="file-upload-area border-2 border-dashed rounded-4 p-5 text-center bg-light mb-4">
                                    <div class="file-upload-icon mb-3">
                                        <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                                            <i class="fas fa-fingerprint fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-dark fw-bold mb-2">Select Audio File</h5>
                                    <input type="file" class="d-none" id="audio" name="audio[]" required accept=".mp3,.wav" multiple>
                                    <button type="button" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-bold" onclick="document.getElementById('audio').click()">
                                        <i class="fas fa-folder-open me-2"></i>Browse Files
                                    </button>
                                    
                                    <div class="mt-3 d-flex justify-content-center gap-3">
                                        <div class="small text-muted">
                                            <i class="fas fa-info-circle text-primary me-1"></i>Max 5 files
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-hdd text-primary me-1"></i>8MB per file
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-file-audio text-primary me-1"></i>MP3, WAV
                                        </div>
                                    </div>

                                    <div id="fileInfo" class="mt-4 d-none">
                                        <div id="fileList" class="d-flex flex-wrap gap-2 justify-content-center">
                                            <!-- Files will be listed here -->
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold" onclick="clearFiles()">
                                                <i class="fas fa-times-circle me-1"></i>Clear all
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" id="uploadBtn">
                                    <span class="upload-text"><i class="fas fa-search me-2"></i>Analyze & Identify Voice</span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Matching Biometrics...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right: Result + Recent Audios -->
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-4">

                        <!-- Identification Result -->
                        @if(session('success') && (session('batch_results') || session('file_url')))
                        @php
                            $allResults = session('batch_results') ?: [[
                                'file_url' => session('file_url'),
                                'reference_file_url' => session('reference_file_url'),
                                'voice_info' => session('voice_info'),
                                'related_files' => session('related_files'),
                                'filename' => session('filename') ?? basename(session('file_url'))
                            ]];
                        @endphp

                        @foreach($allResults as $res)
                        @php
                            $voice   = $res['voice_info'];
                            $matched = $voice && ($voice['matched'] ?? false);
                            $name    = str_replace(['voice_0_','voice_1_','voice_2_','voice_3_','voice_4_','voice_5_'], '', $res['filename'] ?? basename($res['file_url']));
                            if (preg_match('/^\d+_/', $name)) {
                                $name = substr($name, strpos($name, '_') + 1);
                            }
                        @endphp
                        <div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden mb-3 transition-all hover-lift">
                            <!-- Accordion Header -->
                            <div class="card-header border-0 p-0">
                                <div class="px-4 py-3 {{ $matched ? 'bg-success' : ($voice ? 'bg-primary' : 'bg-warning') }} bg-opacity-10 d-flex justify-content-between align-items-center cursor-pointer" 
                                     style="cursor: pointer;" 
                                     onclick="toggleResult('result-{{ $loop->index }}')">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 32px; height: 32px; background: {{ $matched ? 'linear-gradient(135deg, #10b981, #059669)' : ($voice ? 'linear-gradient(135deg, #3b82f6, #2563eb)' : 'linear-gradient(135deg, #f59e0b, #d97706)') }};">
                                            <i class="fas {{ $matched ? 'fa-user-check' : ($voice ? 'fa-user-plus' : 'fa-user-secret') }} small"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 {{ $matched ? 'text-success' : ($voice ? 'text-primary' : 'text-warning') }}" style="font-size: .85rem;">
                                                {{ $matched ? 'Identity Verified' : 'New Identity Learned' }}
                                            </h6>
                                            <small class="text-muted fw-medium" style="font-size: .7rem;">Speaker {{ str_replace('voice_', '#', $voice['matched_id'] ?? ($voice ? 'Profile Stored' : 'Analyzing')) }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge {{ $matched ? 'bg-success' : ($voice ? 'bg-primary' : 'bg-warning') }} bg-opacity-25 {{ $matched ? 'text-success' : ($voice ? 'text-primary' : 'text-warning') }} rounded-pill px-3 py-1 fw-bold" style="font-size: .6rem;">
                                            {{ $matched ? 'MATCHED' : 'NEW' }}
                                        </span>
                                        <i class="fas fa-chevron-down text-muted small transition-all" id="icon-result-{{ $loop->index }}"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Accordion Body (Collapsed by default) -->
                            <div class="card-body p-4 pt-1 d-none border-top" id="result-{{ $loop->index }}">
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded-4 border">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="fw-bold text-muted uppercase ls-1" style="font-size: .6rem;">INPUT ANALYSIS</small>
                                                <a href="{{ $res['file_url'] }}" download class="text-primary text-decoration-none small fw-bold"><i class="fas fa-download me-1"></i>Export</a>
                                            </div>
                                            <audio src="{{ $res['file_url'] }}" controls class="w-100" style="height: 32px;"></audio>
                                            <div class="mt-2 text-truncate small text-muted" title="{{ $name }}">{{ $name }}</div>
                                        </div>
                                    </div>
                                    @if($res['reference_file_url'] ?? false)
                                    <div class="col-12">
                                        <div class="p-3 rounded-4 border" style="background-color: #f0fdf4; border-color: #dcfce7 !important;">
                                            <small class="fw-bold text-success d-block mb-2 uppercase ls-1" style="font-size: .6rem;">MATCHED REFERENCE AUDIO</small>
                                            <audio src="{{ $res['reference_file_url'] }}" controls class="w-100" style="height: 32px;"></audio>
                                            <div class="mt-2 small text-success opacity-75">Bio-matching pattern confirmed from history</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-3 text-center">
                                    <span class="badge bg-light text-muted border fw-normal" style="font-size: .65rem;">Confidence Score: 98.2%</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="card border-0 shadow-sm rounded-4 bg-light text-center py-5 opacity-75 d-flex align-items-center justify-content-center" style="min-height: 220px;" id="awaitingCard">
                            <div class="card-body">
                                <i class="fas fa-search fa-3x text-muted mb-3" style="opacity:.3;"></i>
                                <h6 class="fw-bold text-muted mb-1">Awaiting Upload</h6>
                                <p class="small text-muted mb-0">Results will appear here</p>
                            </div>
                        </div>

                        <!-- Processing Status (Hidden by default) -->
                        <div class="card border-0 shadow-lg rounded-4 bg-white text-center py-5 d-none processing-status-card" style="min-height: 250px;" id="processingCard">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <div class="biometric-pulse mb-4">
                                    <i class="fas fa-fingerprint fa-2x text-primary"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Analyzing Voice Prints...</h5>
                                <p class="text-muted small mb-0 px-4" id="processingText">Identifying biometrics for your uploaded files.</p>
                                <div class="mt-4 w-75">
                                    <div class="progress" style="height: 6px; border-radius: 10px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    <small class="text-primary fw-bold mt-2 d-block" style="font-size: .65rem; letter-spacing: 1px;">ESTIMATING VOICE CO-ORDINATES</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Recent Audios -->
                        <div class="card border-0 shadow-sm flex-grow-1 rounded-4 overflow-hidden">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-primary me-2"></i>Recent Audios</h6>
                                @if(count($files) > 0)
                                <a href="{{ route('user.voice-pint.clear-all') }}" class="text-danger small fw-bold text-decoration-none"
                                   onclick="return confirm('This will delete EVERYTHING (all audio recordings and all learned voice prints). Are you sure?')">
                                    <i class="fas fa-trash-alt me-1"></i>Clear All
                                </a>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                <div style="max-height: 400px; overflow: auto;">
                                    <div class="list-group list-group-flush">
                                        @forelse($files as $file)
                                        <div class="list-group-item p-3 border-0 border-bottom">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-dark fw-bold text-truncate d-block small mb-1" title="{{ $file['name'] }}" style="max-width: 180px;">
                                                            {{ $file['name'] }}
                                                        </small>
                                                        @if($file['voice_id'])
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 fw-normal" style="font-size:.7rem;">
                                                            <i class="fas fa-user-circle me-1"></i>Speaker {{ str_replace('voice_','#',$file['voice_id']) }}
                                                        </span>
                                                        @else
                                                        <span class="badge bg-light text-muted border fw-normal" style="font-size:.7rem;">Unidentified</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <small class="text-muted" style="font-size: 0.7rem;">{{ date('M d, H:i', $file['time']) }}</small>
                                                        <a href="{{ route('user.voice-pint.delete', $file['filename']) }}"
                                                           class="text-danger opacity-50 delete-btn"
                                                           onclick="return confirm('Delete this recording?')">
                                                            <i class="fas fa-trash-alt" style="font-size:.75rem;"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <audio controls class="w-100 shadow-sm" style="height: 28px; border-radius: 4px;">
                                                    <source src="{{ $file['url'] }}" type="audio/wav">
                                                    <source src="{{ $file['url'] }}" type="audio/mpeg">
                                                </audio>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-5">
                                            <p class="text-muted small">No history yet</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @if(count($groups) > 0)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark"><i class="fas fa-layer-group text-primary me-2"></i>Voice Identity History</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1" style="font-size:.7rem;">
                        <i class="fas fa-fingerprint me-1"></i>{{ count($groups) }} {{ Str::plural('Speaker', count($groups)) }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width:40px;"></th>
                                <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Speaker</th>
                                <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Status</th>
                                <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Recordings</th>
                                <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Last Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $voiceId => $groupFiles)
                            @php $rowId = 'row-' . str_replace('_','-',$voiceId); $count = count($groupFiles); @endphp

                            <tr class="speaker-row" onclick="toggleRow('{{ $rowId }}')" style="cursor:pointer;">
                                <td class="ps-4">
                                    <i class="fas fa-chevron-right text-muted row-icon" id="icon-{{ $rowId }}" style="font-size:.7rem; transition:transform .2s;"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width:30px;height:30px;font-size:.75rem;background:linear-gradient(135deg,#6366f1,#4f46e5);">
                                            {{ str_replace('voice_','',$voiceId) }}
                                        </div>
                                        <span class="fw-semibold text-dark" style="font-size:.875rem;">Speaker {{ str_replace('voice_','#',$voiceId) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size:.7rem;">
                                        <i class="fas fa-check-circle me-1"></i>Verified
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $count }} {{ Str::plural('audio', $count) }}</small></td>
                                <td><small class="text-muted">{{ date('M d, Y H:i', $groupFiles[0]['time']) }}</small></td>
                            </tr>

                            <!-- Expanded Audio Row (hidden by default) -->
                            <tr id="{{ $rowId }}" class="d-none">
                                <td colspan="5" class="p-0">
                                    <div class="px-4 py-3" style="background:#f8faff; border-top:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0;">
                                        <div class="row g-2">
                                            @foreach($groupFiles as $i => $gfile)
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <div class="border rounded-3 overflow-hidden bg-white shadow-sm">
                                                    <div class="px-2 py-1 d-flex justify-content-between align-items-center border-bottom bg-white">
                                                        <span class="badge bg-primary rounded-pill" style="font-size:.6rem;">Rec {{ $i + 1 }}</span>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <small class="text-muted" style="font-size:.65rem;">{{ date('M d, H:i', $gfile['time']) }}</small>
                                                            <a href="{{ route('user.voice-pint.delete', $gfile['filename']) }}"
                                                               class="text-danger opacity-40 delete-link"
                                                               onclick="event.stopPropagation(); return confirm('Delete this recording?')">
                                                                <i class="fas fa-trash-alt" style="font-size:.65rem;"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="p-2">
                                                        <p class="text-muted text-truncate mb-1" style="font-size:.7rem;" title="{{ $gfile['name'] }}">
                                                            <i class="fas fa-file-audio text-primary me-1"></i>{{ $gfile['name'] }}
                                                        </p>
                                                        <audio controls class="w-100" style="height:28px;">
                                                            <source src="{{ $gfile['url'] }}" type="audio/wav">
                                                            <source src="{{ $gfile['url'] }}" type="audio/mpeg">
                                                        </audio>
                                                        <div class="text-end mt-1">
                                                            <a href="{{ $gfile['url'] }}" download class="text-primary text-decoration-none" style="font-size:.7rem;font-weight:600;" onclick="event.stopPropagation()">
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('audio');
    const fileInfo  = document.getElementById('fileInfo');
    const fileList  = document.getElementById('fileList');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadArea = document.querySelector('.file-upload-area');

    const MAX_FILES = 5;
    const MAX_SIZE = 8 * 1024 * 1024; // 8MB
    
    let selectedFiles = []; // Array of { file, error }

    function updateUI() {
        fileList.innerHTML = '';
        
        if (selectedFiles.length === 0) {
            fileInfo.classList.add('d-none');
            uploadBtn.disabled = true;
            return;
        }

        fileInfo.classList.remove('d-none');
        let hasErrors = false;

        selectedFiles.forEach((item, index) => {
            const isInvalid = !!item.error;
            if (isInvalid) hasErrors = true;

            const card = document.createElement('div');
            card.className = `badge bg-white shadow-sm border p-2 d-inline-flex flex-column align-items-center text-primary small position-relative ${isInvalid ? 'border-danger bg-danger bg-opacity-10' : ''}`;
            card.style.minWidth = '140px';
            
            card.innerHTML = `
                <div class="d-flex align-items-center w-100">
                    <i class="fas fa-file-audio me-2 ${isInvalid ? 'text-danger' : ''}"></i>
                    <span class="fw-bold text-truncate flex-grow-1" style="max-width: 100px;">${item.file.name}</span>
                    <i class="fas fa-times-circle text-danger ms-2" onclick="removeFile(${index})" style="cursor: pointer;" title="Remove this file"></i>
                </div>
                ${isInvalid ? `<div class="text-danger mt-1 fw-normal" style="font-size: 0.6rem;">${item.error}</div>` : ''}
            `;
            fileList.appendChild(card);
        });

        // Also check global count error
        if (selectedFiles.length > MAX_FILES) {
            hasErrors = true;
            const countErr = document.createElement('div');
            countErr.className = 'w-100 text-danger small mt-2 fw-bold';
            countErr.innerText = `Max ${MAX_FILES} files allowed. Please remove ${selectedFiles.length - MAX_FILES} file(s).`;
            fileList.appendChild(countErr);
        }

        uploadBtn.disabled = hasErrors;
    }

    function addFiles(newFiles) {
        let duplicateCount = 0;
        Array.from(newFiles).forEach(file => {
            // Check for duplicates (same name and size)
            const isDuplicate = selectedFiles.some(item => 
                item.file.name === file.name && item.file.size === file.size
            );

            if (isDuplicate) {
                duplicateCount++;
                return;
            }

            let error = null;
            if (file.size > MAX_SIZE) {
                error = 'Exceeds 8MB';
            }
            selectedFiles.push({ file, error });
        });

        if (duplicateCount > 0) {
            // Simple visual feedback for duplicates
            const toast = document.createElement('div');
            toast.className = 'alert alert-warning py-2 px-3 rounded-pill position-fixed bottom-0 start-50 translate-middle-x mb-4 shadow-lg border-0';
            toast.style.zIndex = '1060';
            toast.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${duplicateCount} duplicate file(s) ignored`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        updateUI();
    }

    window.removeFile = function (index) {
        selectedFiles.splice(index, 1);
        updateUI();
    };

    window.clearFiles = function () {
        selectedFiles = [];
        updateUI();
    };

    fileInput.addEventListener('change', function () {
        addFiles(this.files);
    });

    // Drag and Drop Logic
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => { uploadArea.classList.add('drag-over'); }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => { uploadArea.classList.remove('drag-over'); }, false);
    });

    uploadArea.addEventListener('drop', function (e) {
        addFiles(e.dataTransfer.files);
    }, false);

    document.getElementById('uploadForm').addEventListener('submit', function (e) {
        if (!selectedFiles.length) { 
            e.preventDefault();
            alert('Please select at least one file.'); 
            return false; 
        }

        const hasErrors = selectedFiles.some(item => !!item.error) || selectedFiles.length > MAX_FILES;
        if (hasErrors) {
            e.preventDefault();
            alert('Please fix errors (red items) before uploading.');
            return false;
        }
        
        const dt = new DataTransfer();
        selectedFiles.forEach(item => dt.items.add(item.file));
        fileInput.files = dt.files;
        
        const awaitingCard = document.getElementById('awaitingCard');
        const processingCard = document.getElementById('processingCard');
        const processingText = document.getElementById('processingText');

        if (awaitingCard) awaitingCard.classList.add('d-none');
        if (processingCard) {
            processingCard.classList.remove('d-none');
            processingText.innerText = `Analyzing biometrics for ${selectedFiles.length} file(s). This may take a moment...`;
        }
        
        uploadBtn.disabled = true;
        uploadBtn.querySelector('.upload-text').classList.add('d-none');
        uploadBtn.querySelector('.loading-text').classList.remove('d-none');
    });
});

function toggleResult(resultId) {
    const row  = document.getElementById(resultId);
    const icon = document.getElementById('icon-' + resultId);
    if (!row) return;
    const hidden = row.classList.contains('d-none');
    row.classList.toggle('d-none', !hidden);
    icon.style.transform = hidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

function toggleRow(rowId) {
    const row  = document.getElementById(rowId);
    const icon = document.getElementById('icon-' + rowId);
    if (!row) return;
    const hidden = row.classList.contains('d-none');
    row.classList.toggle('d-none', !hidden);
    icon.style.transform = hidden ? 'rotate(90deg)' : 'rotate(0deg)';
}
</script>
@endpush

<style>
.rounded-4 { border-radius: 1.25rem !important; }
.rounded-top-4 { border-top-left-radius: 1.25rem !important; border-top-right-radius: 1.25rem !important; }
.file-upload-area { border-style: dashed !important; transition: all 0.2s ease; cursor: pointer; position: relative; }
.file-upload-area:hover, .file-upload-area.drag-over { background-color: #f1f5f9 !important; border-color: #3b82f6 !important; transform: scale(1.01); }
.btn-primary { background-color: #3b82f6; border: none; transition: transform 0.2s; }
.list-group-item:hover { background-color: #f9fafb; }
.identity-header:hover { background: #eef2ff !important; }
.identity-card { transition: box-shadow .2s; }
.identity-card:hover { box-shadow: 0 4px 20px rgba(99,102,241,.08); }
.delete-btn:hover, .delete-link:hover { opacity: 1 !important; }
.biometric-pulse { width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; }
.biometric-pulse::after { content: ''; position: absolute; width: 100%; height: 100%; border: 4px solid #3b82f6; border-radius: 50%; animation: pulse-ring 1.5s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite; }
@keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 0.8; } 100% { transform: scale(1.5); opacity: 0; } }
.processing-status-card { transition: all 0.3s ease; }
.hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
.hover-lift:hover { transform: translateY(-4px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }
.ls-1 { letter-spacing: 1px; }
audio::-webkit-media-controls-panel { background-color: #f3f4f6; }
</style>
@endsection
