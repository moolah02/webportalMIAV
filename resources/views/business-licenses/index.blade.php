{{-- File: resources/views/business-licenses/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">
                📋 {{ $direction === 'company_held' ? 'Our Business Licenses' : 'Customer Licenses' }}
            </h2>
            <p style="color: #666; margin: 5px 0 0 0;">
                {{ $direction === 'company_held' ? 'Manage and track business licenses and compliance' : 'Manage licenses issued to customers' }}
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('business-licenses.compliance', ['direction' => $direction]) }}" class="btn">📊 Compliance</a>
            <a href="{{ route('business-licenses.expiring', ['direction' => $direction]) }}" class="btn">⚠️ Expiring</a>
            <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn btn-primary">
                ➕ Add {{ $direction === 'company_held' ? 'License' : 'Customer License' }}
            </a>
        </div>
    </div>

    <!-- Direction Toggle -->
    <div style="margin-block-end: 30px;">
        <div style="display: flex; gap: 10px; padding: 4px; background: #f8f9fa; border-radius: 8px; width: fit-content;">
            <a href="{{ route('business-licenses.index', ['direction' => 'company_held']) }}" 
               class="btn {{ $direction === 'company_held' ? 'btn-primary' : '' }}" 
               style="border: none; {{ $direction === 'company_held' ? '' : 'background: transparent; color: #666;' }}">
                🏢 Our Licenses
            </a>
            <a href="{{ route('business-licenses.index', ['direction' => 'customer_issued']) }}" 
               class="btn {{ $direction === 'customer_issued' ? 'btn-primary' : '' }}"
               style="border: none; {{ $direction === 'customer_issued' ? '' : 'background: transparent; color: #666;' }}">
                👥 Customer Licenses
            </a>
        </div>
    </div>

    <!-- Filters (Now Above Cards) -->
    <div class="content-card" style="margin-block-end: 20px; padding: 20px;">
        <form id="filter-form" method="GET" style="display: grid; grid-template-columns: 2fr repeat(4, 1fr) auto auto; gap: 15px; align-items: center;">
            <input type="hidden" name="direction" value="{{ $direction }}" id="current-direction">
            
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="{{ $direction === 'company_held' ? 'Search licenses...' : 'Search licenses or customers...' }}"
                   style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="search-input">
            
            <select name="status" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="status-filter">
                <option value="">All Status</option>
                @foreach(\App\Models\BusinessLicense::STATUSES as $key => $label)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            
            <select name="license_type" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="type-filter">
                <option value="">All Types</option>
                @foreach(\App\Models\BusinessLicense::LICENSE_TYPES as $key => $label)
                <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <div id="company-filters" style="display: {{ $direction === 'company_held' ? 'contents' : 'none' }};">
                <select name="priority" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="priority-filter">
                    <option value="">All Priority</option>
                    @foreach(\App\Models\BusinessLicense::PRIORITY_LEVELS as $key => $label)
                    <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="department" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="department-filter">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div id="customer-filters" style="display: {{ $direction === 'customer_issued' ? 'contents' : 'none' }};">
                <select name="billing_cycle" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="billing-filter">
                    <option value="">All Billing</option>
                    @foreach(\App\Models\BusinessLicense::BILLING_CYCLES as $key => $label)
                    <option value="{{ $key }}" {{ request('billing_cycle') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="support_level" style="padding: 8px; border: 2px solid #ddd; border-radius: 4px;" id="support-filter">
                    <option value="">All Support</option>
                    @foreach(\App\Models\BusinessLicense::SUPPORT_LEVELS as $key => $label)
                    <option value="{{ $key }}" {{ request('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn">Filter</button>
            
            @if(request()->hasAny(['search', 'status', 'license_type', 'priority', 'department', 'billing_cycle', 'support_level']))
            <a href="{{ route('business-licenses.index', ['direction' => $direction]) }}" class="btn" id="clear-filters">Clear</a>
            @endif
        </form>
    </div>

    <!-- Statistics Cards (Now Below Filters, Dynamic) -->
    <div id="stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-block-end: 30px;">
        @if($direction === 'company_held')
            <!-- Company License Stats -->
            <div class="metric-card" style="background: white; border-top: 4px solid #4caf50;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #4caf50;">✅</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="active-count">{{ $stats['active_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Active Licenses</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #ff9800;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #ff9800;">⏰</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                        <div style="font-size: 14px; color: #666;">Expiring Soon</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #f44336;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #f44336;">⚠️</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Expired</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #2196f3;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #2196f3;">💰</div>
                    <div>
                        <div style="font-size: 24px; font-weight: bold; color: #333;" id="annual-cost">${{ number_format($stats['total_annual_cost'], 0) }}</div>
                        <div style="font-size: 14px; color: #666;">Annual Cost</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #9c27b0;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #9c27b0;">🚨</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="critical-count">{{ $stats['critical_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Critical Priority</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #607d8b;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #607d8b;">📊</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="total-count">{{ $stats['total_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Total Licenses</div>
                    </div>
                </div>
            </div>
        @else
            <!-- Customer License Stats -->
            <div class="metric-card" style="background: white; border-top: 4px solid #4caf50;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #4caf50;">✅</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="active-count">{{ $stats['active_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Active Licenses</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #2196f3;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #2196f3;">💰</div>
                    <div>
                        <div style="font-size: 24px; font-weight: bold; color: #333;" id="revenue-amount">${{ number_format($stats['total_revenue'], 0) }}</div>
                        <div style="font-size: 14px; color: #666;">Annual Revenue</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #9c27b0;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #9c27b0;">👥</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="customers-count">{{ $stats['unique_customers'] }}</div>
                        <div style="font-size: 14px; color: #666;">Unique Customers</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #ff9800;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #ff9800;">⏰</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="expiring-count">{{ $stats['expiring_soon'] }}</div>
                        <div style="font-size: 14px; color: #666;">Expiring Soon</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #f44336;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #f44336;">⚠️</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="expired-count">{{ $stats['expired_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Expired</div>
                    </div>
                </div>
            </div>

            <div class="metric-card" style="background: white; border-top: 4px solid #607d8b;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 32px; color: #607d8b;">📊</div>
                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #333;" id="total-count">{{ $stats['total_licenses'] }}</div>
                        <div style="font-size: 14px; color: #666;">Total Licenses</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" style="display: none; text-align: center; padding: 20px; margin-bottom: 20px;">
        <div style="display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #2196f3; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <span style="margin-left: 10px; color: #666;">Updating statistics...</span>
    </div>

    <!-- Licenses Table -->
    <div class="content-card" style="padding: 0; overflow: hidden;">
        @if($licenses->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">License Details</th>
                        @if($direction === 'company_held')
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Department</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Responsible</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Priority</th>
                        @else
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Customer</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Revenue</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Support</th>
                        @endif
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Status</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Dates</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #333; border-bottom: 1px solid #dee2e6;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licenses as $license)
                    <tr style="border-bottom: 1px solid #e9ecef; {{ $license->is_expired ? 'background: #fff5f5;' : ($license->is_expiring_soon ? 'background: #fffbf0;' : '') }} transition: background-color 0.2s;" 
                        onmouseover="this.style.backgroundColor='#f8f9fa'" 
                        onmouseout="this.style.backgroundColor='{{ $license->is_expired ? '#fff5f5' : ($license->is_expiring_soon ? '#fffbf0' : 'transparent') }}'">
                        <!-- License Details -->
                        <td style="padding: 15px; vertical-align: top;">
                            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">{{ $license->license_name }}</div>
                            <div style="font-size: 13px; color: #666; margin-bottom: 2px;">{{ $license->license_number }}</div>
                            <div style="font-size: 12px; color: #888;">{{ $license->license_type_name }}</div>
                            @if($license->description)
                            <div style="font-size: 12px; color: #666; margin-top: 6px; max-width: 250px;">{{ Str::limit($license->description, 100) }}</div>
                            @endif
                        </td>

                        @if($license->isCompanyHeld())
                        <!-- Department -->
                        <td style="padding: 15px; vertical-align: top;">
                            <div style="font-weight: 500; color: #333;">{{ $license->department->name ?? 'Unassigned' }}</div>
                            @if($license->location)
                            <div style="font-size: 12px; color: #666;">📍 {{ $license->location }}</div>
                            @endif
                        </td>

                        <!-- Responsible -->
                        <td style="padding: 15px; vertical-align: top;">
                            <div style="font-weight: 500; color: #333;">{{ $license->responsibleEmployee->full_name ?? 'Unassigned' }}</div>
                            @if($license->responsibleEmployee?->email)
                            <div style="font-size: 12px; color: #666;">{{ $license->responsibleEmployee->email }}</div>
                            @endif
                        </td>

                        <!-- Priority -->
                        <td style="padding: 15px; vertical-align: top;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500; {{ $license->getPriorityColorClass() }}">
                                {{ $license->priority_level_name }}
                            </span>
                            @if($license->renewal_cost)
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">${{ number_format($license->renewal_cost, 0) }}/yr</div>
                            @endif
                        </td>
                        @else
                        <!-- Customer -->
                        <td style="padding: 15px; vertical-align: top;">
                            <div style="font-weight: 500; color: #333;">{{ $license->customer_display_name }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $license->customer_email }}</div>
                            @if($license->license_quantity)
                            <div style="font-size: 12px; color: #888;">Qty: {{ $license->license_quantity }}</div>
                            @endif
                        </td>

                        <!-- Revenue -->
                        <td style="padding: 15px; vertical-align: top;">
                            <div style="font-weight: 600; color: #4caf50;">${{ number_format($license->revenue_amount ?? 0, 0) }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $license->billing_cycle_name }}</div>
                            @if($license->annual_revenue)
                            <div style="font-size: 11px; color: #888;">${{ number_format($license->annual_revenue, 0) }}/yr</div>
                            @endif
                        </td>

                        <!-- Support -->
                        <td style="padding: 15px; vertical-align: top;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500; background: #e3f2fd; color: #1976d2;">
                                {{ $license->support_level_name }}
                            </span>
                            @if($license->usage_limit)
                            <div style="font-size: 11px; color: #666; margin-top: 4px;">{{ Str::limit($license->usage_limit, 30) }}</div>
                            @endif
                        </td>
                        @endif

                        <!-- Status -->
                        <td style="padding: 15px; vertical-align: top;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500; {{ $license->getStatusColorClass() }}">
                                {{ $license->status_name }}
                            </span>
                            @if($license->is_expired)
                            <div style="font-size: 11px; color: #f44336; margin-top: 4px;">{{ abs($license->days_until_expiry) }} days overdue</div>
                            @elseif($license->is_expiring_soon)
                            <div style="font-size: 11px; color: #ff9800; margin-top: 4px;">{{ $license->days_until_expiry }} days left</div>
                            @endif
                        </td>

                        <!-- Dates -->
                        <td style="padding: 15px; vertical-align: top;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 2px;">
                                <strong>Issued:</strong> {{ $license->issue_date ? $license->issue_date->format('M d, Y') : 'N/A' }}
                            </div>
                            <div style="font-size: 12px; color: #666; {{ $license->is_expired ? 'color: #f44336; font-weight: bold;' : ($license->is_expiring_soon ? 'color: #ff9800; font-weight: bold;' : '') }}">
                                <strong>Expires:</strong> {{ $license->expiry_date ? $license->expiry_date->format('M d, Y') : 'N/A' }}
                            </div>
                            @if($license->renewal_date)
                            <div style="font-size: 11px; color: #888; margin-top: 2px;">
                                <strong>Renewed:</strong> {{ $license->renewal_date->format('M d, Y') }}
                            </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td style="padding: 15px; text-align: center; vertical-align: top;">
                            <div style="display: flex; flex-direction: column; gap: 6px; min-width: 100px;">
                                <a href="{{ route('business-licenses.show', $license) }}" 
                                   style="padding: 6px 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px; text-align: center; border: 1px solid #dee2e6; transition: all 0.2s;"
                                   onmouseover="this.style.backgroundColor='#e9ecef'; this.style.borderColor='#adb5bd';"
                                   onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.borderColor='#dee2e6';">
                                    View
                                </a>
                                
                                @if($license->canRenew() && ($license->is_expired || $license->is_expiring_soon))
                                <a href="{{ route('business-licenses.renew', $license) }}" 
                                   style="padding: 6px 12px; background: #ff9800; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; text-align: center; border: 1px solid #ff9800; transition: all 0.2s;"
                                   onmouseover="this.style.backgroundColor='#f57c00'; this.style.borderColor='#f57c00';"
                                   onmouseout="this.style.backgroundColor='#ff9800'; this.style.borderColor='#ff9800';">
                                    Renew
                                </a>
                                @endif
                                
                                <a href="{{ route('business-licenses.edit', $license) }}" 
                                   style="padding: 6px 12px; background: #2196f3; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; text-align: center; border: 1px solid #2196f3; transition: all 0.2s;"
                                   onmouseover="this.style.backgroundColor='#1976d2'; this.style.borderColor='#1976d2';"
                                   onmouseout="this.style.backgroundColor='#2196f3'; this.style.borderColor='#2196f3';">
                                    Edit
                                </a>
                                
                                @if($license->document_path)
                                <a href="{{ route('business-licenses.download', $license) }}" 
                                   style="padding: 6px 12px; background: #4caf50; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; text-align: center; border: 1px solid #4caf50; transition: all 0.2s;"
                                   onmouseover="this.style.backgroundColor='#388e3c'; this.style.borderColor='#388e3c';"
                                   onmouseout="this.style.backgroundColor='#4caf50'; this.style.borderColor='#4caf50';">
                                    Document
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 60px;">
            <div style="font-size: 48px; margin-bottom: 20px; color: #ccc;">📋</div>
            <h3 style="color: #666; margin-bottom: 10px;">No {{ $direction === 'company_held' ? 'business licenses' : 'customer licenses' }} found</h3>
            <p style="color: #888; margin-bottom: 20px;">Get started by adding your first {{ $direction === 'company_held' ? 'business license' : 'customer license' }}.</p>
            <a href="{{ route('business-licenses.create', ['direction' => $direction]) }}" class="btn btn-primary">
                Add First {{ $direction === 'company_held' ? 'License' : 'Customer License' }}
            </a>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($licenses->hasPages())
    <div style="margin-block-start: 20px;">
        {{ $licenses->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.metric-card {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.btn {
    padding: 8px 16px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
    background: #f8f9fa;
}

.btn-primary {
    background: #2196f3;
    color: white;
    border-color: #2196f3;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
}

/* Table hover effects - removed since using inline onmouseover/onmouseout */

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive table */
@media (max-width: 1200px) {
    #filter-form {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    #company-filters, #customer-filters {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    table {
        font-size: 14px;
    }
    
    table th, table td {
        padding: 10px 8px;
    }
}
</style>

<script>
// JavaScript for dynamic filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#filter-form select, #filter-form input[type="text"]');
    const loadingIndicator = document.getElementById('loading-indicator');
    const statsCards = document.getElementById('stats-cards');
    
    // Add event listeners to all filter inputs
    filterInputs.forEach(input => {
        input.addEventListener('change', updateStats);
        if (input.type === 'text') {
            // Debounce text input
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(updateStats, 500);
            });
        }
    });
    
    function updateStats() {
        // Show loading indicator
        loadingIndicator.style.display = 'block';
        statsCards.style.opacity = '0.5';
        
        // Get current filter values
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams(formData);
        
        // Make AJAX request
        fetch(`{{ route('business-licenses.filtered-stats') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatsCards(data.stats, data.direction);
                }
            })
            .catch(error => {
                console.error('Error updating stats:', error);
            })
            .finally(() => {
                // Hide loading indicator
                loadingIndicator.style.display = 'none';
                statsCards.style.opacity = '1';
            });
    }
    
    function updateStatsCards(stats, direction) {
        // Update common stats
        document.getElementById('active-count').textContent = stats.active_licenses;
        document.getElementById('expiring-count').textContent = stats.expiring_soon;
        document.getElementById('expired-count').textContent = stats.expired_licenses;
        document.getElementById('total-count').textContent = stats.total_licenses;
        
        // Update direction-specific stats
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