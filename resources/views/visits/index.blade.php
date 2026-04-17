@extends('layouts.app')

@section('title', 'Site Visit Management')

@section('content')
{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title">Site Visit Management</h1>
        <p class="page-subtitle">Comprehensive field visit tracking and reporting</p>
    </div>
    <a href="{{ url()->previous() }}" class="btn-secondary">â† Back</a>
</div>

{{-- Filters --}}
<form id="visits-filter" method="GET" action="{{ route('visits.index') }}" class="filter-bar flex-wrap">
    <div>
        <label class="ui-label">Merchant</label>
        <input id="merchant" name="merchant" value="{{ request('merchant') }}" class="ui-input w-44"
               placeholder="Search merchant..." list="merchant-list" autocomplete="off">
        <datalist id="merchant-list"></datalist>
    </div>
    <div>
        <label class="ui-label">Employee</label>
        <input id="employee" name="employee" value="{{ request('employee') }}" class="ui-input w-44"
               placeholder="Search employee..." list="employee-list" autocomplete="off">
        <datalist id="employee-list"></datalist>
    </div>
    <div>
        <label class="ui-label">From Date</label>
        <input type="date" name="dateFrom" value="{{ request('dateFrom') }}" class="ui-input w-36">
    </div>
    <div>
        <label class="ui-label">To Date</label>
        <input type="date" name="dateTo" value="{{ request('dateTo') }}" class="ui-input w-36">
    </div>
    <div>
        <label class="ui-label">Keywords</label>
        <input type="text" name="q" value="{{ request('q') }}" class="ui-input w-40" placeholder="Keywords...">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Apply Filters</button>
        <a href="{{ route('visits.index') }}" class="btn-secondary">Reset All</a>
    </div>
</form>

{{-- Results --}}
@if($visits->isEmpty())
<div class="empty-state">
    <div class="empty-state-icon">ðŸ”</div>
    <p class="empty-state-msg">No visits found. Try adjusting your filter criteria.</p>
</div>
@else
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <span class="text-sm font-semibold text-gray-800">Visit Records</span>
        <span class="badge badge-gray">{{ $visits->count() }} {{ $visits->count() === 1 ? 'record' : 'records' }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date & Time</th>
                    <th>Merchant</th>
                    <th>Employee</th>
                    <th>Assignment</th>
                    <th>Terminal</th>
                    <th>Summary</th>
                    <th>Evidence</th>
                    <th class="text-center">Actions</th>
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
                        <td>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5c] text-white text-xs font-semibold">{{ $v->id }}</span>
                        </td>
                        <td>
                            @if($v->completed_at)
                                <div class="text-sm font-medium text-gray-900">{{ $v->completed_at->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $v->completed_at->format('H:i') }}</div>
                            @else
                                <span class="text-xs text-gray-400">Not completed</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm font-semibold text-gray-900">{{ $v->merchant_name ?? '&#x2014;' }}</div>
                            <div class="text-xs text-gray-400">ID: {{ $v->merchant_id }}</div>
                        </td>
                        <td class="text-sm text-gray-700">{{ optional($v->employee)->full_name ?? $v->employee_id }}</td>
                        <td>
                            <span class="text-xs font-medium text-gray-600">{{ $v->assignment_id ?? '&#x2014;' }}</span>
                        </td>
                        <td>
                            @php $completeTerminal = $v->getCompleteTerminalInfo(); @endphp
                            @if(!empty($completeTerminal))
                                <span class="badge badge-green">1 Terminal</span>
                                <details class="mt-1 text-xs">
                                    <summary class="cursor-pointer text-[#1a3a5c] hover:underline select-none">View Details</summary>
                                    <div class="mt-1 bg-gray-50 border border-gray-200 rounded p-2 space-y-1">
                                        <div><span class="text-gray-500">Terminal ID:</span> <span class="font-medium">{{ $completeTerminal['terminal_id'] ?? '&#x2014;' }}</span></div>
                                        <div><span class="text-gray-500">Status:</span> <span class="font-medium">{{ $completeTerminal['status'] ?? ($completeTerminal['current_status'] ?? '&#x2014;') }}</span></div>
                                        <div><span class="text-gray-500">Condition:</span> <span class="font-medium">{{ $completeTerminal['condition_status'] ?? $completeTerminal['condition'] ?? '&#x2014;' }}</span></div>
                                        <div><span class="text-gray-500">Model:</span> <span class="font-medium">{{ $completeTerminal['terminal_model'] ?? '&#x2014;' }}</span></div>
                                        <div><span class="text-gray-500">Serial:</span> <span class="font-medium">{{ $completeTerminal['serial_number'] ?? '&#x2014;' }}</span></div>
                                        @if(!empty($completeTerminal['issues']))
                                            <div><span class="text-gray-500">Issues:</span> <span class="text-red-600 font-medium">{{ $completeTerminal['issues'] }}</span></div>
                                        @endif
                                    </div>
                                </details>
                            @else
                                <span class="badge badge-gray">No Terminal</span>
                            @endif
                            @if(count($otherTerminals) > 0)
                                <span class="badge badge-orange mt-1">+{{ count($otherTerminals) }} Other</span>
                            @endif
                        </td>
                        <td class="max-w-xs">
                            <div class="text-sm text-gray-700 leading-snug">{{ \Illuminate\Support\Str::limit($v->visit_summary, 120) }}</div>
                            @if(!empty($v->action_points))
                                <div class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($v->action_points, 100) }}</div>
                            @endif
                        </td>
                        <td>
                            @if(count($evidence))
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-[#1a3a5c] hover:underline select-none">&#x1F4CE; {{ count($evidence) }} {{ count($evidence) === 1 ? 'File' : 'Files' }}</summary>
                                    <div class="mt-1 bg-gray-50 border border-gray-200 rounded p-2 space-y-1">
                                        @foreach($evidence as $idx => $item)
                                            <div>
                                                @if(\Illuminate\Support\Str::startsWith($item, ['http://', 'https://', '/storage/']))
                                                    <a href="{{ $item }}" target="_blank" rel="noopener" class="text-[#1a3a5c] hover:underline">&#x1F4CE; Evidence {{ $idx + 1 }}</a>
                                                @else
                                                    <span class="text-gray-500">&#x1F4C4; {{ \Illuminate\Support\Str::limit($item, 35) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <span class="text-xs text-gray-400">No evidence</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('visits.show', $v) }}" class="btn-secondary btn-sm">ðŸ‘ View</a>
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
  const merchantList = document.getElementById('merchant-list');
  const employeeList = document.getElementById('employee-list');

  let mTimer = null, eTimer = null;

  function debounce(key, fn, delay){
    if (key==='m') { clearTimeout(mTimer); mTimer=setTimeout(fn, delay); }
    if (key==='e') { clearTimeout(eTimer); eTimer=setTimeout(fn, delay); }
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
})();
</script>
@endpush
@endsection