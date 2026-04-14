{{-- resources/views/admin/docs/edit.blade.php --}}
@extends('layouts.app')

@push('styles')
{{-- Summernote CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
<style>
    .note-editor.note-frame { border-radius: 0 0 0.5rem 0.5rem; }
    .note-toolbar { border-radius: 0.5rem 0.5rem 0 0; background: #f9fafb; }
    .note-editable { min-height: 500px; font-size: 15px; line-height: 1.7; }
    .note-statusbar { border-radius: 0 0 0.5rem 0.5rem; }
</style>
@endpush

@section('content')
<div>
    {{-- Breadcrumb / header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <div class="text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.docs.index') }}" class="text-[#1a3a5c] hover:underline">Documentation Manager</a>
                <span class="mx-1.5">›</span>
                <span>Editing: {{ $page->title }}</span>
            </div>
            <h1 class="m-0 text-gray-900 text-2xl font-semibold">{{ $page->title }}</h1>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank"
               class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg no-underline font-medium text-sm hover:bg-gray-200 transition-colors">
                👁 View Live
            </a>
            <a href="{{ route('admin.docs.index') }}"
               class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg no-underline font-medium text-sm hover:bg-gray-200 transition-colors">
                ← Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-600 px-4 py-3.5 rounded-lg mb-5 text-sm">
            @foreach($errors->all() as $error) <div>⚠️ {{ $error }}</div> @endforeach
        </div>
    @endif

    <form action="{{ route('admin.docs.update', $page->slug) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Title + Subtitle row --}}
        <div class="grid grid-cols-[1fr_2fr] gap-4 mb-5">
            <div>
                <label class="block font-semibold text-gray-700 mb-1.5 text-sm">Page Title</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-[15px] outline-none focus:ring-2 focus:ring-[#1a3a5c]/30 focus:border-[#1a3a5c] transition"
                       required>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1.5 text-sm">Subtitle / Description</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $page->subtitle) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-[15px] outline-none focus:ring-2 focus:ring-[#1a3a5c]/30 focus:border-[#1a3a5c] transition">
            </div>
        </div>

        {{-- Content WYSIWYG --}}
        <div class="mb-6">
            <label class="block font-semibold text-gray-700 mb-2 text-sm">Page Content</label>
            <textarea id="summernote-editor" name="content">{{ old('content', $page->content) }}</textarea>
        </div>

        {{-- Last edit info --}}
        @if($page->updated_at && $page->editor)
            <p class="text-gray-400 text-xs mb-4">
                Last saved {{ $page->updated_at->diffForHumans() }} by {{ $page->editor->name }}
            </p>
        @endif

        {{-- Save buttons --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.docs.index') }}"
               class="bg-gray-100 text-gray-700 px-7 py-3 rounded-lg no-underline font-medium text-[15px] hover:bg-gray-200 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="bg-[#1a3a5c] text-white px-8 py-3 border-0 rounded-lg font-semibold text-[15px] cursor-pointer hover:bg-[#152e4a] transition-colors">
                💾 Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- jQuery (already in layout, but safe) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Summernote JS --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function () {
    $('#summernote-editor').summernote({
        height: 520,
        toolbar: [
            ['style',   ['style']],
            ['font',    ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['color',   ['color']],
            ['para',    ['ul', 'ol', 'paragraph']],
            ['table',   ['table']],
            ['insert',  ['link', 'hr']],
            ['view',    ['fullscreen', 'codeview', 'help']],
        ],
        styleTags: ['p', 'h2', 'h3', 'h4', 'blockquote', 'pre'],
        callbacks: {
            // Prevent Summernote from stripping certain HTML attributes
            onInit: function () {
                $('.note-editable').css('font-family', 'inherit');
            }
        }
    });
});
</script>
@endpush
