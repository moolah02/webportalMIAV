{{-- resources/views/jobs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Assignment '.$assignment->assignment_id)

@section('content')
@php
$sBadge = [
    'completed'   => 'badge-green',
    'in_progress' => 'badge-blue',
    'assigned'    => 'badge-gray',
    'cancelled'   => 'badge-red',
    'approved'    => 'badge-green',
    'pending'     => 'badge-yellow',
][$assignment->status] ?? 'badge-gray';
$pBadge = [
    'emergency' => 'badge-red',
    'high'      => 'badge-orange',
    'normal'    => 'badge-blue',
    'low'       => 'badge-gray',
][$assignment->priority] ?? 'badge-gray';
@endphp
<div>
    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('jobs.index') }}" class="btn-secondary">&#x2190; Back to Assignments</a>
    </div>

    {{-- Assignment Header --}}
    <div class="ui-card mb-4">
        <div class="ui-card-body">
            {{-- Title / Status / Actions row --}}
            <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                <div class="flex-1 min-w-[280px]">
                    <h2 class="page-title mb-2">Assignment {{ $assignment->assignment_id }}</h2>
                    <div class="flex gap-2 flex-wrap">
                        <span class="badge {{ $sBadge }}">{{ \Illuminate\Support\Str::headline($assignment->status) }}</span>
                        <span class="badge {{ $pBadge }}">{{ \Illuminate\Support\Str::headline($assignment->priority) }}</span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    @if(auth()->user()->can('update', $assignment) || auth()->user()->hasPermission('manage_jobs') || auth()->user()->hasPermission('all') || (auth()->user()->id == $assignment->technician_id))
                    <div class="flex gap-3 items-center">
                        @if($assignment->status === 'assigned')
                            <button type="button" class="btn-primary" onclick="updateStatus({{ $assignment->id }}, 'in_progress', this)">
                                &#x25B6; Start Assignment
                            </button>
                        @elseif($assignment->status === 'in_progress')
                            <button type="button" class="btn-success" onclick="updateStatus({{ $assignment->id }}, 'completed', this)">
                                &#x2713; Mark Complete
                            </button>
                        @elseif($assignment->status === 'completed')
                            <span class="text-green-600 font-semibold text-sm flex items-center gap-1">
                                &#x2705; Assignment Completed
                            </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Assignment Details --}}
                <div>
                    <h6 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 pb-2 border-b border-gray-100">Assignment Details</h6>
                    <div class="space-y-0">
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Technician</span>
                            <div class="text-sm text-gray-800 text-right flex-1">
                                @if($assignment->technician)
                                <div class="flex items-center gap-2 justify-end">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-xs">
                                        {{ substr($assignment->technician->first_name,0,1) }}{{ substr($assignment->technician->last_name,0,1) }}
                                    </div>
                                    <span>{{ $assignment->technician->first_name }} {{ $assignment->technician->last_name }}</span>
                                </div>
                                @else
                                <span class="text-gray-400">Unassigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Client</span>
                            <span class="text-sm text-gray-800 text-right">{{ $assignment->client->company_name ?? '&#x2014;' }}</span>
                        </div>
                        @if($assignment->project)
                        <div class="flex justify-between items-start py-2.5">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Project</span>
                            <span class="text-sm text-gray-800 text-right">{{ $assignment->project->project_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Service Information --}}
                <div>
                    <h6 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 pb-2 border-b border-gray-100">Service Information</h6>
                    <div class="space-y-0">
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Scheduled Date</span>
                            <div class="text-sm text-gray-800 text-right">
                                @if($assignment->scheduled_date)
                                <div>{{ $assignment->scheduled_date->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $assignment->scheduled_date->diffForHumans() }}</div>
                                @else
                                <span class="text-gray-400">Not scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-start py-2.5 border-b border-gray-50">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Service Type</span>
                            <span class="text-sm text-gray-800 text-right">{{ \Illuminate\Support\Str::headline($assignment->service_type) }}</span>
                        </div>
                        <div class="flex justify-between items-start py-2.5">
                            <span class="text-sm font-medium text-gray-500 min-w-[120px] flex-shrink-0">Created</span>
                            <span class="text-sm text-gray-800 text-right">{{ $assignment->created_at?->format('M j, Y') ?? '&#x2014;' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terminals Section --}}
    @if($terminals->isEmpty())
    <div class="ui-card mb-4">
        <div class="ui-card-body">
            <div class="empty-state">
                <div class="empty-state-icon">&#x1F5A5;</div>
                <p class="empty-state-msg">No terminals assigned</p>
                <p class="text-sm text-gray-400 mt-1">This assignment doesn't have any terminals associated with it.</p>
            </div>
        </div>
    </div>
    @else
    <div class="ui-card mb-4">
        <div class="ui-card-header">
            <h6 class="text-sm font-semibold text-gray-700 m-0">Terminals</h6>
            <div class="flex items-center gap-3">
                <span class="badge badge-gray">{{ $terminals->count() }} {{ \Illuminate\Support\Str::plural('terminal', $terminals->count()) }}</span>
                <div class="relative">
                    <input type="text" id="terminalSearch" placeholder="Search terminals..."
                           class="ui-input !py-1.5 !text-xs pr-7 min-w-[200px]">
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Merchant</th>
                        <th>Terminal ID</th>
                        <th>Address</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="terminalsTableBody">
                @foreach($terminals as $t)
                @php
                $ts = strtolower($t->current_status ?? $t->status ?? 'unknown');
                $tb = $ts === 'active' ? 'badge-green' : ($ts === 'inactive' ? 'badge-red' : 'badge-gray');
                @endphp
                <tr class="terminal-row" data-searchable="{{ strtolower($t->merchant_name . ' ' . $t->terminal_id . ' ' . ($t->physical_address ?? $t->address ?? '') . ' ' . ($t->city ?? '') . ' ' . ($t->province ?? '')) }}">
                    <td>
                        <div class="font-semibold text-gray-800 text-sm">{{ $t->merchant_name ?? '&#x2014;' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $t->client->company_name ?? '&#x2014;' }}</div>
                    </td>
                    <td>
                        <code class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-xs border border-gray-200 font-mono">{{ $t->terminal_id }}</code>
                    </td>
                    <td class="text-sm text-gray-700">{{ $t->physical_address ?? $t->address ?? '&#x2014;' }}</td>
                    <td class="text-sm text-gray-700">
                        {{ $t->city ?? '&#x2014;' }}@if($t->province)<span class="text-gray-400">, {{ $t->province }}</span>@endif
                    </td>
                    <td><span class="badge {{ $tb }}">{{ \Illuminate\Support\Str::headline($t->current_status ?? $t->status ?? 'unknown') }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div id="noResults" class="hidden text-center py-6 border-t border-gray-100">
            <p class="text-sm text-gray-400">No terminals match your search criteria</p>
        </div>
    </div>
    @endif

    {{-- Live Site Visits --}}
    <div class="ui-card mb-4" id="liveSiteVisitsCard">
        <div class="ui-card-header">
            <h6 class="text-sm font-semibold text-gray-700 m-0">Live Site Visits</h6>
            <span class="badge badge-gray" id="liveVisitCount">0</span>
        </div>
        <div class="ui-card-body">
            <div id="liveVisitsList" class="max-h-[420px] overflow-y-auto">
                <div class="text-sm text-gray-400">Waiting for updates...</div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($assignment->notes)
    <div class="ui-card">
        <div class="ui-card-header">
            <h6 class="text-sm font-semibold text-gray-700 m-0">Notes</h6>
        </div>
        <div class="ui-card-body">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $assignment->notes }}</div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Classes used only in JS-generated live visits HTML */
.lv-item  { padding:10px 0; border-bottom:1px solid #f1f5f9; }
.lv-item:last-child { border-bottom:none; }
.lv-hdr   { display:flex; justify-content:space-between; align-items:center; }
.lv-name  { font-weight:600; font-size:0.875rem; color:#111827; }
.lv-tid   { color:#6b7280; font-weight:400; }
.lv-meta  { color:#6b7280; font-size:0.75rem; margin-top:4px; }
.lv-det   { margin-top:6px; font-size:0.8125rem; color:#374151; }
.lv-cmt   { color:#374151; margin-top:4px; }
</style>
@endpush

@push('scripts')
<script>
// ==============================
// Terminal Search
// ==============================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('terminalSearch');
    const tableBody   = document.getElementById('terminalsTableBody');
    const noResults   = document.getElementById('noResults');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            let visible = 0;
            tableBody.querySelectorAll('.terminal-row').forEach(function(row) {
                const match = row.getAttribute('data-searchable').includes(term);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (noResults) noResults.classList.toggle('hidden', visible > 0 || term === '');
        });
    }
});

// ==============================
// Status Update
// ==============================
function updateStatus(assignmentId, newStatus, btn) {
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '&#x23F1; Updating...';

    fetch(`/api/assignments/${assignmentId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Status updated successfully');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('error', err.message || 'Failed to update status');
        btn.disabled = false;
        btn.innerHTML = orig;
    });
}

// ==============================
// Toast notification
// ==============================
function showNotification(type, message) {
    const el = document.createElement('div');
    el.style.cssText = 'position:fixed;top:20px;right:20px;min-width:280px;background:#fff;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,.15);border:1px solid #e5e7eb;z-index:9999;opacity:0;transition:opacity .3s;';
    el.style.borderLeft = `4px solid ${type === 'success' ? '#10b981' : '#ef4444'}`;
    el.innerHTML = `<div style="padding:12px 16px;font-size:14px;color:#374151;">${message}</div>`;
    document.body.appendChild(el);
    setTimeout(() => el.style.opacity = '1', 50);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 4000);
}

// ==============================
// Live Site Visits Polling
// ==============================
(function(){
    const assignmentId = {{ (int)$assignment->id }};
    const listEl  = document.getElementById('liveVisitsList');
    const countEl = document.getElementById('liveVisitCount');

    async function fetchVisits() {
        try {
            const res  = await fetch(`{{ route('api.jobs.assignments.visits', $assignment->id) }}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error('Feed error');

            countEl.textContent = data.count;
            if (data.count === 0) {
                listEl.innerHTML = '<div class="text-sm text-gray-400">No visits yet for this assignment.</div>';
                return;
            }

            listEl.innerHTML = data.visits.map(v => `
                <div class="lv-item">
                    <div class="lv-hdr">
                        <div class="lv-name">${v.merchant_name ?? '&#x2014;'} <span class="lv-tid">(${v.terminal_id ?? '&#x2014;'})</span></div>
                        <span class="badge badge-gray lv-status">${v.status ? v.status.replace('_',' ') : 'open'}</span>
                    </div>
                    <div class="lv-meta">Tech: ${v.technician ?? '&#x2014;'} &bull; Started: ${v.started_at ?? '&#x2014;'} ${v.ended_at ? '&bull; Ended: '+v.ended_at : ''}</div>
                    <div class="lv-det"><strong>Terminal Status:</strong> ${v.terminal_status ?? '&#x2014;'}${v.comments ? '<div class="lv-cmt">'+v.comments+'</div>' : ''}</div>
                </div>
            `).join('');
        } catch (e) {
            console.error(e);
            listEl.innerHTML = '<div class="text-sm text-red-500">Failed to load live visits.</div>';
        }
    }

    fetchVisits();
    setInterval(fetchVisits, 10000);
})();
</script>
@endpush