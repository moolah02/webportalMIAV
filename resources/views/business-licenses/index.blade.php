{{-- resources/views/business-licenses/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Business Licenses')

@section('content')
<div>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;">
        <div>
            <h2 style="margin:0;color:#222;font-weight:600;">
                @if($direction === 'company_held') Business Licenses
                @elseif($direction === 'customer_issued') Customer Licenses
                @else All Licenses — History & Lookup
                @endif
            </h2>
            <p style="color:#6b7280;margin:6px 0 0 0;font-size:14px;">
                @if($direction === 'company_held') Repository of licenses and compliance records
                @elseif($direction === 'customer_issued') Repository of customer-issued licenses
                @else Complete record of all issued and held licenses
                @endif
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            @if($direction !== 'all')
                <a href="{{ route('business-licenses.compliance', ['direction' => $direction]) }}" class="btn-secondary">Compliance</a>
                <a href="{{ route('business-licenses.expiring', ['direction' => $direction]) }}" class="btn-secondary">Expiring</a>
                <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn-primary">Add {{ $direction === 'company_held' ? 'License' : 'Customer License' }}</a>
            @endif
        </div>
    </div>

    {{-- Direction toggle --}}
    <div style="margin-bottom:20px;">
        <div style="display:inline-flex;gap:0;">
            <a href="{{ route('business-licenses.index', ['direction' => 'company_held']) }}"
               class="seg {{ $direction === 'company_held' ? 'seg--active' : '' }}">Our Licenses</a>
            <a href="{{ route('business-licenses.index', ['direction' => 'customer_issued']) }}"
               class="seg {{ $direction === 'customer_issued' ? 'seg--active' : '' }}">Customer Licenses</a>
            <a href="{{ route('business-licenses.index', ['direction' => 'all']) }}"
               class="seg {{ $direction === 'all' ? 'seg--active' : '' }}">All / History</a>
        </div>
    </div>

    {{-- Stats --}}
    <div id="stats-cards" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @if($direction === 'company_held')
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">✅</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="active-count">{{ $stats['active_licenses'] }}</div>
                    <div class="stat-label">Active Licenses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">⚠️</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                    <div class="stat-label">Expiring Soon</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">❌</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">💰</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="annual-cost">${{ number_format($stats['total_annual_cost'], 0) }}</div>
                    <div class="stat-label">Annual Cost</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">🚨</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="critical-count">{{ $stats['critical_licenses'] }}</div>
                    <div class="stat-label">Critical Priority</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📋</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="total-count">{{ $stats['total_licenses'] }}</div>
                    <div class="stat-label">Total Licenses</div>
                </div>
            </div>
        @elseif($direction === 'customer_issued')
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">✅</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="active-count">{{ $stats['active_licenses'] }}</div>
                    <div class="stat-label">Active Licenses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">💵</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="revenue-amount">${{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="stat-label">Annual Revenue</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">👥</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="customers-count">{{ $stats['unique_customers'] }}</div>
                    <div class="stat-label">Unique Customers</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">⚠️</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                    <div class="stat-label">Expiring Soon</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">❌</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📋</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number" id="total-count">{{ $stats['total_licenses'] }}</div>
                    <div class="stat-label">Total Licenses</div>
                </div>
            </div>
        @else
            {{-- All / History --}}
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📋</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number">{{ $stats['total_licenses'] }}</div>
                    <div class="stat-label">Total Records</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">✅</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number">{{ $stats['active_licenses'] }}</div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">❌</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number">{{ $stats['expired_licenses'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">⚠️</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number">{{ $stats['expiring_soon'] }}</div>
                    <div class="stat-label">Expiring Soon</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">🏢</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number">{{ $stats['company_held'] }}</div>
                    <div class="stat-label">Company-Held</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">👤</div>
                <div class="flex-1 min-w-0">
                    <div class="stat-number">{{ $stats['customer_issued'] }}</div>
                    <div class="stat-label">Customer-Issued</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="ui-card mb-4 p-4">
        <form id="filter-form" method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="hidden" name="direction" value="{{ $direction }}" id="current-direction">

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ $direction === 'company_held' ? 'Search licenses…' : 'Search licenses or customers…' }}"
                   class="ui-input flex-1 min-w-48" id="search-input">

            <select name="status" class="ui-select w-auto" id="status-filter">
                <option value="">All Status</option>
                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="license_type" class="ui-select w-auto" id="type-filter">
                <option value="">All Types</option>
                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                    <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            @if($direction === 'company_held')
                <select name="priority" class="ui-select w-auto" id="priority-filter">
                    <option value="">All Priority</option>
                    @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="department" class="ui-select w-auto" id="department-filter">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            @elseif($direction === 'customer_issued')
                <select name="billing_cycle" class="ui-select w-auto" id="billing-filter">
                    <option value="">All Billing</option>
                    @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                        <option value="{{ $key }}" {{ request('billing_cycle') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="support_level" class="ui-select w-auto" id="support-filter">
                    <option value="">All Support</option>
                    @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ request('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="btn-secondary">Filter</button>
            @if(request()->hasAny(['search', 'status', 'license_type', 'priority', 'department', 'billing_cycle', 'support_level']))
                <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn-secondary" id="clear-filters">Clear</a>
            @endif
        </form>
    </div>

    {{-- Loading (no spinner/animation) --}}
    <div id="loading-indicator" style="display:none;text-align:center;padding:12px;margin-bottom:12px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;">
        Updating statistics…
    </div>

    {{-- Table --}}
    <div class="ui-card" style="padding:0;overflow:hidden;">
        @if($licenses->count() > 0)
            <div style="overflow-x:auto;">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>License Details</th>
                            @if($direction === 'company_held')
                                <th>Department</th>
                                <th>Responsible</th>
                                <th>Priority</th>
                            @elseif($direction === 'customer_issued')
                                <th>Customer</th>
                                <th>Revenue</th>
                                <th>Support</th>
                            @else
                                <th>Type</th>
                                <th>Dept / Customer</th>
                                <th>Issuing Authority</th>
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

                                @if($direction === 'all')
                                    <td>
                                        <span class="badge badge-gray">{{ $license->isCompanyHeld() ? 'Company' : 'Customer' }}</span>
                                    </td>
                                    <td>
                                        @if($license->isCompanyHeld())
                                            <div class="t-title">{{ $license->department->name ?? 'Unassigned' }}</div>
                                            <div class="t-hint">{{ $license->responsibleEmployee->full_name ?? '' }}</div>
                                        @else
                                            <div class="t-title">{{ $license->customer_display_name }}</div>
                                            <div class="t-hint">{{ $license->customer_email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="t-hint">{{ $license->issuing_authority }}</div>
                                    </td>
                                @elseif($license->isCompanyHeld())
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
                                        <span class="badge badge-gray">{{ $license->priority_level_name }}</span>
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
                                        <span class="badge badge-gray">{{ $license->support_level_name }}</span>
                                        @if($license->usage_limit)
                                            <div class="t-hint">{{ Str::limit($license->usage_limit, 30) }}</div>
                                        @endif
                                    </td>
                                @endif

                                {{-- Status --}}
                                <td>
                                    <span class="badge badge-gray">{{ $license->status_name }}</span>
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
                                        <a href="{{ route('business-licenses.show', $license) }}" class="btn-secondary">View</a>

                                        @if($license->canRenew() && ($license->is_expired || $license->is_expiring_soon))
                                            <a href="{{ route('business-licenses.renew', $license) }}" class="btn-secondary">Renew</a>
                                        @endif

                                        <a href="{{ route('business-licenses.edit', $license) }}" class="btn-primary">Edit</a>

                                        @if($license->document_path)
                                            <a href="{{ route('business-licenses.download', $license) }}" class="btn-secondary">Document</a>
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
                <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn-primary">
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
