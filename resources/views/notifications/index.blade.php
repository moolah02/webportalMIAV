@extends('layouts.app')
@section('title', 'Notifications')

@push('styles')
<style>
    .nt { display: grid; gap: 16px; max-width: 960px; }
    .nt .mv-i { width: 16px; height: 16px; }
    .nt-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .nt-toolbar p { margin: 0; font-size: 13px; color: var(--mv-muted); }
    .nt-toolbar button { display: inline-flex; align-items: center; gap: 6px; }

    .nt-list { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
    .nt-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 18px; }
    .nt-item + .nt-item { border-top: 1px solid var(--mv-line); }
    .nt-item.is-unread { background: #FBFCFE; }
    .nt-ic { width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; flex-shrink: 0; background: var(--mv-surface-2); color: var(--mv-ink-2); }
    .nt-ic.t-ticket { background: var(--mv-warn-soft); color: var(--mv-warn); }
    .nt-ic.t-job { background: var(--mv-good-soft); color: var(--mv-good); }
    .nt-ic.t-asset, .nt-ic.t-visit { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
    .nt-body { flex: 1; min-width: 0; }
    .nt-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
    .nt-title { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); display: inline-flex; align-items: center; gap: 8px; }
    .nt-item.is-unread .nt-title { font-weight: 600; }
    .nt-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mv-accent); flex-shrink: 0; }
    .nt-time { font-size: 12px; color: var(--mv-muted); white-space: nowrap; }
    .nt-text { margin: 2px 0 0; font-size: 13px; color: var(--mv-ink-2); line-height: 1.5; }
    .nt-meta { display: flex; align-items: center; gap: 14px; margin-top: 8px; }
    .nt-meta a { display: inline-flex; align-items: center; gap: 4px; font-size: 12.5px; font-weight: 500; color: var(--mv-accent-ink); text-decoration: none; }
    .nt-meta a:hover { text-decoration: underline; }
    .nt-meta a .mv-i { width: 14px; height: 14px; }
    .nt-link-btn { background: none; border: 0; padding: 0; font: inherit; font-size: 12.5px; color: var(--mv-muted); cursor: pointer; }
    .nt-link-btn:hover { color: var(--mv-ink); text-decoration: underline; }
    .nt-type { font-size: 12px; color: var(--mv-muted); margin-left: auto; }

    .nt-empty { padding: 56px 16px; text-align: center; }
    .nt-empty-icon { width: 40px; height: 40px; margin: 0 auto 10px; border-radius: 10px; display: grid; place-items: center; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-muted); }
    .nt-empty-icon .mv-i { width: 20px; height: 20px; }
    .nt-empty-title { font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .nt-empty-sub { font-size: 13px; color: var(--mv-muted); margin-top: 4px; }
</style>
@endpush

@section('content')
@php
    $typeIcons = ['ticket' => 'ticket', 'job' => 'list-todo', 'asset' => 'box', 'visit' => 'pin', 'employee' => 'user', 'system' => 'bell'];
@endphp
<div class="nt">

    <div class="nt-toolbar">
        <p>All system notifications for your account</p>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn-secondary"><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="nt-list">
        @forelse($notifications as $notification)
            @php
                $data   = $notification->data;
                $type   = $data['type'] ?? 'system';
                $icon   = $typeIcons[$type] ?? 'bell';
                $unread = is_null($notification->read_at);
            @endphp

            <div class="nt-item {{ $unread ? 'is-unread' : '' }}">
                <div class="nt-ic t-{{ $type }}"><svg class="mv-i" aria-hidden="true"><use href="#i-{{ $icon }}"/></svg></div>

                <div class="nt-body">
                    <div class="nt-head">
                        <span class="nt-title">
                            @if($unread)<span class="nt-dot" title="Unread"></span>@endif
                            {{ $data['title'] ?? '' }}
                        </span>
                        <span class="nt-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="nt-text">{{ $data['body'] ?? '' }}</p>
                    <div class="nt-meta">
                        @if(!empty($data['url']))
                            <a href="{{ $data['url'] }}" onclick="markRead('{{ $notification->id }}')">
                                View <svg class="mv-i" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                            </a>
                        @endif
                        @if($unread)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="nt-link-btn">Mark read</button>
                            </form>
                        @endif
                        <span class="nt-type">{{ ucfirst($type) }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="nt-empty">
                <div class="nt-empty-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-bell"/></svg></div>
                <div class="nt-empty-title">You're all caught up!</div>
                <div class="nt-empty-sub">No notifications yet. They'll appear here as activity happens.</div>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
