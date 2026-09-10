{{-- resources/views/reports/history.blade.php --}}
@extends('layouts.app')
@section('title', 'Reports History')

@push('styles')
<style>
.rh-page { display: grid; gap: 16px; }
.rh-page .mv-i { width: 16px; height: 16px; }

/* ── Toolbar ─────────────────────────────────────────────── */
.rh-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.rh-toolbar p { margin: 0; font-size: 13px; color: var(--mv-muted); }
.rh-btn {
    display: inline-flex; align-items: center; gap: 7px; height: 34px; padding: 0 14px;
    border-radius: 8px; border: 1px solid transparent; font: inherit; font-size: 13px; font-weight: 500;
    cursor: pointer; text-decoration: none; white-space: nowrap;
}
.rh-btn-primary { background: var(--mv-accent); border-color: var(--mv-accent); color: #fff; }
.rh-btn-primary:hover { background: var(--mv-accent-ink); color: #fff; }
.rh-btn-primary:disabled { opacity: .5; cursor: default; }
.rh-btn-outline { background: var(--mv-surface); color: var(--mv-ink); border-color: var(--mv-line-strong); }
.rh-btn-outline:hover { background: var(--mv-surface-2); color: var(--mv-ink); }

/* ── Figures ─────────────────────────────────────────────── */
.rh-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
.rh-stat { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; padding: 14px 16px; }
.rh-stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
.rh-stat-top .mv-i { color: var(--mv-muted); }
.rh-stat-value { margin-top: 6px; font-size: 24px; font-weight: 600; letter-spacing: -.02em; line-height: 1.15; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.rh-stat-sub { margin-top: 2px; font-size: 12px; color: var(--mv-muted); }

/* ── Table card ──────────────────────────────────────────── */
.rh-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.rh-card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--mv-line); }
.rh-card-header-title { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.rh-count-badge { background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); font-size: 12px; font-weight: 500; padding: 0 7px; border-radius: 6px; font-variant-numeric: tabular-nums; }
.rh-card-header-meta { font-size: 12.5px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }

table.rh-table { width: 100%; border-collapse: collapse; font-size: 13px; }
table.rh-table thead th { text-align: left; white-space: nowrap; }
table.rh-table td { vertical-align: middle; }

/* ── Cells ───────────────────────────────────────────────── */
.rh-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    background: var(--mv-accent-soft); color: var(--mv-accent-ink);
    font-size: 11.5px; font-weight: 600; display: inline-grid; place-items: center; letter-spacing: .02em;
}
.rh-user-cell { display: flex; align-items: center; gap: 10px; }
.rh-user-name { font-weight: 500; color: var(--mv-ink); font-size: 13px; white-space: nowrap; }
.rh-user-email { font-size: 12px; color: var(--mv-muted); }
.rh-action { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.rh-badge { display: inline-flex; align-items: center; gap: 5px; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; white-space: nowrap; }
.rh-badge .mv-i { width: 13px; height: 13px; }
.rh-badge-preview { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
.rh-badge-export  { background: var(--mv-good-soft); color: var(--mv-good); }
.rh-badge-format  { font-family: var(--mv-mono); font-size: 11px; color: var(--mv-ink-2); background: var(--mv-surface-2); border: 1px solid var(--mv-line); border-radius: 4px; padding: 0 5px; text-transform: uppercase; }
.rh-source-pill { display: inline-block; font-size: 12.5px; color: var(--mv-ink); white-space: nowrap; }
.rh-rows-val { font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.rh-ip { font-family: var(--mv-mono); font-size: 12px; color: var(--mv-muted); }
.rh-time-main { font-size: 13px; color: var(--mv-ink); white-space: nowrap; font-variant-numeric: tabular-nums; }
.rh-time-ago  { font-size: 12px; color: var(--mv-muted); white-space: nowrap; }
.rh-dash { color: var(--mv-muted); }
.rh-col-names { color: var(--mv-ink-2); cursor: help; border-bottom: 1px dashed var(--mv-line-strong); font-variant-numeric: tabular-nums; }

/* ── Row actions ─────────────────────────────────────────── */
.rh-action-group { display: flex; gap: 4px; align-items: center; flex-wrap: nowrap; }
.rh-action-btn {
    display: inline-flex; align-items: center; gap: 5px; height: 28px; padding: 0 9px;
    border-radius: 6px; border: 1px solid var(--mv-line); background: var(--mv-surface);
    font: inherit; font-size: 12px; font-weight: 500; color: var(--mv-ink-2); cursor: pointer; white-space: nowrap;
}
.rh-action-btn .mv-i { width: 13px; height: 13px; }
.rh-action-btn:hover { background: var(--mv-surface-2); border-color: var(--mv-line-strong); color: var(--mv-ink); }
.rh-action-btn:disabled { opacity: .6; cursor: default; }
.rh-action-btn-run { color: var(--mv-accent-ink); }

/* ── Empty / pagination ──────────────────────────────────── */
.rh-empty { text-align: center; padding: 48px 20px; }
.rh-empty-icon { width: 40px; height: 40px; margin: 0 auto 10px; border-radius: 10px; display: grid; place-items: center; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-muted); }
.rh-empty-icon .mv-i { width: 20px; height: 20px; }
.rh-empty-title { font-size: 14px; font-weight: 600; color: var(--mv-ink); margin: 0 0 4px; }
.rh-empty-sub { font-size: 13px; color: var(--mv-muted); margin: 0; }
.rh-pagination { padding: 12px 16px; border-top: 1px solid var(--mv-line); }

/* ── Modal + toast ───────────────────────────────────────── */
.rh-modal-backdrop { position: fixed; inset: 0; background: rgba(22, 32, 44, .45); display: flex; align-items: center; justify-content: center; z-index: 1100; }
.rh-modal-box { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 20px 48px rgba(22, 32, 44, .18); padding: 20px 22px; width: 420px; max-width: 92vw; }
.rh-modal-box h3 { margin: 0 0 16px; font-size: 15px; font-weight: 600; color: var(--mv-ink); }
.rh-modal-label { font-size: 12.5px; font-weight: 500; color: var(--mv-ink-2); display: block; margin-bottom: 5px; }
.rh-modal-input { width: 100%; padding: 8px 11px; border: 1px solid var(--mv-line-strong); border-radius: 8px; font: inherit; font-size: 13.5px; box-sizing: border-box; color: var(--mv-ink); }
.rh-modal-input:focus { outline: none; border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
.rh-toast { position: fixed; bottom: 24px; right: 24px; z-index: 1200; background: var(--mv-ink); color: #fff; padding: 11px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; box-shadow: 0 12px 32px rgba(22, 32, 44, .2); pointer-events: none; }

@media (max-width: 1000px) { .rh-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

@section('content')
@php
    $collection   = $runs->getCollection();
    $totalRuns    = $runs->total();
    $totalPreviews= $collection->where('action', 'preview')->count();
    $totalExports = $collection->where('action', 'export')->count();
    $uniqueUsers  = $collection->pluck('user_id')->unique()->count();
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="rh-page" id="rh-app">

    {{-- Toolbar --}}
    <div class="rh-toolbar">
        <p>Report Audit Trail — every report run and export, who triggered it, when, and from where.</p>
        <a href="{{ route('reports.builder') }}" class="rh-btn rh-btn-outline">
            <svg class="mv-i" aria-hidden="true"><use href="#i-arrow-left"/></svg> Report Builder
        </a>
    </div>

    {{-- Figures --}}
    <div class="rh-stats">
        <div class="rh-stat">
            <div class="rh-stat-top">Total Runs <svg class="mv-i" aria-hidden="true"><use href="#i-activity"/></svg></div>
            <div class="rh-stat-value">{{ number_format($totalRuns) }}</div>
            <div class="rh-stat-sub">all time</div>
        </div>
        <div class="rh-stat">
            <div class="rh-stat-top">Previews <svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg></div>
            <div class="rh-stat-value">{{ number_format($totalPreviews) }}</div>
            <div class="rh-stat-sub">this page</div>
        </div>
        <div class="rh-stat">
            <div class="rh-stat-top">Exports <svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg></div>
            <div class="rh-stat-value">{{ number_format($totalExports) }}</div>
            <div class="rh-stat-sub">this page</div>
        </div>
        <div class="rh-stat">
            <div class="rh-stat-top">Unique Users <svg class="mv-i" aria-hidden="true"><use href="#i-users"/></svg></div>
            <div class="rh-stat-value">{{ $uniqueUsers }}</div>
            <div class="rh-stat-sub">this page</div>
        </div>
    </div>

    {{-- Activity log --}}
    <div class="rh-card">
        <div class="rh-card-header">
            <div class="rh-card-header-title">
                Activity Log
                <span class="rh-count-badge">{{ number_format($totalRuns) }} total</span>
            </div>
            @if($runs->total() > 0)
            <div class="rh-card-header-meta">Showing {{ $runs->firstItem() }}–{{ $runs->lastItem() }} of {{ number_format($runs->total()) }}</div>
            @endif
        </div>

        <div style="overflow-x:auto;">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($runs as $run)
                @php
                    $payload  = is_array($run->payload) ? $run->payload : [];
                    $srcTable = $payload['base']['table'] ?? null;
                    $colCount = isset($payload['select']) ? count($payload['select']) : 0;
                    $colNames = isset($payload['select']) ? collect($payload['select'])->pluck('as')->implode(', ') : '';
                    $isExport = $run->action === 'export';
                    $initials = $run->user
                        ? strtoupper(substr($run->user->first_name ?? '?', 0, 1) . substr($run->user->last_name ?? '', 0, 1))
                        : '??';
                    $payloadJson = json_encode($payload);
                @endphp
                <tr>
                    <td>
                        <div class="rh-user-cell">
                            <div class="rh-avatar">{{ $initials }}</div>
                            <div>
                                @if($run->user)
                                    <div class="rh-user-name">{{ $run->user->first_name }} {{ $run->user->last_name }}</div>
                                    <div class="rh-user-email">{{ $run->user->email ?? '' }}</div>
                                @else
                                    <div class="rh-user-name" style="color:var(--mv-muted);">Unknown user</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="rh-action">
                            @if($isExport)
                                <span class="rh-badge rh-badge-export"><svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Export</span>
                                @if($run->format)
                                    <span class="rh-badge-format">{{ $run->format }}</span>
                                @endif
                            @else
                                <span class="rh-badge rh-badge-preview"><svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg> Preview</span>
                            @endif
                        </div>
                    </td>

                    <td>
                        @if($srcTable)
                            <span class="rh-source-pill">{{ ucwords(str_replace('_', ' ', $srcTable)) }}</span>
                        @else
                            <span class="rh-dash">—</span>
                        @endif
                    </td>

                    <td>
                        @if($colCount > 0)
                            <span class="rh-col-names" title="{{ $colNames }}">{{ $colCount }} col{{ $colCount !== 1 ? 's' : '' }}</span>
                        @else
                            <span class="rh-dash">—</span>
                        @endif
                    </td>

                    <td><span class="rh-rows-val">{{ number_format($run->result_count) }}</span></td>

                    <td><span class="rh-ip">{{ $run->ip_address ?? '—' }}</span></td>

                    <td>
                        @if($run->executed_at)
                            <div class="rh-time-main">{{ $run->executed_at->format('d M Y, H:i') }}</div>
                            <div class="rh-time-ago">{{ $run->executed_at->diffForHumans() }}</div>
                        @else
                            <span class="rh-dash">—</span>
                        @endif
                    </td>

                    <td>
                        @if(!empty($payload['select']))
                        <div class="rh-action-group">
                            <button class="rh-action-btn rh-action-btn-run"
                                    onclick="rhRerun({{ $payloadJson }})"
                                    title="Load this report in the Report Builder">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-play"/></svg> Re-run
                            </button>
                            <button class="rh-action-btn"
                                    onclick="rhExport({{ $payloadJson }}, 'csv', this)"
                                    title="Download as CSV">CSV</button>
                            <button class="rh-action-btn"
                                    onclick="rhExport({{ $payloadJson }}, 'pdf', this)"
                                    title="Download as PDF">PDF</button>
                            <button class="rh-action-btn"
                                    onclick="rhOpenSaveTemplate({{ $payloadJson }})"
                                    title="Save this report as a named template">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-save"/></svg> Save
                            </button>
                        </div>
                        @else
                            <span class="rh-dash">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="rh-empty">
                            <div class="rh-empty-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-history"/></svg></div>
                            <p class="rh-empty-title">No report activity yet</p>
                            <p class="rh-empty-sub">Every time someone runs or exports a report it will appear here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($runs->hasPages())
            <div class="rh-pagination">
                {{ $runs->links() }}
            </div>
        @endif
    </div>

{{-- Save-as-template modal --}}
<div id="rh-tpl-modal" class="rh-modal-backdrop" style="display:none;">
    <div class="rh-modal-box">
        <h3>Save as Template</h3>
        <div style="margin-bottom:12px;">
            <label class="rh-modal-label" for="rh-tpl-name">Template Name *</label>
            <input id="rh-tpl-name" type="text" class="rh-modal-input" placeholder="e.g. Monthly Visits Report">
        </div>
        <div style="margin-bottom:18px;">
            <label class="rh-modal-label" for="rh-tpl-desc">Description (optional)</label>
            <textarea id="rh-tpl-desc" rows="2" class="rh-modal-input" style="resize:vertical;height:64px;"></textarea>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;">
            <button onclick="rhCloseSaveTemplate()" class="rh-btn rh-btn-outline">Cancel</button>
            <button id="rh-tpl-save-btn" onclick="rhSaveTemplate()" class="rh-btn rh-btn-primary">Save Template</button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="rh-toast" class="rh-toast" style="display:none;"></div>

</div>
@endsection

@push('scripts')
<script>
let _rhTplPayload = null;

function rhToast(msg, ok) {
    const t = document.getElementById('rh-toast');
    t.textContent = msg;
    t.style.background = ok === false ? '#B83232' : '#16202C';
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3200);
}

function rhRerun(payload) {
    sessionStorage.setItem('rb_load_run', JSON.stringify(payload));
    window.location.href = '{{ route("reports.builder") }}';
}

async function rhExport(payload, format, btn) {
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = '…';
    try {
        const p = { ...payload, format, filename: 'report_' + new Date().toISOString().slice(0,10), download_all: true };
        const res = await fetch('/api/report/export', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(p)
        });
        if (res.ok) {
            const blob = await res.blob();
            const url  = URL.createObjectURL(blob);
            const a    = Object.assign(document.createElement('a'), { href: url, download: p.filename + '.' + format });
            document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
            rhToast('Download started', true);
        } else {
            const r = await res.json().catch(() => ({}));
            rhToast('Export failed: ' + (r.error || res.statusText), false);
        }
    } catch(e) { rhToast('Export failed: ' + e.message, false); }
    finally { btn.disabled = false; btn.innerHTML = orig; }
}

function rhOpenSaveTemplate(payload) {
    _rhTplPayload = payload;
    document.getElementById('rh-tpl-name').value = '';
    document.getElementById('rh-tpl-desc').value = '';
    document.getElementById('rh-tpl-modal').style.display = 'flex';
    setTimeout(() => document.getElementById('rh-tpl-name').focus(), 50);
}

function rhCloseSaveTemplate() {
    document.getElementById('rh-tpl-modal').style.display = 'none';
    _rhTplPayload = null;
}

async function rhSaveTemplate() {
    const name = document.getElementById('rh-tpl-name').value.trim();
    if (!name) { document.getElementById('rh-tpl-name').focus(); return; }
    const desc = document.getElementById('rh-tpl-desc').value.trim();
    const btn  = document.getElementById('rh-tpl-save-btn');
    btn.disabled = true; btn.textContent = 'Saving…';

    // Reconstruct UI-format payload from server-format payload
    const p = _rhTplPayload;
    const fields = (p.select || []).map(item => ({
        label:      item.aggregate
                        ? item.as.replace(new RegExp('^' + item.aggregate + '\\('), '').replace(/\)$/, '')
                        : item.as,
        expression: item.expr,
        category:   item.aggregate ? 'measures' : 'dimensions',
        aggregate:  item.aggregate || '',
    }));
    const config = { baseTable: p.base?.table || 'pos_terminals', regionId: '', clientId: '', dateColumn: '', dateFrom: '', dateTo: '', limit: p.limit || 100 };
    (p.where || []).forEach(w => {
        if (w.operator === 'between_dates') {
            config.dateColumn = w.column;
            config.dateFrom = w.value?.from || '';
            config.dateTo   = w.value?.to   || '';
        }
    });

    try {
        const res = await fetch('/api/report/templates', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ name, description: desc, is_global: false, payload: { fields, ...config } })
        });
        const r = await res.json().catch(() => ({}));
        if (r.success) {
            rhCloseSaveTemplate();
            rhToast('Template "' + name + '" saved! Load it from the Report Builder.');
        } else {
            rhToast('Save failed: ' + (r.error || 'Unknown error'), false);
        }
    } catch(e) { rhToast('Save failed: ' + e.message, false); }
    finally { btn.disabled = false; btn.textContent = 'Save Template'; }
}

// Close modal on backdrop click
document.getElementById('rh-tpl-modal').addEventListener('click', function(e) {
    if (e.target === this) rhCloseSaveTemplate();
});

// Close modal on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') rhCloseSaveTemplate(); });

// Enter key submits the template form
document.getElementById('rh-tpl-name').addEventListener('keydown', e => { if (e.key === 'Enter') rhSaveTemplate(); });
</script>
@endpush
