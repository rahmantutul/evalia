@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-language text-primary me-2"></i>AI Translation
                    </h1>
                    <p class="text-muted">Translate text between 100+ languages with advanced AI accuracy</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-globe text-info me-1"></i>100+ Languages
                    </span>
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-5">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-3">Translation Complete!</h5>
                            <div class="translation-result p-4 bg-light rounded-3 border">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="source-text">
                                            <strong class="text-muted d-block mb-2">
                                                <i class="fas fa-arrow-right me-1"></i>Original Text
                                            </strong>
                                            <div class="p-3 bg-white rounded-2 border">
                                                {{ old('text') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="translated-text">
                                            <strong class="text-primary d-block mb-2">
                                                <i class="fas fa-flag me-1"></i>Translated Text
                                            </strong>
                                            <div class="p-3 bg-white rounded-2 border border-primary">
                                                {{ session('translated_text') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    <button class="btn btn-outline-primary btn-sm" onclick="copyTranslation()">
                                        <i class="fas fa-copy me-1"></i>Copy Translation
                                    </button>
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download me-1"></i>Export Text
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="swapLanguages()">
                                        <i class="fas fa-exchange-alt me-1"></i>Swap Languages
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                <!-- Main Translation Card -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white py-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0 fw-bold text-dark">
                                    <i class="fas fa-exchange-alt text-primary me-2"></i>Translate Text
                                </h4>
                                <span class="badge bg-success bg-opacity-10 text-success">Real-time</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <form method="POST" action="{{ url('/hamsa/translate') }}" id="translationForm">
                                @csrf
                                
                                <!-- Text Input Section -->
                                <div class="mb-5">
                                    <label for="text" class="form-label fw-semibold text-dark mb-3">
                                        <i class="fas fa-edit text-primary me-2"></i>Text to Translate
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-lg border-2" 
                                              id="text" 
                                              name="text" 
                                              rows="5" 
                                              placeholder="Enter the text you want to translate. You can paste up to 5000 characters..."
                                              required>{{ old('text') }}</textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            For best results, use complete sentences with proper punctuation
                                        </small>
                                        <small class="text-muted" id="charCount">0/5000 characters</small>
                                    </div>
                                </div>

                                <!-- Language Selection Section -->
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="source_language" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-search text-primary me-2"></i>Source Language
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="source_language" name="source_language">
                                                    <option value="auto" selected>Auto-detect Language</option>
                                                    <optgroup label="Popular Languages">
                                                        <option value="en">English</option>
                                                        <option value="ar">Arabic</option>
                                                        <option value="fr">French</option>
                                                        <option value="es">Spanish</option>
                                                        <option value="de">German</option>
                                                    </optgroup>
                                                    <optgroup label="Other Languages">
                                                        <option value="it">Italian</option>
                                                        <option value="pt">Portuguese</option>
                                                        <option value="ru">Russian</option>
                                                        <option value="zh">Chinese</option>
                                                        <option value="ja">Japanese</option>
                                                        <option value="ko">Korean</option>
                                                        <option value="hi">Hindi</option>
                                                    </optgroup>
                                                </select>
                                                <small class="form-text text-muted mt-2 d-block">
                                                    Let AI detect the language automatically
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <label for="target_language" class="form-label fw-semibold text-dark">
                                                    <i class="fas fa-flag text-primary me-2"></i>Target Language
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select border-0 shadow-sm" id="target_language" name="target_language" required>
                                                    <optgroup label="Popular Languages">
                                                        <option value="en" selected>English</option>
                                                        <option value="ar">Arabic</option>
                                                        <option value="fr">French</option>
                                                        <option value="es">Spanish</option>
                                                        <option value="de">German</option>
                                                    </optgroup>
                                                    <optgroup label="Other Languages">
                                                        <option value="it">Italian</option>
                                                        <option value="pt">Portuguese</option>
                                                        <option value="ru">Russian</option>
                                                        <option value="zh">Chinese</option>
                                                        <option value="ja">Japanese</option>
                                                        <option value="ko">Korean</option>
                                                        <option value="hi">Hindi</option>
                                                    </optgroup>
                                                </select>
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="swapLanguages()">
                                                        <i class="fas fa-exchange-alt me-1"></i>Swap Languages
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Translation Options -->
                                <div class="row g-4 mb-5">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <label class="form-label fw-semibold text-dark mb-3">
                                                    <i class="fas fa-cog text-primary me-2"></i>Translation Options
                                                </label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="formal_tone" name="formal_tone">
                                                            <label class="form-check-label text-dark" for="formal_tone">
                                                                Use formal tone
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="preserve_formatting" name="preserve_formatting" checked>
                                                            <label class="form-check-label text-dark" for="preserve_formatting">
                                                                Preserve formatting
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Your text is processed securely and privately
                                        </small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="translateBtn">
                                            <i class="fas fa-exchange-alt me-2"></i>
                                            <span class="translate-text">Translate Text</span>
                                            <span class="loading-text d-none">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Translating...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Information -->
                <div class="col-lg-4">
                    <!-- Supported Languages Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-globe-americas text-primary me-2"></i>Supported Languages
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="language-categories">
                                <div class="category mb-4">
                                    <h6 class="fw-semibold text-dark mb-3">Popular Languages</h6>
                                    <div class="language-grid mb-3">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">English</span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">Arabic</span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">French</span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">Spanish</span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">German</span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 mb-2 me-2 px-3 py-2">Chinese</span>
                                    </div>
                                </div>
                                
                                <div class="category">
                                    <h6 class="fw-semibold text-dark mb-3">Other Languages</h6>
                                    <div class="language-grid">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Japanese</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Korean</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Hindi</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Italian</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Portuguese</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Russian</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Dutch</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 mb-2 me-2 px-3 py-2">Turkish</span>
                                        <span class="badge bg-success bg-opacity-10 text-success border-0 mb-2 me-2 px-3 py-2">+90 more</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Translation Tips Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Translation Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="tips-list">
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Use complete sentences</strong>
                                        <p class="text-muted small mb-0">Context improves translation accuracy</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Check punctuation</strong>
                                        <p class="text-muted small mb-0">Proper punctuation helps AI understand structure</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Be specific with terms</strong>
                                        <p class="text-muted small mb-0">Technical terms may need clarification</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <div>
                                        <strong class="text-dark">Use auto-detect</strong>
                                        <p class="text-muted small mb-0">Let AI identify the source language automatically</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textTextarea = document.getElementById('text');
    const charCount = document.getElementById('charCount');
    const translateBtn = document.getElementById('translateBtn');
    const translationForm = document.getElementById('translationForm');
    
    // Character count update
    textTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count}/5000 characters`;
        
        if (count > 5000) {
            charCount.classList.add('text-danger');
            translateBtn.disabled = true;
        } else {
            charCount.classList.remove('text-danger');
            translateBtn.disabled = false;
        }
    });
    
    // Form submission with loading state
    translationForm.addEventListener('submit', function() {
        if (textTextarea.value.length > 5000) {
            alert('Text exceeds 5000 character limit. Please shorten your text.');
            return false;
        }
        
        translateBtn.disabled = true;
        translateBtn.querySelector('.translate-text').classList.add('d-none');
        translateBtn.querySelector('.loading-text').classList.remove('d-none');
    });
    
    // Initialize character count
    charCount.textContent = `${textTextarea.value.length}/5000 characters`;
});

// Swap source and target languages
function swapLanguages() {
    const sourceLang = document.getElementById('source_language');
    const targetLang = document.getElementById('target_language');
    
    // Store current values
    const sourceValue = sourceLang.value;
    const targetValue = targetLang.value;
    
    // Swap values
    sourceLang.value = targetValue;
    targetLang.value = sourceValue;
    
    // If source was auto, set it to the previous target
    if (sourceValue === 'auto') {
        sourceLang.value = targetValue;
    }
    
    // Visual feedback
    const swapBtn = event.target;
    swapBtn.innerHTML = '<i class="fas fa-check me-1"></i>Languages Swapped';
    swapBtn.classList.remove('btn-outline-primary');
    swapBtn.classList.add('btn-success');
    
    setTimeout(() => {
        swapBtn.innerHTML = '<i class="fas fa-exchange-alt me-1"></i>Swap Languages';
        swapBtn.classList.remove('btn-success');
        swapBtn.classList.add('btn-outline-primary');
    }, 2000);
}

// Copy translation to clipboard
function copyTranslation() {
    const translatedText = "{{ session('translated_text', '') }}";
    if (translatedText) {
        navigator.clipboard.writeText(translatedText).then(() => {
            // Visual feedback
            const copyBtn = event.target;
            const originalHtml = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
            copyBtn.classList.remove('btn-outline-primary');
            copyBtn.classList.add('btn-success');
            
            setTimeout(() => {
                copyBtn.innerHTML = originalHtml;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-primary');
            }, 2000);
        });
    }
}
</script>
@endpush

<style>
.card {
    border-radius: 12px;
}

.form-control, .form-select {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.alert {
    border-radius: 12px;
}

.badge {
    border-radius: 6px;
    font-weight: 500;
}

.language-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.translation-result {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
}

.tip-item {
    transition: all 0.3s ease;
}

.tip-item:hover {
    transform: translateX(5px);
}

.optgroup {
    font-weight: 600;
    color: #6c757d;
}

.optgroup option {
    font-weight: normal;
    color: #000;
}

.category h6 {
    font-size: 0.9rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}
</style>
@endsection