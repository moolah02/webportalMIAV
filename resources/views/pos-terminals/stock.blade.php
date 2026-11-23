@extends('layouts.app')
@section('title', 'Devices in stock')

@push('styles')
<style>
.ts-lead { margin:4px 0 0; font-size:13.5px; color:var(--mv-ink-2); max-width:62ch; line-height:1.55; }
.ts-bar { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.ts-filters { display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; padding:14px 18px; margin-bottom:16px; }
.ts-f-grow { flex:1; min-width:220px; }
.ts-tid { font-family:var(--mv-mono); font-size:13.5px; font-weight:600; color:var(--mv-ink); }
.ts-sub { display:block; margin-top:2px; font-size:12px; color:var(--mv-muted); }
.ts-none { color:var(--mv-muted); }
.ts-empty { padding:40px 20px; text-align:center; }
.ts-empty p { margin:10px 0 0; font-size:13.5px; color:var(--mv-ink-2); }
.ts-count { font-size:12.5px; color:var(--mv-muted); }
.ts-actions { white-space:nowrap; text-align:right; }
.ts-wrap { overflow-x:auto; }
</style>
@endpush

@section('content')
<div class="mv-page">

    <div class="ts-bar">
        <div>
            <h1 class="page-title">Devices in stock</h1>
            <p class="ts-lead">
                Collected from a site and waiting to go out again. Each one keeps the visit history
                from everywhere it has been.
            </p>
        </div>
        <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">All devices</a>
    </div>

    @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('pos-terminals.stock') }}" class="ui-card ts-filters">
        <div class="ts-f-grow">
            <label class="ui-label" for="search">Search</label>
            <input type="text" name="search" id="search" class="ui-input"
                   placeholder="Terminal ID, serial, model or the site it came off…"
                   value="{{ $search }}">
        </div>
        <div>
            <button type="submit" class="btn-primary">Search</button>
        </div>
        @if($search !== '')
        <div>
            <a href="{{ route('pos-terminals.stock') }}" class="btn-secondary">Reset</a>
        </div>
        @endif
    </form>

    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <h2>In stock</h2>
            <span class="ts-count">
                {{ number_format($terminals->total()) }} {{ $terminals->total() === 1 ? 'device' : 'devices' }}
            </span>
        </div>

        <div class="ts-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Model</th>
                    <th>Serial</th>
                    <th>Came off</th>
                    <th>Sites before</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($terminals as $terminal)
                    @php
                        $last = $terminal->siteAssignments->first();
                        $spells = $terminal->siteAssignments->count();
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('pos-terminals.show', $terminal) }}" class="ts-tid">{{ $terminal->terminal_id }}</a>
                            @if($terminal->visits()->count())
                                <span class="ts-sub">{{ $terminal->visits()->count() }} {{ $terminal->visits()->count() === 1 ? 'visit' : 'visits' }} on record</span>
                            @else
                                <span class="ts-sub">Never visited</span>
                            @endif
                        </td>
                        <td>{{ $terminal->terminal_model ?: '—' }}</td>
                        <td class="mv-mono">{{ $terminal->serial_number ?: '—' }}</td>
                        <td>
                            @if($last)
                                {{ $last->siteLabel() }}
                                @if($last->ended_at)
                                    <span class="ts-sub">{{ $last->ended_at->format('d M Y') }}</span>
                                @endif
                            @else
                                <span class="ts-none">—</span>
                            @endif
                        </td>
                        <td>{{ $spells ?: '—' }}</td>
                        <td class="ts-actions">
                            <a href="{{ route('pos-terminals.deploy.form', $terminal) }}" class="btn-primary btn-sm">Send out</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="ts-empty">
                                <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg></div>
                                <p>
                                    @if($search !== '')
                                        No device in stock matches &ldquo;{{ $search }}&rdquo;.
                                    @else
                                        Nothing is in stock. A device arrives here when it is collected from a site.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($terminals->hasPages())
        <div class="ui-card-footer">
            {{ $terminals->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
