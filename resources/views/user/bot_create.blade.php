@extends('user.layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('/') }}assets/css/create-bot.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <form action="{{ route('user.bot.store') }}" method="get" enctype="multipart/form-data">
                @csrf
                <div class="page-title-box d-flex justify-content-between align-items-center mb-3">
                    <h4 class="page-title">Bot Configuration</h4>
                    <a href="{{ route('user.bot.store') }}" class="submit-btn">
                        <i class="fas fa-save me-2"></i> Save
                    </a>
                </div>

                <div class="bot-config-container">
                    <div class="row g-3">
                         <div class="form-header">
                            <h2 class="form-header__title">Basic Setting</h2>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bot Name</label>
                            <input type="text" name="bot_name" class="form-control" placeholder="Support Assistant" value="{{ old('bot_name') ?? $config->bot_name ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Language</label>
                            <select class="form-select select2" name="language">
                                <option value="english" {{ (old('language') ?? $config->language ?? '') == 'english' ? 'selected' : '' }}>English</option>
                                <option value="arabic" {{ (old('language') ?? $config->language ?? '') == 'arabic' ? 'selected' : '' }}>Arabic</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Core Instructions</label>
                            <textarea class="form-control" name="core_instructions" rows="3" placeholder="How your bot should behave">{{ old('core_instructions') ?? $config->core_instructions ?? '' }}</textarea>
                        </div>
                        <div class="form-header">
                            <h2 class="form-header__title">Voice Bot Configuration</h2>
                        </div>
                        <!-- Appearance -->
                        <div class="col-md-4">
                            <label class="form-label">Response Length</label>
                            <select class="form-select select2" name="response_length">
                                <option value="short" {{ (old('language') ?? $config->response_length ?? '') == 'short' ? 'selected' : '' }}>Short</option>
                                <option value="long" {{ (old('language') ?? $config->response_length ?? '') == 'long' ? 'selected' : '' }}>Long</option>
                            </select>
                        </div>
                        <!-- Appearance -->
                        <div class="col-md-4">
                            <label class="form-label">Response Tone</label>
                            <select class="form-select select2" name="response_tone">
                                <option value="formal" {{ (old('language') ?? $config->response_tone ?? '') == 'formal' ? 'selected' : '' }}>Formal</option>
                                <option value="supportive" {{ (old('language') ?? $config->response_tone ?? '') == 'supportive' ? 'selected' : '' }}>Supportive</option>
                            </select>
                        </div>
                        <!-- Appearance -->
                        <div class="col-md-4">
                            <label class="form-label">Translation Language</label>
                            <select class="form-select ">
                                <option value="" selected disabled>Select a language</option>
                                <option value="none" class="text-red-500">🚫 No Translation</option>
                                <option value="ar">🇸🇦 Arabic</option>
                                <option value="en">🇺🇸 English</option>
                                <option value="fr">🇫🇷 French</option>
                                <option value="es">🇪🇸 Spanish</option>
                                <option value="de">🇩🇪 German</option>
                                <option value="it">🇮🇹 Italian</option>
                                <option value="pt">🇵🇹 Portuguese</option>
                                <option value="ru">🇷🇺 Russian</option>
                                <option value="zh">🇨🇳 Chinese</option>
                                <option value="ja">🇯🇵 Japanese</option>
                            </select>
                        </div>
                       <div class="col-md-4">
                            <label class="form-label">Company Avatar (Widget Logo)</label>
                            <div class="avatar-upload">
                                {{--  <img src="{{ asset($config->company_avatar ?? '/assets/images/extra/P9aqUeKFvc.gif') }}" 
                                    id="company-avatar-preview"
                                    style="display:none; width: 60px; height: 60px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; display: block;">  --}}
                                <input type="file" name="company_avatar" id="company-avatar-input" accept="image/*" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bot Avatar (Chat Icon)</label>
                            <div class="avatar-upload">
                                {{--  <img src="{{ asset($config->bot_avatar ?? '/assets/images/extra/P9aqUeKFvc.gif') }}" 
                                    id="bot-avatar-preview"
                                    style="display:none; width: 60px; height: 60px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; display: block;">  --}}
                                <input type="file" name="bot_avatar" id="bot-avatar-input" accept="image/*" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Widget Color</label>
                            <div>
                                <input type="color" class="form-control p-1" name="widget_color" 
                                    value="{{ old('widget_color') ?? $config->widget_color ?? '#6c63ff' }}"
                                    style="height: 45px; width: 100%;">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Voice Selection</label>
                            <select id="default" class="form-select" name="voice">
                                <optgroup label="Jordanian">
                                    <option>Nada (Jordanian Female) <span class="default">Default</span></option>
                                    <option selected>Lana (Jordanian Female)</option>
                                    <option>Jasem (Jordanian Male) <span class="default">Default</span></option>
                                </optgroup>
                                
                                <optgroup label="Palestinian">
                                <option>Rema (Palestinian Female) <span class="default">Default</span></option>
                                <option>Amjad (Palestinian Male) <span class="default">Default</span></option>
                                </optgroup>
                                
                                <optgroup label="Iraqi">
                                <option>Lyali (Iraqi Female) <span class="default">Default</span></option>
                                </optgroup>
                                
                                <optgroup label="Emirati">
                                <option>Salma (Emirati Female) <span class="default">Default</span></option>
                                <option>Dima (Emirati Female)</option>
                                </optgroup>
                                
                                <optgroup label="Egyptian">
                                <option>Mariam (Egyptian Female) <span class="default">Default</span></option>
                                <option>Ahmed (Egyptian Male) <span class="default">Default</span></option>
                                <option>Samir (Egyptian Male)</option>
                                </optgroup>
                                
                                <optgroup label="Syrian">
                                <option>Dalal (Syrian Female) <span class="default">Default</span></option>
                                <option>Rami (Syrian Male) <span class="default">Default</span></option>
                                </optgroup>
                                
                                <optgroup label="Lebanese">
                                <option>Carla (Lebanese Female) <span class="default">Default</span></option>
                                </optgroup>
                                
                                <optgroup label="Saudi">
                                <option>Fahd (Saudi Male) <span class="default">Default</span></option>
                                <option>Jasem (Saudi Male)</option>
                                </optgroup>
                                
                                <optgroup label="American">
                                <option>Bella (American Female) <span class="default">Default</span></option>
                                <option>Rachel (American Female)</option>
                                <option>Domi (American Female)</option>
                                <option>Elli (American Female)</option>
                                <option>Freya (American Female)</option>
                                <option>Grace (American Female)</option>
                                <option>Lily (American Female)</option>
                                <option>Adam (American Male) <span class="default">Default</span></option>
                                <option>Antoni (American Male)</option>
                                <option>Arnold (American Male)</option>
                                <option>Sam (American Male)</option>
                                <option>Ethan (American Male)</option>
                                <option>Josh (American Male)</option>
                                <option>Brian (American Male)</option>
                                <option>Daniel (American Male)</option>
                                </optgroup>
                                
                                <optgroup label="British">
                                <option>Dorothy (British Female) <span class="default">Default</span></option>
                                <option>Charlotte (British Female)</option>
                                <option>Alice (British Female)</option>
                                <option>Matilda (British Female)</option>
                                <option>Harry (British Male) <span class="default">Default</span></option>
                                <option>Liam (British Male)</option>
                                <option>Charlie (British Male)</option>
                                <option>George (British Male)</option>
                                </optgroup>
                                
                                <optgroup label="Australian">
                                <option>Nicole (Australian Female) <span class="default">Default</span></option>
                                <option>Emily (Australian Female)</option>
                                <option>Ryan (Australian Male) <span class="default">Default</span></option>
                                <option>Callum (Australian Male)</option>
                                </optgroup>
                            </select>
                        </div>
                        
                       <!-- Widget Position -->
                        <div class="col-12">
                            <label class="form-label">Widget Position</label>
                            <div class="position-selector">
                                <!-- Left Position -->
                                <label class="position-option {{ (old('widget_position') ?? $config->widget_position ?? 'left') == 'left' ? 'active' : '' }}">
                                    <input type="radio" name="widget_position" value="left" {{ (old('widget_position') ?? $config->widget_position ?? 'left') == 'left' ? 'checked' : '' }} class="visually-hidden">
                                    <div class="position-visualization">
                                        <div class="widget-marker" style="left: 10%;"></div>
                                        <div class="browser-mock"></div>
                                    </div>
                                    <small>Left</small>
                                </label>
                                
                                <!-- Center Position -->
                                <label class="position-option {{ (old('widget_position') ?? $config->widget_position ?? '') == 'center' ? 'active' : '' }}">
                                    <input type="radio" name="widget_position" value="center" {{ (old('widget_position') ?? $config->widget_position ?? '') == 'center' ? 'checked' : '' }} class="visually-hidden">
                                    <div class="position-visualization">
                                        <div class="widget-marker" style="left: 50%; transform: translateX(-50%);"></div>
                                        <div class="browser-mock"></div>
                                    </div>
                                    <small>Center</small>
                                </label>
                                
                                <!-- Right Position -->
                                <label class="position-option {{ (old('widget_position') ?? $config->widget_position ?? '') == 'right' ? 'active' : '' }}">
                                    <input type="radio" name="widget_position" value="right" {{ (old('widget_position') ?? $config->widget_position ?? '') == 'right' ? 'checked' : '' }} class="visually-hidden">
                                    <div class="position-visualization">
                                        <div class="widget-marker" style="right: 10%;"></div>
                                        <div class="browser-mock"></div>
                                    </div>
                                    <small>Right</small>
                                </label>
                            </div>
                        </div>
                        <div class="form-header">
                            <h2 class="form-header__title">Knowledge Base</h2>
                        </div>
                        <div class="col-12 kb-wrapper">
                            <!-- Tabs with unique class names -->
                            <ul class="nav kb-tabs mb-4" id="knowledge-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link kb-tab-link active" id="documents-tab" data-bs-toggle="tab" href="#documents" role="tab">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        <span>Documents</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link kb-tab-link" id="faq-tab" data-bs-toggle="tab" href="#faq" role="tab">
                                        <i class="fas fa-question-circle me-2"></i>
                                        <span>FAQs</span>
                                        @if(isset($config->faqs) && count($config->faqs) > 0)
                                        <span class="kb-badge bg-primary ms-2">{{ count($config->faqs) }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link kb-tab-link" id="links-tab" data-bs-toggle="tab" href="#links" role="tab">
                                        <i class="fas fa-link me-2"></i>
                                        <span>Links</span>
                                        @if(isset($config->links) && count($config->links) > 0)
                                        <span class="kb-badge bg-primary ms-2">{{ count($config->links) }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content kb-tab-content" id="knowledge-tabs-content">
                                <!-- Documents Tab -->
                                <div class="tab-pane fade show active kb-tab-pane" id="documents" role="tabpanel">
                                    <div class="kb-card">
                                        <div class="kb-card-body">
                                            <div class="kb-upload-area">
                                                <label class="kb-label">Upload PDF Documents</label>
                                                <div class="kb-input-group">
                                                    <input type="file" name="knowledge_documents[]" class="kb-form-control" accept=".pdf" multiple>
                                                    <button class="kb-upload-btn" type="button">
                                                        <i class="fas fa-upload me-1"></i> Upload
                                                    </button>
                                                </div>
                                                <small class="kb-hint">Max file size: 5MB</small>
                                            </div>
                                            @if(isset($config->knowledge_documents) && count($config->knowledge_documents) > 0)
                                                <div class="kb-upload-list">
                                                    <h6 class="kb-list-title">Uploaded Documents</h6>
                                                    <div class="kb-doc-list">
                                                        @foreach($config->knowledge_documents as $doc)
                                                        <div class="kb-doc-item">
                                                            <div class="kb-doc-icon">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </div>
                                                            <span class="kb-doc-name">{{ basename($doc) }}</span>
                                                            <button type="button" class="kb-delete-btn">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Tab -->
                                <div class="tab-pane fade kb-tab-pane" id="faq" role="tabpanel">
                                    <div class="kb-card">
                                        <div class="kb-card-body">
                                            <div id="kb-faq-container">
                                                @if(isset($config->faqs) && count($config->faqs) > 0)
                                                    @foreach($config->faqs as $index => $faq)
                                                    <div class="kb-faq-item">
                                                        <div class="kb-faq-header">
                                                            <span class="kb-faq-number">#{{ $index + 1 }}</span>
                                                            <button type="button" class="kb-remove-btn">
                                                                <i class="fas fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                        <div class="kb-faq-fields">
                                                            <div class="kb-faq-question">
                                                                <div class="kb-floating-label">
                                                                    <input type="text" name="faqs[{{ $index }}][question]" 
                                                                        class="kb-form-control" id="kb-question-{{ $index }}" 
                                                                        placeholder=" " value="{{ $faq['question'] }}" required>
                                                                    <label for="kb-question-{{ $index }}">Question</label>
                                                                </div>
                                                            </div>
                                                            <div class="kb-faq-answer">
                                                                <div class="kb-floating-label">
                                                                    <input type="text" name="faqs[{{ $index }}][answer]" 
                                                                        class="kb-form-control" id="kb-answer-{{ $index }}" 
                                                                        placeholder=" " value="{{ $faq['answer'] }}" required>
                                                                    <label for="kb-answer-{{ $index }}">Answer</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    <div class="kb-faq-item">
                                                        <div class="kb-faq-header">
                                                            <span class="kb-faq-number">#1</span>
                                                            <button type="button" class="kb-remove-btn" disabled>
                                                                <i class="fas fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                        <div class="kb-faq-fields">
                                                            <div class="kb-faq-question">
                                                                <div class="kb-floating-label">
                                                                    <input type="text" name="faqs[0][question]" 
                                                                        class="kb-form-control" id="kb-question-0" 
                                                                        placeholder=" " required>
                                                                    <label for="kb-question-0">Question</label>
                                                                </div>
                                                            </div>
                                                            <div class="kb-faq-answer">
                                                                <div class="kb-floating-label">
                                                                    <input type="text" name="faqs[0][answer]" 
                                                                        class="kb-form-control" id="kb-answer-0" 
                                                                        placeholder=" " required>
                                                                    <label for="kb-answer-0">Answer</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="kb-add-btn" id="kb-add-faq" @if(!isset($config->faqs)) disabled @endif>
                                                <i class="fas fa-plus-circle me-2"></i> Add New FAQ
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Links Tab -->
                                <div class="tab-pane fade kb-tab-pane" id="links" role="tabpanel">
                                    <div class="kb-card">
                                        <div class="kb-card-body">
                                            <div id="kb-links-container">
                                                @if(isset($config->links) && count($config->links) > 0)
                                                    @foreach($config->links as $index => $link)
                                                    <div class="kb-link-item mb-3">
                                                        <div class="row g-2">
                                                            <div class="col-md-8">
                                                                <div class="kb-floating-label">
                                                                    <input type="url" name="links[{{ $index }}]" 
                                                                        class="kb-form-control" id="kb-link-{{ $index }}" 
                                                                        placeholder=" " value="{{ $link }}" required>
                                                                    <label for="kb-link-{{ $index }}">Link URL</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 d-flex align-items-end">
                                                                <button type="button" class="kb-remove-btn">
                                                                    <i class="fas fa-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    <div class="kb-link-item mb-3">
                                                        <div class="row g-2">
                                                            <div class="col-md-9">
                                                                <div class="kb-floating-label">
                                                                    <input type="url" name="links[0]" 
                                                                        class="kb-form-control" id="kb-link-0" 
                                                                        placeholder=" " required>
                                                                    <label for="kb-link-0">Link URL</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center mt-0">
                                                                <button type="button" class="kb-remove-btn" disabled>
                                                                    <i class="fas fa-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="kb-add-btn mt-3" id="kb-add-link" @if(!isset($config->links)) disabled @endif>
                                                <i class="fas fa-plus-circle me-2"></i> Add New Link
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!-- Widget Preview Section -->
        <div class="col-md-4 mt-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">Widget Preview</h5>
                    <div class="widget-preview-container">
                        <!-- Chat Widget Mockup -->
                        <div class="rounded shadow-sm" style="width: 100%; max-width: 320px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden;">
                            <!-- Chat Header -->
                            <div class="p-3 d-flex align-items-center" id="widget-header-preview" style="background-color: {{ old('widget_color') ?? $config->widget_color ?? '#6c63ff' }}; color: white;">
                                <img src="{{ asset($config->company_avatar ?? '/assets/images/extra/P9aqUeKFvc.gif') }}" 
                                    id="widget-avatar-preview"
                                    style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                                <div>
                                    <h6 class="mb-0" id="bot-name-preview">{{ $config->bot_name ?? 'Support Bot' }}</h6>
                                    <small class="d-flex align-items-center">
                                        <span class="badge bg-white text-success me-2">Online</span>
                                        <span>Typing</span>
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Chat Messages -->
                            <div class="p-3" style="height: 250px; overflow-y: auto; background-color: #f8f9fa;">
                                <div class="mb-3 text-end">
                                    <div class="d-inline-block p-2 rounded" style="background-color: #e3f2fd; max-width: 80%;">
                                        Hello, I need help
                                        <div class="text-muted text-start small mt-1">Just now</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex">
                                        <img src="{{ asset($config->bot_avatar ?? '/assets/images/extra/P9aqUeKFvc.gif') }}" 
                                            id="chat-avatar-preview"
                                            style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%; margin-right: 8px;">
                                        <div class="p-2 rounded" style="background-color: white; max-width: 80%;">
                                            Hi there! How can I assist you today?
                                            <div class="text-muted small mt-1">Just now</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Message Input -->
                            <div class="p-2 border-top" style="background-color: white; height: 60px;">
                                <div class="input-group h-100">
                                    <button class="btn btn-sm btn-link text-muted align-self-center" style="font-size: 1.5rem;">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                    <input type="text" class="form-control border-0 align-self-center" placeholder="Type your message..." readonly>
                                </div>
                            </div>
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
    // Widget position selector
    document.querySelectorAll('.position-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active class from all options
            document.querySelectorAll('.position-option').forEach(option => {
                option.classList.remove('active');
            });
            
            // Add active class to selected option's parent label
            if (this.checked) {
                this.closest('.position-option').classList.add('active');
            }
        });
    });
</script>

<script>
    // Update preview when company avatar changes
    document.getElementById('company-avatar-input').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const src = URL.createObjectURL(e.target.files[0]);
            // document.getElementById('company-avatar-preview').src = src;
            document.getElementById('widget-avatar-preview').src = src;
        }
    });

    // Update preview when bot avatar changes
    document.getElementById('bot-avatar-input').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const src = URL.createObjectURL(e.target.files[0]);
           // document.getElementById('bot-avatar-preview').src = src;
            document.getElementById('chat-avatar-preview').src = src;
        }
    });

    // Update preview when widget color changes
    document.querySelector('input[name="widget_color"]').addEventListener('input', function() {
        document.getElementById('widget-header-preview').style.backgroundColor = this.value;
    });

    // Update preview when bot name changes
    document.querySelector('input[name="bot_name"]').addEventListener('input', function() {
        document.getElementById('bot-name-preview').textContent = this.value || 'Support Bot';
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqContainer = document.getElementById('kb-faq-container');
    const addFaqBtn = document.getElementById('kb-add-faq');
    
    // Check if first FAQ is filled to enable add button
    function checkFirstFAQ() {
        if (faqContainer.children.length === 1) {
            const firstItem = faqContainer.firstElementChild;
            const question = firstItem.querySelector('input[name*="question"]');
            const answer = firstItem.querySelector('input[name*="answer"]');
            
            addFaqBtn.disabled = !(question.value.trim() && answer.value.trim());
        }
    }
    
    // Add event listeners to first FAQ inputs
    if (faqContainer.children.length === 1) {
        const inputs = faqContainer.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', checkFirstFAQ);
        });
    }
    
    // Add FAQ item
    addFaqBtn.addEventListener('click', function() {
        const index = faqContainer.children.length;
        
        const newItem = document.createElement('div');
        newItem.className = 'kb-faq-item';
        newItem.innerHTML = `
            <div class="kb-faq-header">
                <span class="kb-faq-number">#${index + 1}</span>
                <button type="button" class="kb-remove-btn">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            <div class="kb-faq-fields">
                <div class="kb-faq-question">
                    <div class="kb-floating-label">
                        <input type="text" name="faqs[${index}][question]" 
                               class="kb-form-control" id="kb-question-${index}" 
                               placeholder=" " required>
                        <label for="kb-question-${index}">Question</label>
                    </div>
                </div>
                <div class="kb-faq-answer">
                    <div class="kb-floating-label">
                        <input type="text" name="faqs[${index}][answer]" 
                               class="kb-form-control" id="kb-answer-${index}" 
                               placeholder=" " required>
                        <label for="kb-answer-${index}">Answer</label>
                    </div>
                </div>
            </div>
        `;
        
        faqContainer.appendChild(newItem);
        updateFAQNumbering();
    });
    
    // Remove FAQ item
    faqContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('kb-remove-btn') || e.target.closest('.kb-remove-btn')) {
            const btn = e.target.classList.contains('kb-remove-btn') ? e.target : e.target.closest('.kb-remove-btn');
            if (!btn.disabled) {
                btn.closest('.kb-faq-item').remove();
                updateFAQNumbering();
                
                // If no FAQs left, disable add button
                if (faqContainer.children.length === 0) {
                    addFaqBtn.disabled = true;
                }
            }
        }
    });
    
    // Update FAQ numbering
    function updateFAQNumbering() {
        const items = faqContainer.querySelectorAll('.kb-faq-item');
        items.forEach((item, index) => {
            item.querySelector('.kb-faq-number').textContent = `#${index + 1}`;
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Links functionality
    const linksContainer = document.getElementById('kb-links-container');
    const addLinkBtn = document.getElementById('kb-add-link');
    
    // Check if first link is filled to enable add button
    function checkFirstLink() {
        if (linksContainer.children.length === 1) {
            const firstItem = linksContainer.firstElementChild;
            const linkInput = firstItem.querySelector('input[type="url"]');
            addLinkBtn.disabled = !linkInput.value.trim();
        }
    }
    
    // Add event listeners to first link input
    if (linksContainer.children.length === 1) {
        const input = linksContainer.querySelector('input[type="url"]');
        input.addEventListener('input', checkFirstLink);
    }
    
    // Add new link item
    addLinkBtn.addEventListener('click', function() {
        const index = linksContainer.children.length;
        
        const newItem = document.createElement('div');
        newItem.className = 'kb-link-item mb-3';
        newItem.innerHTML = `
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="kb-floating-label">
                        <input type="url" name="links[${index}]" 
                               class="kb-form-control" id="kb-link-${index}" 
                               placeholder=" " required>
                        <label for="kb-link-${index}">Link URL</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="kb-remove-btn">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        
        linksContainer.appendChild(newItem);
    });
    
    // Remove link item
    linksContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('kb-remove-btn') || e.target.closest('.kb-remove-btn')) {
            const btn = e.target.classList.contains('kb-remove-btn') ? e.target : e.target.closest('.kb-remove-btn');
            if (!btn.disabled) {
                btn.closest('.kb-link-item').remove();
            }
        }
    });
});
</script>
@endpush