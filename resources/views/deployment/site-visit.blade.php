{{-- resources/views/deployment/site-visit.blade.php --}}
@extends('layouts.app')

@section('title', $prefillAssignment
    ? 'Site Visits — '.$prefillAssignment->project->project_name.' ('.$prefillAssignment->client->company_name.')'
    : 'Site Visits')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php $a = $prefillAssignment; @endphp

{{-- Back --}}
<div class="flex justify-end mb-5">
    <a href="{{ url()->previous() }}" class="btn-secondary">&#x2190; Back</a>
</div>

{{-- Job Details --}}
<div class="ui-card mb-5">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Job Details</span>
        @if($a)
            <span class="badge badge-blue text-xs">{{ $a->assignment_id }}</span>
        @endif
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-6">

        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Technician</div>
            <div class="text-sm text-gray-800 font-medium">
                @if($a && $a->technician)
                    {{ $a->technician->first_name }} {{ $a->technician->last_name }}
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </div>
        </div>

        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Project &amp; Client</div>
            <div class="text-sm text-gray-800 font-medium">
                @if($a && $a->project && $a->client)
                    {{ $a->project->project_name }}
                    <span class="text-gray-400 font-normal">— {{ $a->client->company_name }}</span>
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </div>
        </div>

        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Scheduled Date</div>
            <div class="text-sm text-gray-800 font-medium">
                @if($a && $a->scheduled_date)
                    {{ $a->scheduled_date->format('M j, Y') }}
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </div>
        </div>

        @if($a && !empty($a->team_members))
        <div class="sm:col-span-3 border-t border-gray-100 pt-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Team Members</div>
            <div class="flex flex-wrap gap-2">
                @foreach($a->team_members as $member)
                    <span class="badge badge-gray">{{ $member['name'] ?? $member }}</span>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Terminals --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">
            Terminals
            @if(isset($terminals))
                <span class="text-gray-400 font-normal">({{ $terminals->count() }})</span>
            @endif
        </span>
        <div class="flex gap-2 flex-wrap items-center">
            <input type="text" id="terminalSearch" placeholder="Search terminals…"
                   class="ui-input text-sm w-48">
            <div class="flex">
                <input type="text" id="addTerminalInput" placeholder="Add terminal by ID…"
                       class="ui-input text-sm rounded-r-none border-r-0 w-44">
                <button id="btnAddTerminal" class="btn-secondary rounded-l-none border-l-0 text-sm">Add</button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="ui-table">
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
                    <td>
                        <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5c] text-white text-xs font-semibold font-mono">
                            {{ $t->terminal_id }}
                        </span>
                    </td>
                    <td class="text-sm text-gray-800">{{ $t->merchant_name ?? '—' }}</td>
                    <td class="text-sm text-gray-600">{{ $t->terminal_model ?? '—' }}</td>
                    <td class="text-sm text-gray-600">{{ $t->serial_number ?? '—' }}</td>
                    <td class="text-sm text-gray-600 max-w-xs truncate">{{ $t->physical_address ?? $t->address ?? '—' }}</td>
                    <td class="text-sm text-gray-600">
                        {{ $t->city ?? '—' }}@if($t->province), <span class="text-gray-400">{{ $t->province }}</span>@endif
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
                            <span class="text-xs text-gray-400">Not visited</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($a)
                            <a class="btn-primary btn-sm"
                               href="{{ route('site_visits.edit_terminal', ['assignment_id' => $a->id, 'terminal_id' => $t->id]) }}">
                                Edit
                            </a>
                        @endif
                        <a class="btn-secondary btn-sm"
                           href="{{ route('reports.technician-visits') }}?terminal_id={{ $t->id }}">
                            View More
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">&#x1F4CB;</div>
                            <div class="empty-state-msg">No terminals assigned to this job.</div>
                        </div>
                    </td>
                </tr>
                @endforelse

                <tr id="extraTerminalsAnchor" style="display:none;"></tr>
            </tbody>
        </table>
    </div>

    <div id="noResults" class="hidden px-5 py-8 text-center text-sm text-gray-400">
        No terminals match your search.
    </div>
</div>

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
        <td><span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5c] text-white text-xs font-semibold font-mono">${t.terminal_id ?? '—'}</span></td>
        <td class="text-sm text-gray-800">${t.merchant_name ?? '—'}</td>
        <td class="text-sm text-gray-600">${t.terminal_model ?? '—'}</td>
        <td class="text-sm text-gray-600">${t.serial_number ?? '—'}</td>
        <td class="text-sm text-gray-600">${t.address ?? '—'}</td>
        <td class="text-sm text-gray-600">${t.city ?? '—'}${t.province ? ', <span class="text-gray-400">'+t.province+'</span>' : ''}</td>
        <td><span class="text-xs text-gray-400">Not visited</span></td>
        <td class="text-right">
          <a class="btn-primary btn-sm" href="${editUrl}">Edit</a>
          <a class="btn-secondary btn-sm" href="${viewUrl}">View More</a>
        </td>`;

      anchorRow.style.display = '';
      tableBody.appendChild(newRow);
      addInput.value = '';
      if (searchEl) { searchEl.value = ''; searchEl.dispatchEvent(new Event('input')); }

      const toast = document.createElement('div');
      toast.className = 'flash-success';
      toast.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:9999;';
      toast.innerHTML = `<span>&#x2713;</span> Terminal ${t.terminal_id} added`;
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
