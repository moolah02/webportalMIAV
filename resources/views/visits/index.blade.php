@extends('layouts.app')

@section('content')
<div class="visits-container">
  <div class="container-fluid py-4 px-4">
    {{-- Header Section --}}
    <div class="page-header mb-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="page-title mb-2">Site Visit Management</h2>
          <p class="page-subtitle mb-0">Comprehensive field visit tracking and reporting</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light border btn-back">
          <i class="fas fa-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    {{-- Filters Section - Single Row --}}
    <div class="filter-card mb-4">
      <div class="card-header-custom">
        <i class="fas fa-filter me-2"></i>
        <span>Advanced Filters</span>
      </div>
      <div class="card-body p-4">
        <form id="visits-filter" method="GET" action="{{ route('visits.index') }}">
          {{-- Single Row Filter Layout --}}
          <div class="filters-row">
            {{-- Merchant Filter --}}
            <div class="filter-item">
              <label class="form-label-custom">
                <i class="fas fa-store me-2"></i>Merchant
              </label>
              <input id="merchant"
                    name="merchant"
                    value="{{ request('merchant') }}"
                    class="form-control-custom"
                    placeholder="Search merchant..."
                    list="merchant-list"
                    autocomplete="off">
              <datalist id="merchant-list"></datalist>
            </div>

            {{-- Employee Filter --}}
            <div class="filter-item">
              <label class="form-label-custom">
                <i class="fas fa-user me-2"></i>Employee
              </label>
              <input id="employee"
                    name="employee"
                    value="{{ request('employee') }}"
                    class="form-control-custom"
                    placeholder="Search employee..."
                    list="employee-list"
                    autocomplete="off">
              <datalist id="employee-list"></datalist>
            </div>

            {{-- Date From --}}
            <div class="filter-item">
              <label class="form-label-custom">
                <i class="fas fa-calendar-alt me-2"></i>From Date
              </label>
              <input type="date"
                    name="dateFrom"
                    value="{{ request('dateFrom') }}"
                    class="form-control-custom">
            </div>

            {{-- Date To --}}
            <div class="filter-item">
              <label class="form-label-custom">
                <i class="fas fa-calendar-check me-2"></i>To Date
              </label>
              <input type="date"
                    name="dateTo"
                    value="{{ request('dateTo') }}"
                    class="form-control-custom">
            </div>

            {{-- General Search --}}
            <div class="filter-item">
              <label class="form-label-custom">
                <i class="fas fa-search me-2"></i>Search
              </label>
              <input type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="form-control-custom"
                    placeholder="Keywords...">
            </div>
          </div>

          <div class="filter-actions mt-4">
            <button type="submit" class="btn btn-primary-custom">
              <i class="fas fa-filter me-2"></i>Apply Filters
            </button>
            <a href="{{ route('visits.index') }}" class="btn btn-secondary-custom">
              <i class="fas fa-redo me-2"></i>Reset All
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- Results Section --}}
    @if($visits->isEmpty())
      <div class="empty-state">
        <div class="empty-state-icon">
          <i class="fas fa-search"></i>
        </div>
        <h4 class="empty-state-title">No Visits Found</h4>
        <p class="empty-state-text">No records match your current filter criteria. Try adjusting your search parameters.</p>
      </div>
    @else
      <div class="results-card">
        <div class="results-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="results-title mb-1">Visit Records</h5>
              <p class="results-count mb-0">Displaying {{ $visits->count() }} {{ $visits->count() === 1 ? 'record' : 'records' }}</p>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table-custom">
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
                    <span class="id-badge">{{ $v->id }}</span>
                  </td>

                  <td>
                    @if($v->completed_at)
                      <div class="date-time">
                        <div class="date">{{ $v->completed_at->format('M j, Y') }}</div>
                        <div class="time">{{ $v->completed_at->format('H:i') }}</div>
                      </div>
                    @else
                      <span class="text-muted-custom">Not completed</span>
                    @endif
                  </td>

                  <td>
                    <div class="merchant-info">
                      <div class="merchant-name">{{ $v->merchant_name ?? '—' }}</div>
                      <div class="merchant-id">ID: {{ $v->merchant_id }}</div>
                    </div>
                  </td>

                  <td>
                    <span class="employee-name">
                      {{ optional($v->employee)->full_name ?? $v->employee_id }}
                    </span>
                  </td>

                  <td>
                    <span class="assignment-id">{{ $v->assignment_id ?? '—' }}</span>
                  </td>

                  <td>
                    <div class="terminal-info">
                      @php $completeTerminal = $v->getCompleteTerminalInfo(); @endphp
                      @if(!empty($completeTerminal))
                        <span class="badge badge-success">1 Terminal</span>
                        <details class="terminal-details">
                          <summary>View Details</summary>
                          <div class="details-content">
                            <div class="detail-row">
                              <span class="detail-label">Terminal ID:</span>
                              <span class="detail-value">{{ $completeTerminal['terminal_id'] ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                              <span class="detail-label">Status:</span>
                              <span class="detail-value">{{ $completeTerminal['status'] ?? ($completeTerminal['current_status'] ?? '—') }}</span>
                            </div>
                            <div class="detail-row">
                              <span class="detail-label">Condition:</span>
                              <span class="detail-value">{{ $completeTerminal['condition_status'] ?? $completeTerminal['condition'] ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                              <span class="detail-label">Model:</span>
                              <span class="detail-value">{{ $completeTerminal['terminal_model'] ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                              <span class="detail-label">Serial:</span>
                              <span class="detail-value">{{ $completeTerminal['serial_number'] ?? '—' }}</span>
                            </div>
                            @if(!empty($completeTerminal['issues']))
                              <div class="detail-row">
                                <span class="detail-label">Issues:</span>
                                <span class="detail-value text-danger">{{ $completeTerminal['issues'] }}</span>
                              </div>
                            @endif
                          </div>
                        </details>
                      @else
                        <span class="badge badge-secondary">No Terminal</span>
                      @endif

                      @if(count($otherTerminals) > 0)
                        <span class="badge badge-warning mt-1">
                          +{{ count($otherTerminals) }} Other
                        </span>
                      @endif
                    </div>
                  </td>

                  <td style="max-width: 320px;">
                    <div class="summary-content">
                      <div class="summary-text">
                        {{ \Illuminate\Support\Str::limit($v->visit_summary, 120) }}
                      </div>
                      @if(!empty($v->action_points))
                        <div class="action-points">
                          <i class="fas fa-tasks me-1"></i>
                          <span>{{ \Illuminate\Support\Str::limit($v->action_points, 100) }}</span>
                        </div>
                      @endif
                    </div>
                  </td>

                  <td>
                    @if(count($evidence))
                      <details class="evidence-details">
                        <summary>
                          <i class="fas fa-paperclip me-1"></i>
                          {{ count($evidence) }} {{ count($evidence) === 1 ? 'File' : 'Files' }}
                        </summary>
                        <div class="details-content">
                          @foreach($evidence as $idx => $item)
                            <div class="evidence-item">
                              @if(\Illuminate\Support\Str::startsWith($item, ['http://', 'https://', '/storage/']))
                                <a href="{{ $item }}" target="_blank" rel="noopener">
                                  <i class="fas fa-external-link-alt me-1"></i>
                                  Evidence {{ $idx + 1 }}
                                </a>
                              @else
                                <span class="text-muted">
                                  <i class="fas fa-file me-1"></i>
                                  {{ \Illuminate\Support\Str::limit($item, 35) }}
                                </span>
                              @endif
                            </div>
                          @endforeach
                        </div>
                      </details>
                    @else
                      <span class="text-muted-custom">No evidence</span>
                    @endif
                  </td>

                  <td class="text-center">
                    <a href="{{ route('visits.show', $v) }}" class="btn btn-view">
                      <i class="fas fa-eye me-1"></i>View
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
</div>

<style>
:root {
  --primary-color: #3b82f6;
  --primary-hover: #2563eb;
  --secondary-color: #6b7280;
  --success-color: #10b981;
  --warning-color: #f59e0b;
  --border-color: #e5e7eb;
  --bg-light: #f9fafb;
  --text-dark: #111827;
  --text-muted: #6b7280;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.visits-container {
  background-color: var(--bg-light);
  min-height: 100vh;
}

/* Page Header */
.page-header {
  padding-bottom: 1.5rem;
  border-bottom: 2px solid var(--border-color);
}

.page-title {
  color: var(--text-dark);
  font-size: 1.75rem;
  font-weight: 600;
  letter-spacing: -0.025em;
}

.page-subtitle {
  color: var(--text-muted);
  font-size: 0.95rem;
}

.btn-back {
  padding: 0.5rem 1.25rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-back:hover {
  transform: translateX(-3px);
  box-shadow: var(--shadow-sm);
}

/* Filter Card */
.filter-card {
  background: white;
  border-radius: 10px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.card-header-custom {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
  color: white;
  padding: 1rem 1.5rem;
  font-weight: 600;
  font-size: 1rem;
  letter-spacing: 0.025em;
}

/* HORIZONTAL FILTER ROW */
.filters-row {
  display: flex;
  gap: 1rem;
  flex-wrap: nowrap;
  align-items: flex-end;
}

.filter-item {
  flex: 1;
  min-width: 0;
}

.form-label-custom {
  color: var(--text-dark);
  font-weight: 600;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
  display: block;
  white-space: nowrap;
}

.form-control-custom {
  display: block;
  width: 100%;
  padding: 0.625rem 0.875rem;
  font-size: 0.9375rem;
  font-weight: 400;
  line-height: 1.5;
  color: var(--text-dark);
  background-color: #fff;
  border: 1.5px solid var(--border-color);
  border-radius: 6px;
  transition: all 0.2s ease;
}

.form-control-custom:focus {
  border-color: var(--primary-color);
  outline: 0;
  box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
}

.form-control-custom::placeholder {
  color: var(--text-muted);
  opacity: 0.7;
}

.filter-actions {
  display: flex;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.btn-primary-custom {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 0.625rem 1.5rem;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.2s ease;
  font-size: 0.9375rem;
}

.btn-primary-custom:hover {
  background: var(--primary-hover);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-secondary-custom {
  background: white;
  color: var(--secondary-color);
  border: 1.5px solid var(--border-color);
  padding: 0.625rem 1.5rem;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.2s ease;
  font-size: 0.9375rem;
}

.btn-secondary-custom:hover {
  background: var(--bg-light);
  border-color: var(--secondary-color);
}

/* Empty State */
.empty-state {
  background: white;
  border-radius: 10px;
  padding: 4rem 2rem;
  text-align: center;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
}

.empty-state-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  background: var(--bg-light);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: var(--text-muted);
}

.empty-state-title {
  color: var(--text-dark);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.empty-state-text {
  color: var(--text-muted);
  font-size: 0.9375rem;
  margin-bottom: 0;
}

/* Results Card */
.results-card {
  background: white;
  border-radius: 10px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.results-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 2px solid var(--border-color);
  background: var(--bg-light);
}

.results-title {
  color: var(--text-dark);
  font-weight: 600;
  font-size: 1.125rem;
}

.results-count {
  color: var(--text-muted);
  font-size: 0.875rem;
}

/* Table Styles */
.table-custom {
  width: 100%;
  margin-bottom: 0;
  border-collapse: separate;
  border-spacing: 0;
}

.table-custom thead {
  background: var(--bg-light);
}

.table-custom thead th {
  padding: 1rem 1.25rem;
  font-weight: 600;
  font-size: 0.8125rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted);
  border-bottom: 2px solid var(--border-color);
  white-space: nowrap;
}

.table-custom tbody tr {
  transition: all 0.2s ease;
  border-bottom: 1px solid var(--border-color);
}

.table-custom tbody tr:hover {
  background: var(--bg-light);
}

.table-custom tbody td {
  padding: 1.25rem 1.25rem;
  vertical-align: top;
  font-size: 0.9375rem;
  color: var(--text-dark);
}

/* ID Badge */
.id-badge {
  display: inline-block;
  background: var(--primary-color);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8125rem;
}

/* Date & Time */
.date-time {
  line-height: 1.4;
}

.date-time .date {
  font-weight: 600;
  color: var(--text-dark);
  font-size: 0.9375rem;
}

.date-time .time {
  color: var(--text-muted);
  font-size: 0.8125rem;
  margin-top: 0.125rem;
}

/* Merchant Info */
.merchant-info {
  line-height: 1.4;
}

.merchant-name {
  font-weight: 600;
  color: var(--text-dark);
  font-size: 0.9375rem;
}

.merchant-id {
  color: var(--text-muted);
  font-size: 0.8125rem;
  margin-top: 0.125rem;
}

/* Employee Name */
.employee-name {
  font-weight: 500;
  color: var(--text-dark);
}

/* Assignment ID */
.assignment-id {
  font-family: 'Courier New', monospace;
  color: var(--text-muted);
  font-size: 0.875rem;
}

/* Badges */
.badge {
  display: inline-block;
  padding: 0.35rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  line-height: 1;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.badge-success {
  background: #10b981;
  color: white;
}

.badge-secondary {
  background: #6b7280;
  color: white;
}

.badge-warning {
  background: #f59e0b;
  color: white;
}

/* Details Elements */
details summary {
  cursor: pointer;
  user-select: none;
  list-style: none;
  outline: none;
  color: var(--primary-color);
  font-weight: 500;
  font-size: 0.8125rem;
  padding: 0.25rem 0;
  transition: color 0.2s ease;
}

details summary:hover {
  color: var(--primary-hover);
}

details summary::-webkit-details-marker {
  display: none;
}

details summary::after {
  content: '▼';
  font-size: 0.625rem;
  margin-left: 0.375rem;
  transition: transform 0.2s ease;
  display: inline-block;
}

details[open] summary::after {
  transform: rotate(180deg);
}

.details-content {
  margin-top: 0.75rem;
  padding: 1rem;
  background: var(--bg-light);
  border-radius: 6px;
  border: 1px solid var(--border-color);
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 0.375rem 0;
  border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: var(--text-muted);
  font-size: 0.8125rem;
}

.detail-value {
  font-weight: 500;
  color: var(--text-dark);
  font-size: 0.8125rem;
  text-align: right;
}

/* Terminal Info */
.terminal-info {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.terminal-details {
  margin-top: 0.25rem;
}

/* Summary Content */
.summary-content {
  line-height: 1.5;
}

.summary-text {
  color: var(--text-dark);
  font-size: 0.9375rem;
}

.action-points {
  margin-top: 0.625rem;
  padding-top: 0.625rem;
  border-top: 1px solid var(--border-color);
  color: var(--text-muted);
  font-size: 0.875rem;
  font-style: italic;
}

.action-points i {
  color: var(--primary-color);
}

/* Evidence Details */
.evidence-details summary {
  display: inline-flex;
  align-items: center;
}

.evidence-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-color);
}

.evidence-item:last-child {
  border-bottom: none;
}

.evidence-item a {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.875rem;
  transition: color 0.2s ease;
}

.evidence-item a:hover {
  color: var(--primary-hover);
  text-decoration: underline;
}

/* View Button */
.btn-view {
  background: white;
  color: var(--primary-color);
  border: 1.5px solid var(--primary-color);
  padding: 0.5rem 1rem;
  font-weight: 600;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.btn-view:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

/* Text Utilities */
.text-muted-custom {
  color: var(--text-muted);
  font-style: italic;
  font-size: 0.875rem;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
  .filters-row {
    flex-wrap: wrap;
  }

  .filter-item {
    flex: 1 1 calc(50% - 0.5rem);
    min-width: 200px;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: 1.5rem;
  }

  .filters-row {
    flex-direction: column;
  }

  .filter-item {
    flex: 1 1 100%;
  }

  .filter-actions {
    flex-direction: column;
  }

  .btn-primary-custom,
  .btn-secondary-custom {
    width: 100%;
  }

  .table-custom {
    font-size: 0.875rem;
  }

  .table-custom thead th,
  .table-custom tbody td {
    padding: 0.875rem;
  }
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
