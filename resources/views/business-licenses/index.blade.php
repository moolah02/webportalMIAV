{{-- resources/views/business-licenses/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;">
        <div>
            <h2 style="margin:0;color:#222;font-weight:600;">
                {{ $direction === 'company_held' ? 'Business Licenses' : 'Customer Licenses' }}
            </h2>
            <p style="color:#6b7280;margin:6px 0 0 0;font-size:14px;">
                {{ $direction === 'company_held'
                    ? 'Repository of licenses and compliance records'
                    : 'Repository of customer-issued licenses' }}
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('business-licenses.compliance', ['direction' => $direction]) }}" class="btn btn-ghost">Compliance</a>
            <a href="{{ route('business-licenses.expiring', ['direction' => $direction]) }}" class="btn btn-ghost">Expiring</a>
            <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn btn-primary">Add {{ $direction === 'company_held' ? 'License' : 'Customer License' }}</a>
        </div>
    </div>

    {{-- Direction toggle --}}
    <div style="margin-bottom:20px;">
        <div style="display:inline-flex;gap:0;">
            <a href="{{ route('business-licenses.index', ['direction' => 'company_held']) }}"
               class="seg {{ $direction === 'company_held' ? 'seg--active' : '' }}">Our Licenses</a>
            <a href="{{ route('business-licenses.index', ['direction' => 'customer_issued']) }}"
               class="seg {{ $direction === 'customer_issued' ? 'seg--active' : '' }}">Customer Licenses</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom:16px;padding:16px;">
        <form id="filter-form" method="GET" style="display:grid;grid-template-columns:2fr repeat(4,1fr) auto auto;gap:12px;align-items:center;">
            <input type="hidden" name="direction" value="{{ $direction }}" id="current-direction">

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ $direction === 'company_held' ? 'Search licenses…' : 'Search licenses or customers…' }}"
                   class="input" id="search-input">

            <select name="status" class="input" id="status-filter">
                <option value="">All Status</option>
                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="license_type" class="input" id="type-filter">
                <option value="">All Types</option>
                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                    <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <div id="company-filters" style="display: {{ $direction === 'company_held' ? 'contents' : 'none' }};">
                <select name="priority" class="input" id="priority-filter">
                    <option value="">All Priority</option>
                    @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="department" class="input" id="department-filter">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="customer-filters" style="display: {{ $direction === 'customer_issued' ? 'contents' : 'none' }};">
                <select name="billing_cycle" class="input" id="billing-filter">
                    <option value="">All Billing</option>
                    @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                        <option value="{{ $key }}" {{ request('billing_cycle') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="support_level" class="input" id="support-filter">
                    <option value="">All Support</option>
                    @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ request('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-ghost">Filter</button>

            @if(request()->hasAny(['search', 'status', 'license_type', 'priority', 'department', 'billing_cycle', 'support_level']))
                <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn btn-ghost" id="clear-filters">Clear</a>
            @endif
        </form>
    </div>

    {{-- Stats (neutral, no icons / colors) --}}
    <div id="stats-cards" class="stats-grid" style="margin-bottom:20px;">
        @if($direction === 'company_held')
            <div class="stat">
                <div class="stat__value" id="active-count">{{ $stats['active_licenses'] }}</div>
                <div class="stat__label">Active Licenses</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                <div class="stat__label">Expiring Soon</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                <div class="stat__label">Expired</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="annual-cost">${{ number_format($stats['total_annual_cost'], 0) }}</div>
                <div class="stat__label">Annual Cost</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="critical-count">{{ $stats['critical_licenses'] }}</div>
                <div class="stat__label">Critical Priority</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="total-count">{{ $stats['total_licenses'] }}</div>
                <div class="stat__label">Total Licenses</div>
            </div>
        @else
            <div class="stat">
                <div class="stat__value" id="active-count">{{ $stats['active_licenses'] }}</div>
                <div class="stat__label">Active Licenses</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="revenue-amount">${{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="stat__label">Annual Revenue</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="customers-count">{{ $stats['unique_customers'] }}</div>
                <div class="stat__label">Unique Customers</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                <div class="stat__label">Expiring Soon</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                <div class="stat__label">Expired</div>
            </div>
            <div class="stat">
                <div class="stat__value" id="total-count">{{ $stats['total_licenses'] }}</div>
                <div class="stat__label">Total Licenses</div>
            </div>
        @endif
    </div>

    {{-- Loading (no spinner/animation) --}}
    <div id="loading-indicator" style="display:none;text-align:center;padding:12px;margin-bottom:12px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;">
        Updating statistics…
    </div>

    {{-- Table --}}
    <div class="card" style="padding:0;overflow:hidden;">
        @if($licenses->count() > 0)
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>License Details</th>
                            @if($direction === 'company_held')
                                <th>Department</th>
                                <th>Responsible</th>
                                <th>Priority</th>
                            @else
                                <th>Customer</th>
                                <th>Revenue</th>
                                <th>Support</th>
                            @endif
                            <th>Status</th>
                            <th>Dates</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($licenses as $license)
                            <tr>
                                {{-- License details --}}
                                <td>
                                    <div class="t-title">{{ $license->license_name }}</div>
                                    <div class="t-sub">{{ $license->license_number }}</div>
                                    <div class="t-hint">{{ $license->license_type_name }}</div>
                                    @if($license->description)
                                        <div class="t-note">{{ Str::limit($license->description, 100) }}</div>
                                    @endif
                                </td>

                                @if($license->isCompanyHeld())
                                    <td>
                                        <div class="t-title">{{ $license->department->name ?? 'Unassigned' }}</div>
                                        @if($license->location)
                                            <div class="t-hint">{{ $license->location }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="t-title">{{ $license->responsibleEmployee->full_name ?? 'Unassigned' }}</div>
                                        @if($license->responsibleEmployee?->email)
                                            <div class="t-hint">{{ $license->responsibleEmployee->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge">{{ $license->priority_level_name }}</span>
                                        @if($license->renewal_cost)
                                            <div class="t-hint">${{ number_format($license->renewal_cost, 0) }}/yr</div>
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        <div class="t-title">{{ $license->customer_display_name }}</div>
                                        <div class="t-hint">{{ $license->customer_email }}</div>
                                        @if($license->license_quantity)
                                            <div class="t-hint">Qty: {{ $license->license_quantity }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="t-title">${{ number_format($license->revenue_amount ?? 0, 0) }}</div>
                                        <div class="t-hint">{{ $license->billing_cycle_name }}</div>
                                        @if($license->annual_revenue)
                                            <div class="t-hint">${{ number_format($license->annual_revenue, 0) }}/yr</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge">{{ $license->support_level_name }}</span>
                                        @if($license->usage_limit)
                                            <div class="t-hint">{{ Str::limit($license->usage_limit, 30) }}</div>
                                        @endif
                                    </td>
                                @endif

                                {{-- Status --}}
                                <td>
                                    <span class="badge">{{ $license->status_name }}</span>
                                    @if($license->is_expired)
                                        <div class="t-danger">{{ abs((int)$license->days_until_expiry) }} days overdue</div>
                                    @elseif($license->is_expiring_soon)
                                        <div class="t-warn">{{ (int)$license->days_until_expiry }} days left</div>
                                    @endif
                                </td>

                                {{-- Dates --}}
                                <td>
                                    <div class="t-hint"><strong>Issued:</strong> {{ $license->issue_date ? $license->issue_date->format('M d, Y') : 'N/A' }}</div>
                                    <div class="t-hint"><strong>Expires:</strong> {{ $license->expiry_date ? $license->expiry_date->format('M d, Y') : 'N/A' }}</div>
                                    @if($license->renewal_date)
                                        <div class="t-hint"><strong>Renewed:</strong> {{ $license->renewal_date->format('M d, Y') }}</div>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td style="text-align:center;">
                                    <div style="display:flex;flex-direction:column;gap:6px;min-width:100px;">
                                        <a href="{{ route('business-licenses.show', $license) }}" class="btn btn-ghost">View</a>

                                        @if($license->canRenew() && ($license->is_expired || $license->is_expiring_soon))
                                            <a href="{{ route('business-licenses.renew', $license) }}" class="btn btn-outline">Renew</a>
                                        @endif

                                        <a href="{{ route('business-licenses.edit', $license) }}" class="btn btn-primary">Edit</a>

                                        @if($license->document_path)
                                            <a href="{{ route('business-licenses.download', $license) }}" class="btn btn-outline">Document</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align:center;padding:48px;">
                <h3 style="color:#374151;margin-bottom:8px;font-weight:600;">No records found</h3>
                <p style="color:#6b7280;margin-bottom:16px;">Start by adding a {{ $direction === 'company_held' ? 'business license' : 'customer license' }}.</p>
                <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn btn-primary">
                    Add {{ $direction === 'company_held' ? 'License' : 'Customer License' }}
                </a>
            </div>
        @endif
    </div>

    {{-- Pagination --}}
    @if($licenses->hasPages())
        <div style="margin-top:16px;">
            {{ $licenses->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<style>
/* Base */
.card{background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:none}
.input{padding:8px 10px;border:1px solid #d1d5db;border-radius:4px;background:#fff;color:#111827;font-size:14px}
.input:focus{outline:none;border-color:#9ca3af}

/* Buttons */
.btn{padding:8px 12px;border:1px solid #d1d5db;border-radius:4px;background:#fff;color:#111827;text-decoration:none;display:inline-block;font-size:14px}
.btn-ghost{background:#fff}
.btn-outline{background:#fff}
.btn-primary{background:#111827;color:#fff;border-color:#111827}
.btn:hover{filter:none} /* kill hover animations */

/* Segmented control */
.seg{padding:8px 12px;border:1px solid #d1d5db;border-right:none;background:#f9fafb;color:#111827;text-decoration:none}
.seg:last-child{border-right:1px solid #d1d5db;border-top-right-radius:6px;border-bottom-right-radius:6px}
.seg:first-child{border-top-left-radius:6px;border-bottom-left-radius:6px}
.seg--active{background:#fff}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
.stat{border:1px solid #e5e7eb;border-radius:6px;padding:12px;background:#fff}
.stat__value{font-size:22px;font-weight:600;color:#111827}
.stat__label{font-size:12px;color:#6b7280;margin-top:4px}

/* Table */
.table{width:100%;border-collapse:collapse;font-size:14px}
.table th{background:#f9fafb;text-align:left;padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:600}
.table td{padding:12px;vertical-align:top;border-bottom:1px solid #f3f4f6}
.table tbody tr:nth-child(even){background:#fcfcfc} /* subtle zebra */
.table tbody tr:hover{background:transparent}       /* remove hover color */

/* Text helpers */
.t-title{font-weight:600;color:#111827}
.t-sub{font-size:12px;color:#6b7280;margin-top:2px}
.t-hint{font-size:12px;color:#6b7280;margin-top:2px}
.t-note{font-size:12px;color:#4b5563;margin-top:6px;max-width:360px}
.t-danger{font-size:12px;color:#7f1d1d;margin-top:4px}
.t-warn{font-size:12px;color:#92400e;margin-top:4px}

/* Badges (neutral) */
.badge{display:inline-block;padding:2px 8px;border:1px solid #d1d5db;border-radius:999px;font-size:12px;color:#374151;background:#fff}

/* Responsive */
@media (max-width: 1200px){
    #filter-form{grid-template-columns:1fr;gap:10px}
    #company-filters,#customer-filters{display:grid !important;grid-template-columns:1fr 1fr;gap:10px}
    .table{font-size:13px}
    .table th,.table td{padding:10px 8px}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterInputs = document.querySelectorAll('#filter-form select, #filter-form input[type="text"]');
    const loadingIndicator = document.getElementById('loading-indicator');
    const statsCards = document.getElementById('stats-cards');

    filterInputs.forEach(input => {
        input.addEventListener('change', updateStats);
        if (input.type === 'text') {
            let timeout;
            input.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(updateStats, 500);
            });
        }
    });

    function updateStats() {
        loadingIndicator.style.display = 'block';
        statsCards.style.opacity = '0.7';

        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams(formData);

        fetch(`{{ route('business-licenses.filtered-stats') }}?${params}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) updateStatsCards(data.stats, data.direction);
            })
            .catch(console.error)
            .finally(() => {
                loadingIndicator.style.display = 'none';
                statsCards.style.opacity = '1';
            });
    }

    function updateStatsCards(stats, direction) {
        document.getElementById('active-count').textContent = stats.active_licenses;
        document.getElementById('expiring-count').textContent = stats.expiring_soon;
        document.getElementById('expired-count').textContent = stats.expired_licenses;
        document.getElementById('total-count').textContent = stats.total_licenses;

        if (direction === 'company_held') {
            document.getElementById('annual-cost').textContent = '$' + numberFormat(stats.total_annual_cost);
            document.getElementById('critical-count').textContent = stats.critical_licenses;
        } else {
            document.getElementById('revenue-amount').textContent = '$' + numberFormat(stats.total_revenue);
            document.getElementById('customers-count').textContent = stats.unique_customers;
        }
    }

    function numberFormat(num) {
        return new Intl.NumberFormat().format(num);
    }
});
</script>
@endsection
