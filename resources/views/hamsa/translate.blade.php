@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Audio/Video Transcription</h1>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Transcription Form --}}
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form action="{{ url('/hamsa/translate') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Media URL Input --}}
                <div class="mb-4">
                    <label for="mediaUrl" class="block text-gray-700 font-medium mb-2">
                        Media URL <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="url" 
                        name="mediaUrl" 
                        id="mediaUrl" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mediaUrl') border-red-500 @enderror"
                        value="{{ old('mediaUrl') }}"
                        placeholder="https://example.com/audio.mp3"
                        required
                    >
                    @error('mediaUrl')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Enter the URL of the audio or video file to transcribe</p>
                </div>

                {{-- Title Input --}}
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">
                        Title (Optional)
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        value="{{ old('title') }}"
                        placeholder="Enter a title for this transcription"
                    >
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Language Selection --}}
                <div class="mb-4">
                    <label for="language" class="block text-gray-700 font-medium mb-2">
                        Language <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="language" 
                        id="language" 
                        class="form-control"
                        required
                    >
                        <option value="">Select Language</option>
                        <option value="ar" {{ old('language') == 'ar' ? 'selected' : '' }}>Arabic (ar)</option>
                        <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English (en)</option>
                    </select>
                    @error('language')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model Selection --}}
                <div class="mb-4">
                    <label for="model" class="block text-gray-700 font-medium mb-2">
                        Model <span class="text-red-500">*</span>
                    </label>
                    <select name="model"  id="model" class="form-control" required >
                        <option value="Hamsa-General-V2.0" {{ old('model', 'Hamsa-General-V2.0') == 'Hamsa-General-V2.0' ? 'selected' : '' }}>CRTV AI V2.0</option>
                        <option value="Hamsa-General-V2.0" {{ old('model', 'Hamsa-General-V1.0') == 'Hamsa-General-V2.0' ? 'selected' : '' }}>CRTV AI V1.0</option>
                    </select>
                    @error('model')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <input type="hidden"  name="processingType"  value="async">
                </div>
                {{-- SRT Format Options --}}
                <div class="mb-4">
                    <div class="flex items-center mb-2">
                        <input 
                            type="checkbox" 
                            name="returnSrtFormat" 
                            id="returnSrtFormat" 
                            value="1"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            {{ old('returnSrtFormat') ? 'checked' : '' }}
                        >
                        <label for="returnSrtFormat" class="ml-2 text-gray-700 font-medium">
                            Return SRT Format
                        </label>
                    </div>
                </div>

                {{-- Advanced SRT Options (collapsible) --}}
                <div class="mb-4">
                    <button 
                        type="button" 
                        class="text-blue-600 hover:text-blue-800 font-medium mb-2"
                        onclick="document.getElementById('srtOptions').classList.toggle('hidden')"
                    >
                        ▼ Advanced SRT Options
                    </button>
                    
                    <div id="srtOptions" class="hidden border border-gray-200 rounded-lg p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="maxLinesPerSubtitle" class="block text-gray-700 text-sm mb-1">Max Lines Per Subtitle</label>
                                <input 
                                    type="number" 
                                    name="srtOptions[maxLinesPerSubtitle]" 
                                    id="maxLinesPerSubtitle" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded"
                                    value="{{ old('srtOptions.maxLinesPerSubtitle', 2) }}"
                                    min="1"
                                >
                            </div>

                            <div>
                                <label for="maxCharsPerLine" class="block text-gray-700 text-sm mb-1">Max Chars Per Line</label>
                                <input 
                                    type="number" 
                                    name="srtOptions[maxCharsPerLine]" 
                                    id="maxCharsPerLine" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded"
                                    value="{{ old('srtOptions.maxCharsPerLine', 42) }}"
                                    min="1"
                                >
                            </div>

                            <div>
                                <label for="maxMergeableGap" class="block text-gray-700 text-sm mb-1">Max Mergeable Gap (s)</label>
                                <input 
                                    type="number" 
                                    name="srtOptions[maxMergeableGap]" 
                                    id="maxMergeableGap" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded"
                                    value="{{ old('srtOptions.maxMergeableGap', 0.3) }}"
                                    step="0.1"
                                    min="0"
                                >
                            </div>

                            <div>
                                <label for="minDuration" class="block text-gray-700 text-sm mb-1">Min Duration (s)</label>
                                <input 
                                    type="number" 
                                    name="srtOptions[minDuration]" 
                                    id="minDuration" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded"
                                    value="{{ old('srtOptions.minDuration', 0.7) }}"
                                    step="0.1"
                                    min="0"
                                >
                            </div>

                            <div>
                                <label for="maxDuration" class="block text-gray-700 text-sm mb-1">Max Duration (s)</label>
                                <input 
                                    type="number" 
                                    name="srtOptions[maxDuration]" 
                                    id="maxDuration" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded"
                                    value="{{ old('srtOptions.maxDuration', 7) }}"
                                    step="0.1"
                                    min="0"
                                >
                            </div>

                            <div>
                                <label for="minGap" class="block text-gray-700 text-sm mb-1">Min Gap (s)</label>
                                <input 
                                    type="number" 
                                    name="srtOptions[minGap]" 
                                    id="minGap" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded"
                                    value="{{ old('srtOptions.minGap', 0.04) }}"
                                    step="0.01"
                                    min="0"
                                >
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="srtOptions[singleSpeakerPerSubtitle]" 
                                id="singleSpeakerPerSubtitle" 
                                value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                                {{ old('srtOptions.singleSpeakerPerSubtitle', true) ? 'checked' : '' }}
                            >
                            <label for="singleSpeakerPerSubtitle" class="ml-2 text-gray-700 text-sm">
                                Single Speaker Per Subtitle
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition duration-200"
                    >
                        Submit Transcription
                    </button>
                </div>
            </form>
        </div>

        {{-- Result Display --}}
        @if(session('result_data'))
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Transcription Result</h2>
                
                <div class="space-y-4">
                    @if(isset(session('result_data')['jobId']))
                        <div>
                            <span class="font-medium">Job ID:</span>
                            <span class="text-gray-700">{{ session('result_data')['jobId'] }}</span>
                        </div>
                    @endif

                    @if(isset(session('result_data')['status']))
                        <div>
                            <span class="font-medium">Status:</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded">
                                {{ session('result_data')['status'] }}
                            </span>
                        </div>
                    @endif

                    @if(isset(session('result_data')['transcription']))
                        <div>
                            <span class="font-medium block mb-2">Transcription:</span>
                            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                                <pre class="whitespace-pre-wrap">{{ session('result_data')['transcription'] }}</pre>
                            </div>
                        </div>
                    @endif

                    @if(isset(session('result_data')['srtFormat']))
                        <div>
                            <span class="font-medium block mb-2">SRT Format:</span>
                            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                                <pre class="whitespace-pre-wrap text-sm">{{ session('result_data')['srtFormat'] }}</pre>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <button 
                            onclick="navigator.clipboard.writeText(document.querySelector('pre').textContent)"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition duration-200"
                        >
                            Copy to Clipboard
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection