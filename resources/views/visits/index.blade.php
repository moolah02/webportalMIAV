@extends('layouts.app')

@section('title', 'Site Visit Management')

@push('styles')
<style>
.vi-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px}
#visits-filter.filter-bar{padding:14px 16px;gap:12px;margin-bottom:16px}
#visits-filter .vi-f{display:flex;flex-direction:column;min-width:0}
#visits-filter .ui-label{margin-bottom:4px}
#visits-filter .ui-input{width:11rem;min-width:0;padding:8px 10px;font-size:13px}
#visits-filter input[type="date"].ui-input{width:9.75rem}
#visits-filter .vi-actions{display:flex;align-items:flex-end;gap:8px;margin-left:auto}
.vi-count{font-size:12px;color:var(--mv-muted);font-variant-numeric:tabular-nums}
.mv-page .vi-table tbody td{vertical-align:top;font-size:13px}
.mv-page .vi-table thead th,.mv-page .vi-table tbody td{padding-left:10px;padding-right:10px}
.mv-page .vi-table thead th:last-child,.mv-page .vi-table tbody td:last-child{position:sticky;right:0;background:var(--mv-surface);box-shadow:-1px 0 0 var(--mv-line)}
.mv-page .vi-table thead th:last-child{background:var(--mv-surface-2)}
.mv-page .vi-table tbody tr:hover td:last-child{background:var(--mv-surface-2)}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.vi-id{font-size:12.5px;font-weight:500;color:var(--mv-ink)}
.vi-strong{font-weight:500;color:var(--mv-ink)}
.vi-sub{margin-top:2px;font-size:12px;color:var(--mv-muted)}
.vi-nowrap{white-space:nowrap}
.vi-summary{min-width:180px;max-width:260px;line-height:1.45}
.vi-chips{display:flex;flex-wrap:wrap;gap:4px}
.vi-disclose summary{list-style:none;display:inline-flex;align-items:center;gap:4px;margin-top:5px;cursor:pointer;font-size:12px;font-weight:500;color:var(--mv-accent-ink);white-space:nowrap;user-select:none}
.vi-disclose.vi-flush summary{margin-top:0}
.vi-disclose summary::-webkit-details-marker{display:none}
.vi-disclose summary:hover{text-decoration:underline}
.vi-disclose summary .mv-i{width:13px;height:13px}
.vi-disclose .vi-chev{transition:transform .15s ease}
.vi-disclose[open] .vi-chev{transform:rotate(90deg)}
.vi-panel{display:grid;grid-template-columns:auto 1fr;gap:3px 10px;min-width:190px;margin:6px 0 0;padding:8px 10px;font-size:12px;background:var(--mv-surface-2);border:1px solid var(--mv-line);border-radius:8px}
.vi-panel dt{margin:0;font-weight:400;color:var(--mv-muted)}
.vi-panel dd{margin:0;font-weight:500;color:var(--mv-ink)}
.vi-panel .vi-crit{color:var(--mv-crit)}
.vi-files{display:flex;flex-direction:column;gap:4px;margin-top:6px;font-size:12px}
.vi-files a{display:inline-flex;align-items:center;gap:4px;color:var(--mv-accent-ink)}
.vi-files span{display:inline-flex;align-items:center;gap:4px;color:var(--mv-ink-2)}
.vi-files .mv-i{width:13px;height:13px;color:var(--mv-muted)}
.vi-row-actions{display:flex;gap:6px;justify-content:flex-end}
.vi-empty{padding:48px 16px;text-align:center}
.vi-empty .empty-state-icon .mv-i{display:block;margin:0 auto}
.vi-empty p{margin:0 0 14px;font-size:13.5px;color:var(--mv-ink-2)}
@media (prefers-reduced-motion: reduce){.vi-disclose .vi-chev{transition:none}}
</style>
@endpush

@section('content')
{{-- Toolbar --}}
<div class="vi-bar">
    <a href="{{ url()->previous() }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back</a>
</div>

{{-- Filters --}}
<form id="visits-filter" method="GET" action="{{ route('visits.index') }}" class="filter-bar">
    <div class="vi-f">
        <label class="ui-label" for="merchant">Merchant</label>
        <input id="merchant" name="merchant" value="{{ request('merchant') }}" class="ui-input"
               placeholder="Search merchant..." list="merchant-list" autocomplete="off">
        <datalist id="merchant-list"></datalist>
    </div>
    <div class="vi-f">
        <label class="ui-label" for="employee">Employee</label>
        <input id="employee" name="employee" value="{{ request('employee') }}" class="ui-input"
               placeholder="Search employee..." list="employee-list" autocomplete="off">
        <datalist id="employee-list"></datalist>
    </div>
    <div class="vi-f">
        <label class="ui-label">From Date</label>
        <input type="date" name="dateFrom" value="{{ request('dateFrom') }}" class="ui-input">
    </div>
    <div class="vi-f">
        <label class="ui-label">To Date</label>
        <input type="date" name="dateTo" value="{{ request('dateTo') }}" class="ui-input">
    </div>
    <div class="vi-f">
        <label class="ui-label" for="terminal">Terminal</label>
        <input id="terminal" name="terminal" value="{{ request('terminal') }}" class="ui-input"
               placeholder="Search terminal ID..." list="terminal-list" autocomplete="off">
        <datalist id="terminal-list"></datalist>
    </div>
    <div class="vi-f">
        <label class="ui-label">Keywords</label>
        <input type="text" name="q" value="{{ request('q') }}" class="ui-input" placeholder="Keywords...">
    </div>
    <div class="vi-actions">
        <button type="submit" class="btn-primary">Apply Filters</button>
        <a href="{{ route('visits.index') }}" class="btn-secondary">Reset All</a>
    </div>
</form>

{{-- Results --}}
@if($visits->isEmpty())
<div class="ui-card vi-empty">
    <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-search"/></svg></div>
    <p>No visits found. Try adjusting your filter criteria.</p>
    <a href="{{ route('visits.index') }}" class="btn-secondary btn-sm">Reset All</a>
</div>
@else
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <h2>Visit Records</h2>
        <span class="vi-count">{{ $visits->count() }} {{ $visits->count() === 1 ? 'record' : 'records' }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table vi-table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date & Time</th>
                    <th>Merchant</th>
                    <th>Employee</th>
                    <th>Assignment</th>
                    <th>Status</th>
                    <th>Terminal</th>
                    <th>Summary</th>
                    <th>Evidence</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visits as $v)
                    @php
                        $terminal = is_array($v->terminal) ? $v->terminal : [];
                        $evidence = is_array($v->evidence) ? $v->evidence : [];
                        $otherTerminals = is_array($v->other_terminals_found) ? $v->other_terminals_found : [];
                    @endphp
                    <tr>
                        <td><span class="mv-mono vi-id">{{ $v->id }}</span></td>
                        <td class="vi-nowrap">
                            @if($v->completed_at)
                                <div class="vi-strong">{{ $v->completed_at->format('M j, Y') }}</div>
                                <div class="vi-sub mv-mono">{{ $v->completed_at->format('H:i') }}</div>
                            @else
                                <span class="vi-sub">Not completed</span>
                            @endif
                        </td>
                        <td>
                            <div class="vi-strong">{{ $v->merchant_name ?? '—' }}</div>
                            <div class="vi-sub">ID: <span class="mv-mono">{{ $v->merchant_id }}</span></div>
                        </td>
                        <td>{{ optional($v->employee)->full_name ?? $v->employee_id }}</td>
                        <td>
                            @if($v->assignment_id)
                                <span class="mv-mono">{{ $v->assignment_id }}</span>
                            @else
                                <span class="vi-sub">—</span>
                            @endif
                        </td>
                        <td>
                            @if($v->completed_at)
                                <span class="badge badge-green"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-check"/></svg>Completed</span>
                            @else
                                <span class="badge badge-yellow"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-hourglass"/></svg>Pending</span>
                            @endif
                        </td>
                        <td>
                            @php $completeTerminal = $v->getCompleteTerminalInfo(); @endphp
                            <div class="vi-chips">
                                @if(!empty($completeTerminal))
                                    <span class="badge badge-gray">1 Terminal</span>
                                @else
                                    <span class="badge badge-gray">No Terminal</span>
                                @endif
                                @if(count($otherTerminals) > 0)
                                    <span class="badge badge-blue">+{{ count($otherTerminals) }} Other</span>
                                @endif
                            </div>
                            @if(!empty($completeTerminal))
                                <details class="vi-disclose">
                                    <summary><svg class="mv-i vi-chev" aria-hidden="true"><use href="#i-chevron-right"/></svg>View Details</summary>
                                    <dl class="vi-panel">
                                        <dt>Terminal ID</dt><dd class="mv-mono">{{ $completeTerminal['terminal_id'] ?? '—' }}</dd>
                                        <dt>Status</dt><dd>{{ $completeTerminal['status'] ?? ($completeTerminal['current_status'] ?? '—') }}</dd>
                                        <dt>Condition</dt><dd>{{ $completeTerminal['condition_status'] ?? $completeTerminal['condition'] ?? '—' }}</dd>
                                        <dt>Model</dt><dd>{{ $completeTerminal['terminal_model'] ?? '—' }}</dd>
                                        <dt>Serial</dt><dd class="mv-mono">{{ $completeTerminal['serial_number'] ?? '—' }}</dd>
                                        @if(!empty($completeTerminal['issues']))
                                            <dt>Issues</dt><dd class="vi-crit">{{ $completeTerminal['issues'] }}</dd>
                                        @endif
                                    </dl>
                                </details>
                            @endif
                        </td>
                        <td class="vi-summary">
                            <div>{{ \Illuminate\Support\Str::limit($v->visit_summary, 120) }}</div>
                            @if(!empty($v->action_points))
                                <div class="vi-sub">{{ \Illuminate\Support\Str::limit($v->action_points, 100) }}</div>
                            @endif
                        </td>
                        <td class="vi-nowrap">
                            @if(count($evidence))
                                <details class="vi-disclose vi-flush">
                                    <summary><svg class="mv-i" aria-hidden="true"><use href="#i-paperclip"/></svg>{{ count($evidence) }} {{ count($evidence) === 1 ? 'File' : 'Files' }}</summary>
                                    <div class="vi-files">
                                        @foreach($evidence as $idx => $item)
                                            @if(\Illuminate\Support\Str::startsWith($item, ['http://', 'https://', '/storage/']))
                                                <a href="{{ $item }}" target="_blank" rel="noopener"><svg class="mv-i" aria-hidden="true"><use href="#i-paperclip"/></svg>Evidence {{ $idx + 1 }}</a>
                                            @else
                                                <span><svg class="mv-i" aria-hidden="true"><use href="#i-file"/></svg>{{ \Illuminate\Support\Str::limit($item, 35) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <span class="vi-sub">No evidence</span>
                            @endif
                        </td>
                        <td>
                            <div class="vi-row-actions">
                                <a href="{{ route('visits.show', $v) }}" class="action-btn" title="View" aria-label="View visit {{ $v->id }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg></a>
                                <a href="{{ route('visits.edit', $v) }}" class="action-btn" title="Edit" aria-label="Edit visit {{ $v->id }}"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@push('scripts')
<script>
(function(){
  const form = document.getElementById('visits-filter');
  const merchantInput = document.getElementById('merchant');
  const employeeInput = document.getElementById('employee');
  const terminalInput = document.getElementById('terminal');
  const merchantList = document.getElementById('merchant-list');
  const employeeList = document.getElementById('employee-list');
  const terminalList = document.getElementById('terminal-list');

  let mTimer = null, eTimer = null, tTimer = null;

  function debounce(key, fn, delay){
    if (key==='m') { clearTimeout(mTimer); mTimer=setTimeout(fn, delay); }
    if (key==='e') { clearTimeout(eTimer); eTimer=setTimeout(fn, delay); }
    if (key==='t') { clearTimeout(tTimer); tTimer=setTimeout(fn, delay); }
  }

  function fetchJSON(url, cb){
    fetch(url).then(r=>r.json()).then(cb).catch(()=>cb([]));
  }

  function suggestMerchants(q){
    if(q.length<1){ merchantList.innerHTML=''; return; }
    fetchJSON(`{{ route('visits.suggest.merchants') }}?q=${encodeURIComponent(q)}`, items=>{
      merchantList.innerHTML = items.map(v=>`<option value="${v}"></option>`).join('');
    });
  }

  function suggestEmployees(q){
    if(q.length<1){ employeeList.innerHTML=''; return; }
    fetchJSON(`{{ route('visits.suggest.employees') }}?q=${encodeURIComponent(q)}`, items=>{
      employeeList.innerHTML = items.map(v=>`<option value="${v.name}"></option>`).join('');
    });
  }

  function suggestTerminals(q){
    if(q.length<1){ terminalList.innerHTML=''; return; }
    fetchJSON(`{{ route('visits.suggest.terminals') }}?q=${encodeURIComponent(q)}`, items=>{
      terminalList.innerHTML = items.map(v=>`<option value="${v}"></option>`).join('');
    });
  }

  merchantInput.addEventListener('input', (e)=>{
    const q = e.target.value || '';
    debounce('m', ()=>suggestMerchants(q), 150);
    debounce('m', ()=>form.requestSubmit(), 400);
  });

  employeeInput.addEventListener('input', (e)=>{
    const q = e.target.value || '';
    debounce('e', ()=>suggestEmployees(q), 150);
    debounce('e', ()=>form.requestSubmit(), 400);
  });

  terminalInput.addEventListener('input', (e)=>{
    const q = e.target.value || '';
    debounce('t', ()=>suggestTerminals(q), 150);
    debounce('t', ()=>form.requestSubmit(), 400);
  });
})();
</script>
@endpush
@endsection
