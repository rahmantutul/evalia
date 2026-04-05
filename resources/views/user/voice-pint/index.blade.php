@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-11 col-xl-12">

            <!-- Header -->
            <div class="mb-5">
                <h1 class="h3 mb-2 text-dark fw-bold">
                    <i class="fas fa-microphone-alt text-primary me-2"></i>Voice Pint
                </h1>
                <p class="text-muted">Upload audio to identify speaker voice prints</p>
            </div>

            <!-- ===== TOP ROW: Upload + Result / Recent Audios ===== -->
            <div class="row g-4 mb-4">

                <!-- Left: Upload Section -->
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
                                    <p class="text-muted mb-4 small">MP3 or WAV format up to 50MB</p>

                                    <input type="file" class="d-none" id="audio" name="audio" required accept=".mp3,.wav">
                                    <button type="button" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-bold" onclick="document.getElementById('audio').click()">
                                        <i class="fas fa-folder-open me-2"></i>Browse File
                                    </button>

                                    <div id="fileInfo" class="mt-4 d-none">
                                        <div class="badge bg-white shadow-sm border p-3 d-inline-flex align-items-center text-primary">
                                            <i class="fas fa-file-audio me-2"></i>
                                            <span id="fileName" class="fw-bold me-2"></span>
                                            <i class="fas fa-times-circle text-danger ms-2" onclick="clearFile()" style="cursor: pointer;"></i>
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
                    <div class="d-flex flex-column gap-4 h-100">

                        <!-- Identification Result -->
                        @if(session('success') && session('file_url'))
                        @php
                            $voice   = session('voice_info');
                            $related = session('related_files', []);
                            $matched = $voice && ($voice['matched'] ?? false);
                        @endphp
                        <div class="card border-0 shadow-lg bg-white rounded-4 overflow-hidden border-start border-5 {{ $matched ? 'border-success' : ($voice ? 'border-info' : 'border-warning') }}">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h5 class="fw-bold mb-1 {{ $matched ? 'text-success' : ($voice ? 'text-info' : 'text-warning') }}">
                                            <i class="fas {{ $matched ? 'fa-user-check' : ($voice ? 'fa-user-plus' : 'fa-user-secret') }} me-2"></i>
                                            @if(!$voice) Identity Service Offline
                                            @elseif($matched) Match Found!
                                            @else New Voice Learned!
                                            @endif
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            Identity: <strong>{{ ($matched ? $voice['matched_id'] : ($voice ? 'New Print Created' : 'Unknown')) }}</strong>
                                        </p>
                                    </div>
                                    <span class="badge {{ $matched ? 'bg-success' : ($voice ? 'bg-info' : 'bg-warning') }} rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        {{ $matched ? 'Confirmed' : ($voice ? 'Database Updated' : 'Offline') }}
                                    </span>
                                </div>

                                <div class="bg-light rounded-4 p-3 mb-3 shadow-inner">
                                    <audio controls class="w-100" style="height: 42px;">
                                        <source src="{{ session('file_url') }}" type="audio/wav">
                                        <source src="{{ session('file_url') }}" type="audio/mpeg">
                                    </audio>
                                </div>

                                <!-- Hamsa Transcription Result (New) -->
                                @if(session('hamsa_response'))
                                <div class="hamsa-result mb-3">
                                    <h6 class="fw-bold text-dark mb-2 small"><i class="fas fa-file-alt text-primary me-2"></i>Current Transcription</h6>
                                    <div class="rounded-3 border p-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                                        @php
                                            $hamsaData = session('hamsa_response');
                                            $transcription = "";
                                            if (isset($hamsaData['results']) && is_array($hamsaData['results'])) {
                                                foreach($hamsaData['results'] as $segment) {
                                                    $transcription .= ($segment['speaker'] ?? 'Unknown') . ": " . ($segment['transcript'] ?? $segment['text'] ?? '') . "\n\n";
                                                }
                                            } elseif (isset($hamsaData['transcript'])) {
                                                $transcription = $hamsaData['transcript'];
                                            } else {
                                                $transcription = is_string($hamsaData) ? $hamsaData : json_encode($hamsaData, JSON_PRETTY_PRINT);
                                            }
                                        @endphp
                                        <pre class="mb-0 small text-muted" style="white-space: pre-wrap; font-family: inherit;">{{ $transcription ?: 'No transcription text found.' }}</pre>
                                    </div>
                                </div>
                                @endif

                                @if(session('reference_response'))
                                <div class="hamsa-result mb-4">
                                    <h6 class="fw-bold text-success mb-2 small"><i class="fas fa-history me-2"></i>Reference Transcription (Matched)</h6>
                                    <div class="rounded-3 border p-3" style="background-color: #f0fdf4; max-height: 200px; overflow-y: auto;">
                                        @php
                                            $refData = session('reference_response');
                                            $refText = "";
                                            if (isset($refData['results']) && is_array($refData['results'])) {
                                                foreach($refData['results'] as $segment) {
                                                    $refText .= ($segment['speaker'] ?? 'Unknown') . ": " . ($segment['transcript'] ?? $segment['text'] ?? '') . "\n\n";
                                                }
                                            } elseif (isset($refData['transcript'])) {
                                                $refText = $refData['transcript'];
                                            } else {
                                                $refText = is_string($refData) ? $refData : json_encode($refData, JSON_PRETTY_PRINT);
                                            }
                                        @endphp
                                        <pre class="mb-0 small text-dark opacity-75" style="white-space: pre-wrap; font-family: inherit;">{{ $refText ?: 'No reference text available.' }}</pre>
                                    </div>
                                </div>
                                @endif

                                <div class="row g-2 text-center">
                                    <div class="col-12">
                                        <a href="{{ session('file_url') }}" download class="btn btn-dark w-100 py-2 rounded-3 fw-bold">
                                            <i class="fas fa-download me-2"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card border-0 shadow-sm rounded-4 bg-light text-center py-5 opacity-75 d-flex align-items-center justify-content-center" style="min-height: 220px;">
                            <div class="card-body">
                                <i class="fas fa-search fa-3x text-muted mb-3" style="opacity:.3;"></i>
                                <h6 class="fw-bold text-muted mb-1">Awaiting Upload</h6>
                                <p class="small text-muted mb-0">Results will appear here</p>
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
                                                            <i class="fas fa-user-circle me-1"></i>{{ $file['voice_id'] }}
                                                        </span>
                                                        @else
                                                        <span class="badge bg-light text-muted border fw-normal" style="font-size:.7rem;">Unidentified</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <small class="text-muted" style="font-size: 0.7rem;">{{ date('M d', $file['time']) }}</small>
                                                        <a href="{{ route('user.voice-pint.delete', $file['filename']) }}"
                                                           class="text-danger opacity-50 delete-btn"
                                                           onclick="return confirm('Delete this recording? If it is the last one for this identity, the voice print will also be removed.')">
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

            <!-- ===== BOTTOM: Voice Identity History (Expandable Table) ===== -->
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

                            <!-- Speaker Row (clickable) -->
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
    const fileName  = document.getElementById('fileName');
    const uploadBtn = document.getElementById('uploadBtn');

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileInfo.classList.remove('d-none');
        }
    });

    document.getElementById('uploadForm').addEventListener('submit', function () {
        if (!fileInput.files.length) { alert('Please select a file.'); return false; }
        uploadBtn.disabled = true;
        uploadBtn.querySelector('.upload-text').classList.add('d-none');
        uploadBtn.querySelector('.loading-text').classList.remove('d-none');
    });

    window.clearFile = function () {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
    };
});

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
.file-upload-area { border-style: dashed !important; transition: all 0.2s ease; cursor: pointer; }
.file-upload-area:hover { background-color: #f1f5f9 !important; border-color: #3b82f6 !important; }
.btn-primary { background-color: #3b82f6; border: none; transition: transform 0.2s; }
.list-group-item:hover { background-color: #f9fafb; }
.identity-header:hover { background: #eef2ff !important; }
.identity-card { transition: box-shadow .2s; }
.identity-card:hover { box-shadow: 0 4px 20px rgba(99,102,241,.08); }
.delete-btn:hover, .delete-link:hover { opacity: 1 !important; }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,.05); }
audio::-webkit-media-controls-panel { background-color: #f3f4f6; }
</style>
@endsection
