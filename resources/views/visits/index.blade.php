@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 text-dark">Site Visits</h4>
      <p class="text-muted mb-0 small">Manage and track field visit records</p>
    </div>
    <a href="{{ url()->previous() }}"
      class="btn btn-outline-secondary btn-sm px-3">
      ← Back
    </a>
  </div>

  {{-- Filters --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form id="visits-filter" method="GET" action="{{ route('visits.index') }}">
        <div class="row g-3">
          {{-- Merchant name (type-ahead) --}}
          <div class="col-md-3">
            <label class="form-label small fw-medium text-muted">
              Merchant
            </label>
            <input id="merchant"
                  name="merchant"
                  value="{{ request('merchant') }}"
                  class="form-control form-control-sm"
                  placeholder="Start typing…"
                  list="merchant-list"
                  autocomplete="off">
            <datalist id="merchant-list"></datalist>
          </div>

          {{-- Employee name (type-ahead) --}}
          <div class="col-md-3">
            <label class="form-label small fw-medium text-muted">
              Employee
            </label>
            <input id="employee"
                  name="employee"
                  value="{{ request('employee') }}"
                  class="form-control form-control-sm"
                  placeholder="Start typing…"
                  list="employee-list"
                  autocomplete="off">
            <datalist id="employee-list"></datalist>
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-medium text-muted">
              From Date
            </label>
            <input type="date"
                  name="dateFrom"
                  value="{{ request('dateFrom') }}"
                  class="form-control form-control-sm">
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-medium text-muted">
              To Date
            </label>
            <input type="date"
                  name="dateTo"
                  value="{{ request('dateTo') }}"
                  class="form-control form-control-sm">
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-medium text-muted">
              Search
            </label>
            <input type="text"
                  name="q"
                  value="{{ request('q') }}"
                  class="form-control form-control-sm"
                  placeholder="merchant, summary, action...">
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-dark btn-sm">Filter</button>
          <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Results --}}
  @if($visits->isEmpty())
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <div class="text-muted">
          <i class="fas fa-search fa-2x mb-3"></i>
          <h6>No visits found</h6>
          <p class="small mb-0">Try adjusting your filters</p>
        </div>
      </div>
    </div>
  @else
    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead class="bg-light">
            <tr class="border-0">
              <th class="fw-medium text-muted small px-3 py-3">ID</th>
              <th class="fw-medium text-muted small px-3 py-3">Completed</th>
              <th class="fw-medium text-muted small px-3 py-3">Merchant</th>
              <th class="fw-medium text-muted small px-3 py-3">Employee</th>
              <th class="fw-medium text-muted small px-3 py-3">Assignment</th>
              <th class="fw-medium text-muted small px-3 py-3">Terminal</th>
              <th class="fw-medium text-muted small px-3 py-3">Summary</th>
              <th class="fw-medium text-muted small px-3 py-3">Evidence</th>
              <th class="fw-medium text-muted small px-3 py-3"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($visits as $v)
              @php
                $terminal = is_array($v->terminal) ? $v->terminal : [];
                $evidence = is_array($v->evidence) ? $v->evidence : [];
                $otherTerminals = is_array($v->other_terminals_found) ? $v->other_terminals_found : [];
              @endphp
              <tr class="border-bottom border-light">
                <td class="px-3 py-3">
                  <span class="small fw-medium">{{ $v->id }}</span>
                </td>

                <td class="px-3 py-3">
                  @if($v->completed_at)
                    <div class="small">
                      {{ $v->completed_at->format('M j, Y') }}
                    </div>
                    <div class="small text-muted">
                      {{ $v->completed_at->format('H:i') }}
                    </div>
                  @else
                    <span class="small text-muted">—</span>
                  @endif
                </td>

                <td class="px-3 py-3">
                  <div class="fw-medium small">
                    {{ $v->merchant_name ?? '—' }}
                  </div>
                  <div class="small text-muted">
                    ID: {{ $v->merchant_id }}
                  </div>
                </td>

                <td class="px-3 py-3">
                  <span class="small">
                    {{ optional($v->employee)->full_name ?? $v->employee_id }}
                  </span>
                </td>

                <td class="px-3 py-3">
                  <span class="small">{{ $v->assignment_id ?? '—' }}</span>
                </td>

                <td class="px-3 py-3">
                  <div class="d-flex align-items-center">
                    @php $completeTerminal = $v->getCompleteTerminalInfo(); @endphp
                    @if(!empty($completeTerminal))
                      <span class="badge bg-success small">1</span>
                      <details class="ms-2">
                        <summary class="small text-muted text-decoration-none"
                                style="cursor: pointer;">
                          details
                        </summary>
                        <div class="mt-2 p-2 bg-light rounded-1">
                          <div class="small mb-2 pb-2">
                            <div class="fw-medium">
                              Terminal: {{ $completeTerminal['terminal_id'] ?? '—' }}
                            </div>
                            <div class="text-muted">
                              Status: {{ $completeTerminal['status'] ?? ($completeTerminal['current_status'] ?? '—') }} •
                              Condition: {{ $completeTerminal['condition_status'] ?? $completeTerminal['condition'] ?? '—' }}
                            </div>
                            <div class="text-muted">
                              Model: {{ $completeTerminal['terminal_model'] ?? '—' }} •
                              Serial: {{ $completeTerminal['serial_number'] ?? '—' }}
                            </div>
                            @if(!empty($completeTerminal['issues']))
                              <div class="text-muted fst-italic mt-1">
                                Issues: {{ $completeTerminal['issues'] }}
                              </div>
                            @endif
                          </div>
                        </div>
                      </details>
                    @else
                      <span class="badge bg-secondary small">0</span>
                    @endif

                    {{-- Show other terminals found if any --}}
                    @if(count($otherTerminals) > 0)
                      <span class="badge bg-warning small ms-1">
                        +{{ count($otherTerminals) }} other
                      </span>
                    @endif
                  </div>
                </td>

                <td class="px-3 py-3" style="max-width: 300px;">
                  <div class="small lh-sm">
                    {{ \Illuminate\Support\Str::limit($v->visit_summary, 100) }}
                  </div>

                  @if(!empty($v->action_points))
                    <div class="small text-muted mt-1">
                      <span class="fw-medium">Action:</span>
                      {{ \Illuminate\Support\Str::limit($v->action_points, 80) }}
                    </div>
                  @endif
                </td>

                <td class="px-3 py-3">
                  @if(count($evidence))
                    <details>
                      <summary class="small text-muted text-decoration-none"
                              style="cursor: pointer;">
                        {{ count($evidence) }} files
                      </summary>
                      <div class="mt-2 p-2 bg-light rounded-1">
                        @foreach($evidence as $idx => $item)
                          <div class="small mb-1">
                            @if(\Illuminate\Support\Str::startsWith($item, ['http://', 'https://', '/storage/']))
                              <a href="{{ $item }}"
                                target="_blank"
                                rel="noopener"
                                class="text-decoration-none">
                                Evidence {{ $idx + 1 }}
                              </a>
                            @else
                              <span class="text-muted">
                                Evidence {{ $idx + 1 }}:
                                {{ \Illuminate\Support\Str::limit($item, 30) }}
                              </span>
                            @endif
                          </div>
                        @endforeach
                      </div>
                    </details>
                  @else
                    <span class="small text-muted">None</span>
                  @endif
                </td>

                <td class="px-3 py-3">
                  <a href="{{ route('visits.show', $v) }}"
                    class="btn btn-outline-secondary btn-sm">
                    View
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>

<style>
.last-child-no-border > *:last-child {
  border-bottom: none !important;
}
details summary {
  list-style: none;
  outline: none;
}
details summary::-webkit-details-marker {
  display: none;
}
details summary::after {
  content: '▼';
  font-size: 0.75em;
  margin-left: 4px;
  transition: transform 0.2s;
}
details[open] summary::after {
  transform: rotate(180deg);
}
.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}
.form-control:focus {
  border-color: #6c757d;
  box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
}
</style>

{{-- Type-ahead suggestions + auto-filter --}}
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
    debounce('m', ()=>form.requestSubmit(), 400); // auto-apply
  });

  employeeInput.addEventListener('input', (e)=>{
    const q = e.target.value || '';
    debounce('e', ()=>suggestEmployees(q), 150);
    debounce('e', ()=>form.requestSubmit(), 400); // auto-apply
  });
})();
</script>
@endpush
@endsection
