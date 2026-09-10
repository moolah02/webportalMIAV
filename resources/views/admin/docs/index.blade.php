@extends('layouts.app')
@section('title', 'Documentation Manager')

@section('header-actions')
<a href="{{ url('/docs') }}" target="_blank" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-external"/></svg> View Live Docs</a>
@endsection

@push('styles')
<style>
    .dm-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .dm-card .ui-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .dm-card .ui-table th { text-align: left; white-space: nowrap; }
    .dm-title { color: var(--mv-ink); font-weight: 500; }
    .dm-sub { font-size: 12.5px; color: var(--mv-muted); margin-top: 1px; }
    .dm-slug { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 6px; }
    .dm-muted { color: var(--mv-muted); white-space: nowrap; font-variant-numeric: tabular-nums; }
    .dm-actions { display: inline-flex; gap: 6px; }
    .dm-empty { padding: 44px 16px; text-align: center; font-size: 13.5px; color: var(--mv-muted); line-height: 1.6; }
    .dm-empty .mv-i { width: 28px; height: 28px; color: var(--mv-line-strong); display: block; margin: 0 auto 8px; }
    .dm-empty code { font-family: var(--mv-mono); font-size: 12px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 5px; padding: 1px 6px; color: var(--mv-ink-2); }
</style>
@endpush

@section('content')
<div class="dm-card">
    <div style="overflow-x:auto;">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Slug</th>
                    <th>Last Edited</th>
                    <th>Edited By</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>
                        <div class="dm-title">{{ $page->title }}</div>
                        @if($page->subtitle)
                            <div class="dm-sub">{{ Str::limit($page->subtitle, 80) }}</div>
                        @endif
                    </td>
                    <td><span class="dm-slug">{{ $page->slug }}</span></td>
                    <td class="dm-muted">{{ $page->updated_at ? $page->updated_at->format('d M Y, H:i') : '—' }}</td>
                    <td class="dm-muted">{{ $page->editor?->name ?? '—' }}</td>
                    <td style="text-align:right;">
                        <div class="dm-actions">
                            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank" class="btn-secondary btn-sm">View</a>
                            <a href="{{ route('admin.docs.edit', $page->slug) }}" class="btn-primary btn-sm">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="dm-empty">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-book"/></svg>
                            No documentation pages found.<br>
                            Run <code>php artisan db:seed --class=DocPageSeeder</code> to populate them.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
