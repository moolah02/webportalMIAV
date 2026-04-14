{{-- resources/views/reports/history.blade.php --}}
@extends('layouts.app')

@push('styles')
<style>
:root {
    --rh-accent:  #1a3a5c;
    --rh-accent-h:#152e4a;
    --rh-border:  #e2e8f0;
    --rh-muted:   #f8fafc;
    --rh-text:    #0f172a;
    --rh-sub:     #64748b;
    --rh-radius:  12px;
}

/* ── Page shell ──────────────────────────────────────────── */
.rh-page { max-width: 100%; }

/* ── Top bar ─────────────────────────────────────────────── */
.rh-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding-bottom: 18px; border-bottom: 1px solid var(--rh-border);
    margin-bottom: 24px; gap: 12px; flex-wrap: wrap;
}
.rh-topbar h1 { margin: 0; font-size: 22px; font-weight: 700; color: var(--rh-text); }
.rh-topbar p  { margin: 4px 0 0; font-size: 13px; color: var(--rh-sub); }
.rh-topbar-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }

.rh-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 8px; border: 1px solid transparent;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: all .15s; text-decoration: none; white-space: nowrap;
}
.rh-btn-primary { background: var(--rh-accent); color: #fff; }
.rh-btn-primary:hover { background: var(--rh-accent-h); color: #fff; }
.rh-btn-outline { background: #fff; color: #374151; border-color: var(--rh-border); }
.rh-btn-outline:hover { background: var(--rh-muted); }

/* ── Stat cards ──────────────────────────────────────────── */
.rh-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
.rh-stat {
    background: #fff; border: 1px solid var(--rh-border);
    border-radius: var(--rh-radius); padding: 18px 20px;
    display: flex; flex-direction: column; gap: 6px;
    transition: box-shadow .15s;
}
.rh-stat:hover { box-shadow: 0 4px 12px rgba(0,0,0,.07); }
.rh-stat-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; margin-bottom: 2px;
}
.rh-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--rh-sub); }
.rh-stat-value { font-size: 28px; font-weight: 800; line-height: 1; color: var(--rh-text); }
.rh-stat-sub { font-size: 11px; color: #94a3b8; }

/* ── Filter bar ──────────────────────────────────────────── */
.rh-filter-bar {
    display: flex; align-items: center; gap: 10px;
    background: var(--rh-muted); border: 1px solid var(--rh-border);
    border-radius: 10px; padding: 10px 14px; margin-bottom: 16px; flex-wrap: wrap;
}
.rh-filter-bar label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--rh-sub); white-space: nowrap; }
.rh-filter-bar select, .rh-filter-bar input {
    height: 32px; padding: 0 10px; border: 1px solid var(--rh-border);
    border-radius: 7px; font-size: 12px; color: var(--rh-text); background: #fff;
}
.rh-filter-spacer { flex: 1; }

/* ── Table card ──────────────────────────────────────────── */
.rh-card {
    background: #fff; border: 1px solid var(--rh-border);
    border-radius: var(--rh-radius); overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.rh-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid var(--rh-border);
    background: var(--rh-muted);
}
.rh-card-header-title { font-size: 13px; font-weight: 700; color: var(--rh-text); display: flex; align-items: center; gap: 8px; }
.rh-count-badge {
    background: #e0e7ff; color: #3730a3;
    font-size: 11px; font-weight: 700;
    padding: 2px 8px; border-radius: 20px;
}

table.rh-table { width: 100%; border-collapse: collapse; font-size: 13px; }
table.rh-table thead th {
    padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em; color: var(--rh-sub);
    background: var(--rh-muted); border-bottom: 1px solid var(--rh-border);
}
table.rh-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
table.rh-table tbody tr:last-child { border-bottom: none; }
table.rh-table tbody tr:hover { background: #f8fafc; }
table.rh-table td { padding: 13px 16px; vertical-align: middle; }

/* ── User avatar ─────────────────────────────────────────── */
.rh-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #1a3a5c, #2563eb);
    color: #fff; font-size: 12px; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rh-user-cell { display: flex; align-items: center; gap: 10px; }
.rh-user-name { font-weight: 600; color: var(--rh-text); font-size: 13px; }
.rh-user-email { font-size: 11px; color: #94a3b8; margin-top: 1px; }

/* ── Action badges ───────────────────────────────────────── */
.rh-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; white-space: nowrap;
    border: 1px solid transparent;
}
.rh-badge-preview { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.rh-badge-export  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.rh-badge-format  {
    background: #fff7ed; color: #c2410c; border-color: #fed7aa;
    font-size: 10px; padding: 2px 6px; border-radius: 4px;
    text-transform: uppercase; letter-spacing: .05em;
}

/* ── Source pill ─────────────────────────────────────────── */
.rh-source-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: #f1f5f9; color: #334155;
    border: 1px solid #e2e8f0; border-radius: 6px;
    padding: 3px 9px; font-size: 11px; font-weight: 600;
    font-family: ui-monospace, monospace;
}

/* ── Row count bar ───────────────────────────────────────── */
.rh-rows-wrap { display: flex; flex-direction: column; gap: 3px; }
.rh-rows-val { font-size: 13px; font-weight: 700; color: var(--rh-text); }
.rh-rows-bar-outer { width: 60px; height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; }
.rh-rows-bar-inner { height: 100%; background: #6366f1; border-radius: 2px; }

/* ── IP mono ─────────────────────────────────────────────── */
.rh-ip { font-family: ui-monospace, monospace; font-size: 11px; color: #64748b; }

/* ── Time cell ───────────────────────────────────────────── */
.rh-time-main { font-size: 12px; font-weight: 600; color: var(--rh-text); }
.rh-time-ago  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* ── Empty state ─────────────────────────────────────────── */
.rh-empty { text-align: center; padding: 56px 20px; }
.rh-empty-icon { font-size: 42px; margin-bottom: 12px; }
.rh-empty-title { font-size: 15px; font-weight: 600; color: var(--rh-text); margin: 0 0 6px; }
.rh-empty-sub { font-size: 13px; color: var(--rh-sub); margin: 0; }

/* ── Pagination override ─────────────────────────────────── */
.rh-pagination { padding: 14px 16px; border-top: 1px solid var(--rh-border); background: var(--rh-muted); }
</style>
@endpush

@section('content')
@php
    $collection   = $runs->getCollection();
    $totalRuns    = $runs->total();
    $totalPreviews= $collection->where('action', 'preview')->count();
    $totalExports = $collection->where('action', 'export')->count();
    $uniqueUsers  = $collection->pluck('user_id')->unique()->count();
    $maxRows      = $collection->max('result_count') ?: 1;
@endphp

<div class="rh-page">

    {{-- ── Top bar ──────────────────────────────────────────── --}}
    <div class="rh-topbar">
        <div>
            <h1>&#128221; Report Audit Trail</h1>
            <p>Every report run and export, who triggered it, when, and from where.</p>
        </div>
        <div class="rh-topbar-actions">
            <a href="{{ route('reports.builder') }}" class="rh-btn rh-btn-outline">
                &#8592; Report Builder
            </a>
        </div>
    </div>

    {{-- ── Stat cards ────────────────────────────────────────── --}}
    <div class="rh-stats">
        <div class="rh-stat">
            <div class="rh-stat-icon" style="background:#eff6ff;">&#128202;</div>
            <div class="rh-stat-label">Total Runs</div>
            <div class="rh-stat-value">{{ number_format($totalRuns) }}</div>
            <div class="rh-stat-sub">all time</div>
        </div>
        <div class="rh-stat">
            <div class="rh-stat-icon" style="background:#eff6ff; color:#1d4ed8;">&#9654;</div>
            <div class="rh-stat-label">Previews</div>
            <div class="rh-stat-value" style="color:#1d4ed8;">{{ number_format($totalPreviews) }}</div>
            <div class="rh-stat-sub">this page</div>
        </div>
        <div class="rh-stat">
            <div class="rh-stat-icon" style="background:#f0fdf4; color:#15803d;">&#11015;</div>
            <div class="rh-stat-label">Exports</div>
            <div class="rh-stat-value" style="color:#15803d;">{{ number_format($totalExports) }}</div>
            <div class="rh-stat-sub">this page</div>
        </div>
        <div class="rh-stat">
            <div class="rh-stat-icon" style="background:#faf5ff; color:#7c3aed;">&#128100;</div>
            <div class="rh-stat-label">Unique Users</div>
            <div class="rh-stat-value" style="color:#7c3aed;">{{ $uniqueUsers }}</div>
            <div class="rh-stat-sub">this page</div>
        </div>
    </div>

    {{-- ── Table card ─────────────────────────────────────────── --}}
    <div class="rh-card">
        <div class="rh-card-header">
            <div class="rh-card-header-title">
                &#128337; Activity Log
                <span class="rh-count-badge">{{ number_format($totalRuns) }} total</span>
            </div>
            <div style="font-size:12px; color:#94a3b8;">Showing {{ $runs->firstItem() }}–{{ $runs->lastItem() }} of {{ number_format($runs->total()) }}</div>
        </div>

        <table class="rh-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Data Source</th>
                    <th>Columns</th>
                    <th>Rows Returned</th>
                    <th>IP Address</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @forelse($runs as $run)
                @php
                    $payload  = is_array($run->payload) ? $run->payload : [];
                    $srcTable = $payload['base']['table'] ?? null;
                    $colCount = isset($payload['select']) ? count($payload['select']) : 0;
                    $isExport = $run->action === 'export';
                    $initials = $run->user
                        ? strtoupper(substr($run->user->first_name ?? '?', 0, 1) . substr($run->user->last_name ?? '', 0, 1))
                        : '??';
                    $rowPct = $maxRows > 0 ? min(100, round(($run->result_count / $maxRows) * 100)) : 0;
                @endphp
                <tr>
                    {{-- User --}}
                    <td>
                        <div class="rh-user-cell">
                            <div class="rh-avatar">{{ $initials }}</div>
                            <div>
                                @if($run->user)
                                    <div class="rh-user-name">{{ $run->user->first_name }} {{ $run->user->last_name }}</div>
                                    <div class="rh-user-email">{{ $run->user->email ?? '' }}</div>
                                @else
                                    <div class="rh-user-name" style="color:#94a3b8;font-style:italic;">Unknown user</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Action --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            @if($isExport)
                                <span class="rh-badge rh-badge-export">&#11015; Export</span>
                                @if($run->format)
                                    <span class="rh-badge-format">{{ $run->format }}</span>
                                @endif
                            @else
                                <span class="rh-badge rh-badge-preview">&#9654; Preview</span>
                            @endif
                        </div>
                    </td>

                    {{-- Data source --}}
                    <td>
                        @if($srcTable)
                            <span class="rh-source-pill">{{ ucwords(str_replace('_', ' ', $srcTable)) }}</span>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>

                    {{-- Columns --}}
                    <td>
                        <span style="font-size:13px; font-weight:600; color:#374151;">{{ $colCount ?: '—' }}</span>
                        @if($colCount > 0)
                            <span style="font-size:11px; color:#94a3b8;"> col{{ $colCount !== 1 ? 's' : '' }}</span>
                        @endif
                    </td>

                    {{-- Rows with mini bar --}}
                    <td>
                        <div class="rh-rows-wrap">
                            <span class="rh-rows-val">{{ number_format($run->result_count) }}</span>
                            <div class="rh-rows-bar-outer">
                                <div class="rh-rows-bar-inner" style="width:{{ $rowPct }}%;"></div>
                            </div>
                        </div>
                    </td>

                    {{-- IP --}}
                    <td>
                        <span class="rh-ip">{{ $run->ip_address ?? '—' }}</span>
                    </td>

                    {{-- When --}}
                    <td>
                        @if($run->executed_at)
                            <div class="rh-time-main">{{ $run->executed_at->format('d M Y, H:i') }}</div>
                            <div class="rh-time-ago">{{ $run->executed_at->diffForHumans() }}</div>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="rh-empty">
                            <div class="rh-empty-icon">&#128202;</div>
                            <p class="rh-empty-title">No report activity yet</p>
                            <p class="rh-empty-sub">Every time someone runs or exports a report it will appear here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($runs->hasPages())
            <div class="rh-pagination">
                {{ $runs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
