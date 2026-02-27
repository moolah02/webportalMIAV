{{-- resources/views/admin/docs/edit.blade.php --}}
@extends('layouts.app')

@push('styles')
{{-- Summernote CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
<style>
    .note-editor.note-frame { border-radius: 0 0 8px 8px; }
    .note-toolbar { border-radius: 8px 8px 0 0; background: #f9fafb; }
    .note-editable { min-height: 500px; font-size: 15px; line-height: 1.7; }
    .note-statusbar { border-radius: 0 0 8px 8px; }
</style>
@endpush

@section('content')
<div>
    {{-- Breadcrumb / header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #e5e7eb;">
        <div>
            <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">
                <a href="{{ route('admin.docs.index') }}" style="color:#1a3a5c;text-decoration:none;">Documentation Manager</a>
                <span style="margin:0 6px;">›</span>
                <span>Editing: {{ $page->title }}</span>
            </div>
            <h1 style="margin:0;color:#111827;font-size:26px;font-weight:600;">{{ $page->title }}</h1>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank"
               style="background:#f3f4f6;color:#374151;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;font-size:14px;">
                👁 View Live
            </a>
            <a href="{{ route('admin.docs.index') }}"
               style="background:#f3f4f6;color:#374151;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;font-size:14px;">
                ← Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;padding:14px 18px;border-radius:8px;margin-bottom:20px;font-size:14px;">
            @foreach($errors->all() as $error) <div>⚠️ {{ $error }}</div> @endforeach
        </div>
    @endif

    <form action="{{ route('admin.docs.update', $page->slug) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Title + Subtitle row --}}
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;margin-bottom:20px;">
            <div>
                <label style="display:block;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px;">Page Title</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}"
                       style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;outline:none;"
                       required>
            </div>
            <div>
                <label style="display:block;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px;">Subtitle / Description</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $page->subtitle) }}"
                       style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;outline:none;">
            </div>
        </div>

        {{-- Content WYSIWYG --}}
        <div style="margin-bottom:24px;">
            <label style="display:block;font-weight:600;color:#374151;margin-bottom:8px;font-size:14px;">Page Content</label>
            <textarea id="summernote-editor" name="content">{{ old('content', $page->content) }}</textarea>
        </div>

        {{-- Last edit info --}}
        @if($page->updated_at && $page->editor)
            <p style="color:#9ca3af;font-size:13px;margin-bottom:16px;">
                Last saved {{ $page->updated_at->diffForHumans() }} by {{ $page->editor->name }}
            </p>
        @endif

        {{-- Save buttons --}}
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="{{ route('admin.docs.index') }}"
               style="background:#f3f4f6;color:#374151;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:500;font-size:15px;">
                Cancel
            </a>
            <button type="submit"
                    style="background:#1a3a5c;color:#fff;padding:12px 32px;border:none;border-radius:8px;font-weight:600;font-size:15px;cursor:pointer;">
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
