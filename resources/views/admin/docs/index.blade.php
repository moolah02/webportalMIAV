@extends('layouts.app')
@section('title', 'Documentation Manager')

@section('header-actions')
<a href="{{ url('/docs') }}" target="_blank" class="btn-secondary">🔗 View Live Docs</a>
@endsection

@section('content')

@if(session('success'))
<div class="flash-success"><span>✓</span> {{ session('success') }}</div>
@endif

<div class="ui-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Slug</th>
                    <th>Last Edited</th>
                    <th>Edited By</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>
                        <div class="font-semibold text-gray-900">{{ $page->title }}</div>
                        @if($page->subtitle)
                            <div class="text-gray-500 text-xs mt-0.5">{{ Str::limit($page->subtitle, 80) }}</div>
                        @endif
                    </td>
                    <td>
                        <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-700">{{ $page->slug }}</code>
                    </td>
                    <td class="text-gray-500 text-sm">
                        {{ $page->updated_at ? $page->updated_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td class="text-gray-500 text-sm">
                        {{ $page->editor?->name ?? '—' }}
                    </td>
                    <td class="text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank" class="btn-secondary btn-sm">👁 View</a>
                            <a href="{{ route('admin.docs.edit', $page->slug) }}" class="btn-primary btn-sm">✏️ Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">📄</div>
                            <div class="empty-state-msg">
                                No documentation pages found.<br>
                                Run <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">php artisan db:seed --class=DocPageSeeder</code> to populate them.
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
