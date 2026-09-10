{{-- resources/views/deployment/site-visit.blade.php --}}
@extends('layouts.app')

@section('title', $prefillAssignment
    ? 'Site Visits — '.$prefillAssignment->project->project_name.' ('.$prefillAssignment->client->company_name.')'
    : 'Site Visits')

@push('styles')
<style>
.sd-bar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.sd-dl{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px 24px;margin:0;padding:16px 18px}
@media (max-width:800px){.sd-dl{grid-template-columns:1fr}}
.sd-dl dt{margin:0 0 3px;font-size:12px;font-weight:500;color:var(--mv-muted)}
.sd-dl dd{margin:0;font-size:13.5px;color:var(--mv-ink)}
.sd-dl .sd-span{grid-column:1/-1;padding-top:14px;border-top:1px solid var(--mv-line)}
.sd-none,.sd-muted{color:var(--mv-muted)}
.sd-team{display:flex;flex-wrap:wrap;gap:6px}
.sd-terminals{margin-top:16px}
.mv-page .sd-terminals .ui-card-header{flex-wrap:wrap;gap:10px}
.sd-count{font-weight:400;color:var(--mv-muted);font-variant-numeric:tabular-nums}
.sd-tools{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.sd-search{position:relative}
.sd-search .mv-i{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--mv-muted);pointer-events:none}
.mv-page .sd-tools .ui-input{width:13rem;padding:7px 10px;font-size:13px}
.mv-page .sd-search .ui-input{padding-left:32px}
.sd-add{display:flex}
.mv-page .sd-add .ui-input{width:11rem;border-radius:8px 0 0 8px !important}
.mv-page .sd-add .btn-secondary{margin-left:-1px;padding:7px 14px;border-radius:0 8px 8px 0}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.sd-tid{font-size:12.5px;font-weight:500;color:var(--mv-ink)}
.sd-addr{max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sd-actions{display:flex;gap:6px;justify-content:flex-end}
.sd-empty{padding:40px 16px;text-align:center}
.sd-empty p{margin:0;font-size:13.5px;color:var(--mv-ink-2)}
#noResults{padding:28px 16px;text-align:center;font-size:13px;color:var(--mv-muted);border-top:1px solid var(--mv-line)}
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php $a = $prefillAssignment; @endphp

{{-- Toolbar --}}
<div class="sd-bar">
    <a href="{{ url()->previous() }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back</a>
</div>

{{-- Job Details --}}
<section class="ui-card">
    <div class="ui-card-header">
        <h3>Job Details</h3>
        @if($a)
            <span class="id-chip">{{ $a->assignment_id }}</span>
        @endif
    </div>
    <dl class="sd-dl">
        <div>
            <dt>Technician</dt>
            <dd>
                @if($a && $a->technician)
                    {{ $a->technician->first_name }} {{ $a->technician->last_name }}
                @else
                    <span class="sd-none">—</span>
                @endif
            </dd>
        </div>

        <div>
            <dt>Project &amp; Client</dt>
            <dd>
                @if($a && $a->project && $a->client)
                    {{ $a->project->project_name }}
                    <span class="sd-muted">— {{ $a->client->company_name }}</span>
                @else
                    <span class="sd-none">—</span>
                @endif
            </dd>
        </div>

        <div>
            <dt>Scheduled Date</dt>
            <dd>
                @if($a && $a->scheduled_date)
                    {{ $a->scheduled_date->format('M j, Y') }}
                @else
                    <span class="sd-none">—</span>
                @endif
            </dd>
        </div>

        @if($a && !empty($a->team_members))
        <div class="sd-span">
            <dt>Team Members</dt>
            <dd class="sd-team">
                @foreach($a->team_members as $member)
                    <span class="badge badge-gray">{{ $member['name'] ?? $member }}</span>
                @endforeach
            </dd>
        </div>
        @endif
    </dl>
</section>

{{-- Terminals --}}
<section class="ui-card overflow-hidden sd-terminals">
    <div class="ui-card-header">
        <h3>
            Terminals
            @if(isset($terminals))
                <span class="sd-count">({{ $terminals->count() }})</span>
            @endif
        </h3>
        <div class="sd-tools">
            <div class="sd-search">
                <svg class="mv-i" aria-hidden="true"><use href="#i-search"/></svg>
                <input type="text" id="terminalSearch" placeholder="Search terminals…" class="ui-input" aria-label="Search terminals">
            </div>
            <div class="sd-add">
                <input type="text" id="addTerminalInput" placeholder="Add terminal by ID…" class="ui-input" aria-label="Add terminal by ID">
                <button id="btnAddTerminal" class="btn-secondary">Add</button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Terminal ID</th>
                    <th>Merchant</th>
                    <th>Device</th>
                    <th>Serial</th>
                    <th>Address</th>
                    <th>Location</th>
                    <th>Visit Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="terminalsTableBody">

                @php
                $termsList = isset($terminals) && $terminals instanceof \Illuminate\Support\Collection
                    ? $terminals : collect();

                $visitsByTerminal = collect();
                if ($a && $termsList->count()) {
                    $visitsByTerminal = \App\Models\TechnicianVisit::query()
                        ->where('job_assignment_id', $a->id)
                        ->whereIn('pos_terminal_id', $termsList->pluck('id'))
                        ->latest('started_at')
                        ->get()
                        ->groupBy('pos_terminal_id');
                }
                @endphp

                @forelse ($terminals as $t)
                @php
                $latestVisit = optional($visitsByTerminal[$t->id] ?? collect())->first();
                $searchableData = strtolower(implode(' ', array_filter([
                    $t->merchant_name, $t->terminal_id,
                    $t->terminal_model ?? '', $t->serial_number ?? '',
                    $t->physical_address ?? $t->address ?? '',
                    $t->city ?? '', $t->province ?? ''
                ])));
                $statusText = $latestVisit ? ($latestVisit->terminal_status_during_visit ?? $latestVisit->status) : null;
                @endphp
                <tr class="terminal-row"
                    data-terminal-id="{{ $t->id }}"
                    data-searchable="{{ $searchableData }}">
                    <td><span class="mv-mono sd-tid">{{ $t->terminal_id }}</span></td>
                    <td>{{ $t->merchant_name ?? '—' }}</td>
                    <td>{{ $t->terminal_model ?? '—' }}</td>
                    <td class="mv-mono">{{ $t->serial_number ?? '—' }}</td>
                    <td class="sd-addr" title="{{ $t->physical_address ?? $t->address ?? '' }}">{{ $t->physical_address ?? $t->address ?? '—' }}</td>
                    <td>
                        {{ $t->city ?? '—' }}@if($t->province), <span class="sd-muted">{{ $t->province }}</span>@endif
                    </td>
                    <td>
                        @if($statusText)
                            @php
                            $badgeMap = [
                                'active'    => 'badge-green',
                                'inactive'  => 'badge-red',
                                'relocated' => 'badge-yellow',
                                'replaced'  => 'badge-yellow',
                                'not_found'         => 'badge-gray',
                                'closed'            => 'badge-green',
                                'in_progress'       => 'badge-blue',
                                'open'              => 'badge-blue',
                            ];
                            $badgeClass = $badgeMap[$statusText] ?? 'badge-gray';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ \Illuminate\Support\Str::headline($statusText) }}
                            </span>
                        @else
                            <span class="sd-muted">Not visited</span>
                        @endif
                    </td>
                    <td>
                        <div class="sd-actions">
                            @if($a)
                                <a class="action-btn" title="Edit" aria-label="Edit visit for terminal {{ $t->terminal_id }}"
                                   href="{{ route('site_visits.edit_terminal', ['assignment_id' => $a->id, 'terminal_id' => $t->id]) }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
                            @endif
                            <a class="action-btn" title="View More" aria-label="View more visits for terminal {{ $t->terminal_id }}"
                               href="{{ route('reports.technician-visits') }}?terminal_id={{ $t->id }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-history"/></svg></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="sd-empty">
                            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-clipboard"/></svg></div>
                            <p>No terminals assigned to this job.</p>
                        </div>
                    </td>
                </tr>
                @endforelse

                <tr id="extraTerminalsAnchor" style="display:none;"></tr>
            </tbody>
        </table>
    </div>

    <div id="noResults" class="hidden">
        No terminals match your search.
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tableBody   = document.getElementById('terminalsTableBody');
  const noResults   = document.getElementById('noResults');
  const searchEl    = document.getElementById('terminalSearch');
  const addInput    = document.getElementById('addTerminalInput');
  const addBtn      = document.getElementById('btnAddTerminal');
  const anchorRow   = document.getElementById('extraTerminalsAnchor');
  const ASSIGNMENT_ID = {{ $a?->id ?? 0 }};

  // Search
  if (searchEl) {
    searchEl.addEventListener('input', () => {
      const needle = searchEl.value.toLowerCase().trim();
      const rows = tableBody.querySelectorAll('.terminal-row');
      let visible = 0;
      rows.forEach(row => {
        const match = !needle || (row.dataset.searchable || '').includes(needle);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
      });
      noResults.classList.toggle('hidden', !(needle && !visible));
    });
  }

  // Add terminal found onsite
  async function addExtraTerminal() {
    const rawInput = (addInput.value || '').trim();
    if (!rawInput) return;
    const terminalId = parseInt(rawInput, 10);
    if (!terminalId || isNaN(terminalId)) { alert('Please enter a valid numeric terminal ID'); return; }
    if (tableBody.querySelector(`tr[data-terminal-id="${terminalId}"]`)) {
      alert('This terminal is already in the list');
      addInput.value = '';
      return;
    }
    try {
      const res = await fetch("{{ url('/site-visits/lookup/terminal') }}/" + terminalId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json',
                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      });
      if (!res.ok) throw new Error(res.status === 404 ? 'Terminal not found' : `Server error: ${res.status}`);
      const t = await res.json();

      const editUrl = ASSIGNMENT_ID
        ? "{{ route('site_visits.edit_terminal', ['assignment_id' => '__A__', 'terminal_id' => '__T__']) }}"
            .replace('__A__', ASSIGNMENT_ID).replace('__T__', t.id)
        : '#';
      const viewUrl = "{{ route('reports.technician-visits') }}?terminal_id=" + t.id;

      const newRow = document.createElement('tr');
      newRow.className = 'terminal-row';
      newRow.dataset.terminalId = t.id;
      newRow.dataset.searchable = [t.merchant_name, t.terminal_id, t.terminal_model, t.serial_number, t.address, t.city, t.province].filter(Boolean).join(' ').toLowerCase();
      newRow.innerHTML = `
        <td><span class="mv-mono sd-tid">${t.terminal_id ?? '—'}</span></td>
        <td>${t.merchant_name ?? '—'}</td>
        <td>${t.terminal_model ?? '—'}</td>
        <td class="mv-mono">${t.serial_number ?? '—'}</td>
        <td class="sd-addr">${t.address ?? '—'}</td>
        <td>${t.city ?? '—'}${t.province ? ', <span class="sd-muted">'+t.province+'</span>' : ''}</td>
        <td><span class="sd-muted">Not visited</span></td>
        <td>
          <div class="sd-actions">
            <a class="action-btn" href="${editUrl}" title="Edit" aria-label="Edit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
            <a class="action-btn" href="${viewUrl}" title="View More" aria-label="View More"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-history"/></svg></a>
          </div>
        </td>`;

      anchorRow.style.display = '';
      tableBody.appendChild(newRow);
      addInput.value = '';
      if (searchEl) { searchEl.value = ''; searchEl.dispatchEvent(new Event('input')); }

      const toast = document.createElement('div');
      toast.className = 'flash-success';
      toast.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:9999;';
      toast.innerHTML = `<svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg> Terminal ${t.terminal_id} added`;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);

    } catch (err) {
      alert(err.message || 'Failed to find terminal. Please check the ID and try again.');
    }
  }

  if (addBtn) addBtn.addEventListener('click', addExtraTerminal);
  if (addInput) addInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addExtraTerminal(); } });
});
</script>
@endpush
