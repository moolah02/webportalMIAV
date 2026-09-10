{{-- resources/views/admin/docs/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit: ' . $page->title)

@section('header-actions')
<a href="{{ route('admin.docs.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back</a>
<a href="{{ url('/docs/' . $page->slug) }}" target="_blank" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-external"/></svg> View Live</a>
@endsection

@push('styles')
{{-- Summernote CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
<style>
    .de { display: grid; gap: 16px; }
    .de-crumb { font-size: 13px; color: var(--mv-muted); }
    .de-crumb a { color: var(--mv-accent-ink); text-decoration: none; }
    .de-crumb a:hover { text-decoration: underline; }
    .de-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 18px 20px; display: grid; gap: 16px; }
    .de-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 2fr); gap: 16px; }
    .de .ui-label { display: block; margin-bottom: 5px; }
    .de .ui-input { width: 100%; height: 38px; padding: 0 12px; font-size: 14px; }
    .de-meta { font-size: 12.5px; color: var(--mv-muted); margin: 0; }
    .de-foot { display: flex; justify-content: flex-end; align-items: center; gap: 8px; }
    .de-foot .de-meta { margin-right: auto; }
    .de .note-editor.note-frame { border: 1px solid var(--mv-line-strong); border-radius: 8px; box-shadow: none; }
    .de .note-toolbar { border-radius: 8px 8px 0 0; background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); }
    .de .note-editable { min-height: 500px; font-size: 15px; line-height: 1.7; color: var(--mv-ink); }
    .de .note-statusbar { border-radius: 0 0 8px 8px; }
    @media (max-width: 800px) { .de-row { grid-template-columns: minmax(0, 1fr); } }
</style>
@endpush

@section('content')
<div class="de">
    <div class="de-crumb">
        <a href="{{ route('admin.docs.index') }}">Documentation Manager</a>
        <span aria-hidden="true"> / </span>
        <span>Editing: {{ $page->title }}</span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="padding:11px 14px;border:1px solid;font-size:13.5px;">
            @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
    @endif

    <form action="{{ route('admin.docs.update', $page->slug) }}" method="POST" class="de-card">
        @csrf
        @method('PUT')

        {{-- Title + Subtitle row --}}
        <div class="de-row">
            <div>
                <label class="ui-label">Page Title</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" class="ui-input" required>
            </div>
            <div>
                <label class="ui-label">Subtitle / Description</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $page->subtitle) }}" class="ui-input">
            </div>
        </div>

        {{-- Content WYSIWYG --}}
        <div>
            <label class="ui-label">Page Content</label>
            <textarea id="summernote-editor" name="content">{{ old('content', $page->content) }}</textarea>
        </div>

        {{-- Save buttons --}}
        <div class="de-foot">
            @if($page->updated_at && $page->editor)
                <p class="de-meta">Last saved {{ $page->updated_at->diffForHumans() }} by {{ $page->editor->name }}</p>
            @endif
            <a href="{{ route('admin.docs.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Save Changes</button>
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
