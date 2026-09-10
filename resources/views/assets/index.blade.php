@extends('layouts.app')
@php $activeTab = request()->get('tab', 'assets'); @endphp

@section('title', 'Asset Management')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* ── Assets: index + its tabs, partials and modals ─────────────────── */
.as-index a[class*="btn-"], .as-index a.tab-btn, .as-index a.action-btn { text-decoration: none !important; }
.as-index .mv-mono { font-size: 12px; }
.as-index .as-num { font-variant-numeric: tabular-nums; color: var(--mv-ink); font-weight: 500; }
.as-index .as-muted { color: var(--mv-muted); }

/* Tabs */
.as-index .as-tabs { margin-bottom: 18px; }
.as-index .as-tabs .tab-btn { padding: 10px 14px; font-size: 13.5px; }
.as-index .as-tabs .tab-btn:not(.active):hover { border-bottom-color: var(--mv-line-strong); }
.as-index .as-tabs .tab-btn .mv-i { color: currentColor; }
.as-index .as-count {
  min-width: 20px; padding: 0 6px; border-radius: 9px; line-height: 18px; text-align: center;
  font-size: 11.5px; font-weight: 500; font-variant-numeric: tabular-nums;
  background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2);
}
.as-index .tab-btn.active .as-count { background: var(--mv-accent-soft); border-color: #C9D9EE; color: var(--mv-accent-ink); }

/* Controls */
.as-index .ui-input, .as-index .ui-select, .as-index .ui-textarea { font-size: 13.5px; padding: 8px 11px; }
.as-index .ui-label { margin-bottom: 5px; }
.as-index .as-req { color: var(--mv-crit); }
.as-index .as-opt { color: var(--mv-muted); font-weight: 400; }
.as-index .as-hint { font-size: 12px; color: var(--mv-muted); margin: 4px 0 0; }

/* Toolbar (search + filters left, actions right) */
.as-index .as-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 14px; }
.as-index .as-toolbar-end { display: flex; flex-wrap: wrap; gap: 8px; margin-left: auto; }
.as-index .as-toolbar .ui-input, .as-index .as-toolbar .ui-select { height: 36px; }
.as-index .as-toolbar [class*="btn-"] { height: 36px; padding: 0 13px; font-size: 13.5px; }
.as-index .as-search { position: relative; margin: 0; }
.as-index .as-search .mv-i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--mv-muted); pointer-events: none; }
.as-index .as-search .ui-input { width: 280px; padding-left: 33px; }
.as-index .as-toolbar .ui-select { width: 170px; }

/* Filter bars (assignments / history) */
.as-index .filter-bar { padding: 12px 14px; gap: 10px 12px; margin-bottom: 14px; }
.as-index .filter-bar .ui-input, .as-index .filter-bar .ui-select { height: 36px; }
.as-index .filter-bar .as-grow { flex: 1 1 220px; }
.as-index .filter-bar .as-grow .ui-input { width: 100%; }
.as-index .filter-bar .filter-actions [class*="btn-"] { height: 36px; padding: 0 13px; font-size: 13.5px; }
.as-index .as-check { display: inline-flex; align-items: center; gap: 8px; height: 36px; margin: 0; font-size: 13.5px; color: var(--mv-ink-2); cursor: pointer; }
.as-index .as-check input { width: 15px; height: 15px; margin: 0; accent-color: var(--mv-accent); }

/* Stat tiles */
.as-index .as-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
@media (max-width: 1100px) { .as-index .as-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

/* Cards + tables */
.as-index .as-card-head { display: flex; align-items: baseline; gap: 10px; min-width: 0; }
.as-index .as-card-title { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.as-index .as-card-meta { font-size: 12.5px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.as-index .ui-table thead th.text-center, .as-index .ui-table td.text-center { text-align: center; }
.as-index .ui-table thead th.as-right, .as-index .ui-table td.as-right,
.as-index .assignment-table thead th.as-right, .as-index .assignment-table td.as-right { text-align: right; }
.as-index .as-cell { display: flex; align-items: center; gap: 12px; min-width: 0; }
.as-index .as-thumb {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0; display: grid; place-items: center;
  background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2);
}
.as-index .as-thumb .mv-i { width: 16px; height: 16px; }
.as-index .cell-sub { margin-top: 1px; }
.as-index .as-desc { max-width: 340px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.as-index .as-stock { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
.as-index .action-group { justify-content: flex-end; }
.as-index .action-btn .mv-i { width: 15px; height: 15px; }
.as-index .mv-avatar { width: 30px; height: 30px; font-size: 11.5px; }
.as-index .overdue-row td { background: #FDF7F6; }
.as-index .overdue-row:hover td { background: var(--mv-crit-soft); }
.as-index .as-late { color: var(--mv-crit); font-weight: 500; }
.as-index .as-cond { display: grid; grid-template-columns: auto auto; gap: 4px 8px; align-items: center; justify-content: start; }
.as-index .as-cond .cell-sub { margin: 0; }
.as-index .as-pager { justify-content: space-between; gap: 12px; font-size: 12.5px; color: var(--mv-muted); padding: 10px 18px; }
.as-index .as-pager nav { margin-left: auto; }

/* Empty states */
.as-index .empty-state { padding: 44px 16px; }
.as-index .empty-state-icon { margin-bottom: 8px; }
.as-index .empty-state-icon .mv-i { width: 32px; height: 32px; stroke-width: 1.5; }
.as-index .empty-state h3 { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
.as-index .empty-state-msg { margin: 0 0 14px; font-size: 13.5px; color: var(--mv-muted); }

/* Asset cards (partials/assets-tab) */
.as-index .as-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 12px; }
.as-index .as-cards .ui-card-body { display: grid; gap: 12px; padding: 16px 18px; }
.as-index .as-price { font-size: 15px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; white-space: nowrap; }

/* Forms */
.as-index .as-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
.as-index .as-grid.is-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
@media (max-width: 1100px) { .as-index .as-grid.is-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 640px) { .as-index .as-grid, .as-index .as-grid.is-4 { grid-template-columns: minmax(0, 1fr); } }
.as-index .as-grid .is-full { grid-column: 1 / -1; }
.as-index .as-stack { display: grid; gap: 14px; }
.as-index .as-form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
.as-index .as-banner { display: flex; align-items: center; gap: 10px; }

/* Modals */
.as-index .as-overlay {
  position: fixed; inset: 0; z-index: 1100; padding: 16px;
  background: rgba(22, 32, 44, .45); align-items: center; justify-content: center;
}
.as-index .as-modal {
  width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto;
  background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 12px;
  box-shadow: 0 16px 40px rgba(22, 32, 44, .16);
}
.as-index .as-modal.is-wide { max-width: 660px; }
.as-index .as-modal.is-narrow { max-width: 420px; }
.as-index .as-modal-head {
  position: sticky; top: 0; z-index: 1; display: flex; align-items: center; gap: 10px;
  padding: 14px 16px 14px 18px; background: var(--mv-surface); border-bottom: 1px solid var(--mv-line);
}
.as-index .as-modal-head h3 {
  flex: 1; min-width: 0; margin: 0; font-size: 15px; font-weight: 600; color: var(--mv-ink);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.as-index .as-x {
  width: 32px; height: 32px; flex-shrink: 0; display: grid; place-items: center; padding: 0;
  border: 0; border-radius: 8px; background: transparent; color: var(--mv-muted); cursor: pointer;
}
.as-index .as-x:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.as-index .as-modal-body { padding: 18px; }
.as-index .as-modal-actions {
  display: flex; justify-content: flex-end; gap: 8px;
  margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--mv-line);
}
.as-index .as-summary {
  margin-bottom: 16px; padding: 12px 14px; border-radius: 8px;
  background: var(--mv-surface-2); border: 1px solid var(--mv-line);
}
.as-index .as-sec { margin: 0 0 10px; font-size: 12px; font-weight: 600; color: var(--mv-muted); letter-spacing: .02em; }
.as-index .as-kv { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 16px; }
.as-index .as-kv.is-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.as-index .as-k { font-size: 12px; color: var(--mv-muted); margin-bottom: 2px; }
.as-index .as-v { font-size: 13.5px; color: var(--mv-ink); overflow-wrap: anywhere; font-variant-numeric: tabular-nums; }
.as-index .as-v.is-strong { font-weight: 500; }
.as-index .as-detail { display: grid; gap: 16px; }
.as-index .as-detail > section + section { padding-top: 16px; border-top: 1px solid var(--mv-line); }
.as-index .as-detail .as-note { margin: 0; font-size: 13.5px; color: var(--mv-ink-2); white-space: pre-line; }
.as-index .as-detail .as-note + .as-note { margin-top: 10px; }
.as-index .as-alert {
  margin-top: 12px; padding: 9px 12px; border-radius: 8px; font-size: 13px;
  background: var(--mv-crit-soft); color: var(--mv-crit); border: 1px solid #F2CACA;
}
.as-index .as-panel { padding: 12px 14px; border-radius: 8px; border: 1px solid var(--mv-line); background: var(--mv-surface-2); }
.as-index .as-choices { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px; }
.as-index .as-choice {
  display: flex; align-items: center; gap: 8px; margin: 0; padding: 9px 11px; cursor: pointer;
  border: 1px solid var(--mv-line-strong); border-radius: 8px; background: var(--mv-surface);
  font-size: 13px; color: var(--mv-ink-2);
}
.as-index .as-choice:hover { background: var(--mv-surface-2); }
.as-index .as-choice input { margin: 0; accent-color: var(--mv-accent); }
.as-index .as-choice .mv-i { width: 15px; height: 15px; color: var(--mv-muted); }
.as-index .as-choice:has(input:checked) { border-color: var(--mv-accent); background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
.as-index .as-choice:has(input:checked) .mv-i { color: var(--mv-accent-ink); }
.as-index .as-checks { display: grid; gap: 8px; }
.as-index .as-checks .as-check { height: auto; }
.as-index .as-menu { display: grid; gap: 6px; }
.as-index .as-menu-item {
  display: flex; align-items: center; gap: 12px; width: 100%; padding: 10px 12px; text-align: left; cursor: pointer;
  border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface); color: var(--mv-ink);
}
.as-index .as-menu-item:hover { background: var(--mv-surface-2); border-color: var(--mv-line-strong); }
.as-index .as-menu-item .mv-i { color: var(--mv-muted); }
.as-index .as-menu-item strong { display: block; font-size: 13.5px; font-weight: 500; }
.as-index .as-menu-item small { display: block; font-size: 12px; color: var(--mv-muted); }
.as-index .as-menu-item.is-danger, .as-index .as-menu-item.is-danger .mv-i { color: var(--mv-crit); }
.as-index .as-menu-item.is-danger:hover { background: var(--mv-crit-soft); border-color: #F2CACA; }
@media (max-width: 560px) { .as-index .as-kv, .as-index .as-kv.is-3 { grid-template-columns: minmax(0, 1fr); } }

/* Tom Select */
.as-index .ts-wrapper.ui-select, .as-index .ts-wrapper.ui-input {
  padding: 0 !important; border: 0 !important; background: none !important; box-shadow: none !important; height: auto;
}
.as-index .ts-wrapper .ts-control {
  min-height: 36px; padding: 7px 11px; border: 1px solid var(--mv-line-strong); border-radius: 8px;
  background-color: var(--mv-surface); box-shadow: none; font-size: 13.5px; color: var(--mv-ink);
}
.as-index .ts-wrapper.focus .ts-control { border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
.ts-dropdown { z-index: 1150; border: 1px solid var(--mv-line); border-radius: 8px; box-shadow: 0 12px 32px rgba(22, 32, 44, .12); font-size: 13.5px; }
.ts-dropdown .option { padding: 7px 11px; }
.ts-dropdown .option.active { background: var(--mv-accent-soft); color: var(--mv-accent-ink); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="as-index">

{{-- Tab Navigation --}}
<nav class="tab-nav as-tabs" aria-label="Asset sections">
    <a href="{{ route('assets.index', ['tab' => 'assets']) }}" class="tab-btn {{ $activeTab === 'assets' ? 'active' : '' }}">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-box"/></svg> All Assets
        @if(isset($stats))
        <span class="as-count">{{ $stats['total_assets'] }}</span>
        @endif
    </a>
    <a href="{{ route('assets.index', ['tab' => 'assignments']) }}" class="tab-btn {{ $activeTab === 'assignments' ? 'active' : '' }}">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user-check"/></svg> Current Assignments
        @if(isset($assignmentStats))
        <span class="as-count">{{ $assignmentStats['active_assignments'] }}</span>
        @endif
    </a>
    <a href="{{ route('assets.index', ['tab' => 'history']) }}" class="tab-btn {{ $activeTab === 'history' ? 'active' : '' }}">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-history"/></svg> Assignment History
    </a>
    <a href="{{ route('assets.index', ['tab' => 'assign']) }}" class="tab-btn {{ $activeTab === 'assign' ? 'active' : '' }}">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user-plus"/></svg> Assign Assets
    </a>
</nav>

{{-- Tab Content --}}
@if($activeTab === 'assets')

{{-- Toolbar: search + filter left, actions right --}}
<div class="as-toolbar">
    <div class="as-search">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-search"/></svg>
        <input type="text" id="assetSearch" placeholder="Search assets..." class="ui-input" aria-label="Search assets">
    </div>
    <select id="categoryFilter" class="ui-select" aria-label="Category">
        <option value="">All Categories</option>
        <option value="laptop">Laptops</option>
        <option value="phone">Phones</option>
        <option value="equipment">Equipment</option>
        <option value="furniture">Furniture</option>
        <option value="other">Other</option>
    </select>
    <div class="as-toolbar-end">
        <a href="{{ route('assets.export') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Export CSV</a>
        <a href="{{ route('assets.import-form') }}" class="btn-secondary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-upload"/></svg> Bulk Import</a>
        <a href="{{ route('assets.create') }}" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add New Asset</a>
    </div>
</div>

{{-- Assets Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <div class="as-card-head">
            <h3 class="as-card-title">Asset Inventory</h3>
            <span class="as-card-meta">{{ isset($assets) ? ($assets->count() ?? 0) : 0 }} items</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="ui-table w-full">
            <thead>
                <tr>
                    <th>Asset Details</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th class="as-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($assets) && $assets->count() > 0)
                    @foreach($assets as $asset)
                    @php
                        $catKey = strtolower($asset->category ?? '');
                        $catIcon = match (true) {
                            str_contains($catKey, 'vehicle') => 'truck',
                            str_contains($catKey, 'pos') || str_contains($catKey, 'terminal') => 'card',
                            str_contains($catKey, 'licen') || str_contains($catKey, 'software') => 'key',
                            str_contains($catKey, 'phone') => 'phone',
                            str_contains($catKey, 'laptop') || str_contains($catKey, 'computer') || str_contains($catKey, 'it ') || str_contains($catKey, 'electronic') => 'monitor',
                            str_contains($catKey, 'equipment') || str_contains($catKey, 'tool') => 'wrench',
                            str_contains($catKey, 'furniture') || str_contains($catKey, 'office') => 'layers',
                            default => 'box',
                        };
                        $stKey = str_replace('asset-', '', strtolower($asset->status ?? ''));
                        $stBadge = ['active'=>'badge-green','available'=>'badge-green','inactive'=>'badge-gray','maintenance'=>'badge-yellow','pending'=>'badge-yellow','damaged'=>'badge-red','discontinued'=>'badge-red','retired'=>'badge-gray'];
                        $stock = $asset->stock_quantity ?? 0;
                    @endphp
                    <tr class="asset-row">
                        <td>
                            <div class="as-cell">
                                <span class="as-thumb" aria-hidden="true"><svg class="mv-i"><use href="#i-{{ $catIcon }}"/></svg></span>
                                <div class="min-w-0">
                                    <div class="cell-primary">{{ $asset->name }}</div>
                                    <div class="cell-sub"><span class="mv-mono">#{{ $asset->id ?? 'N/A' }}</span>@if($asset->sku ?? false) <span aria-hidden="true">&middot;</span> SKU <span class="mv-mono">{{ $asset->sku }}</span>@endif</div>
                                    @if($asset->description ?? false)<div class="cell-sub as-desc">{{ $asset->description }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ ucfirst($asset->category ?? 'Unknown') }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $stBadge[$stKey] ?? 'badge-gray' }}">{{ $stKey !== '' ? ucfirst(str_replace(['-', '_'], ' ', $stKey)) : 'Unknown' }}</span>
                        </td>
                        <td>
                            <div class="as-stock">
                                <span class="as-num">{{ $stock }} units</span>
                                <span class="badge {{ $stock > 10 ? 'badge-green' : ($stock > 0 ? 'badge-yellow' : 'badge-red') }}">
                                    {{ $stock > 10 ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock') }}
                                </span>
                            </div>
                        </td>
                        <td class="as-right">
                            <div class="action-group">
                                <a href="{{ route('assets.show', $asset->id ?? 1) }}" class="action-btn" title="View" aria-label="View"><svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg></a>
                                <a href="{{ route('assets.edit', $asset->id ?? 1) }}" class="action-btn" title="Edit" aria-label="Edit"><svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon"><svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg></div>
                            <p class="empty-state-msg">No assets found.</p>
                            <a href="{{ route('assets.create') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-plus"/></svg> Add your first asset</a>
                        </div>
                    </td></tr>
                @endif
            </tbody>
        </table>
    </div>
    @if(isset($assets) && method_exists($assets,'hasPages') && $assets->hasPages())
    <div class="ui-card-footer as-pager">
        <span class="tabular">Showing {{ $assets->firstItem() ?? 0 }}&#x2013;{{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }}</span>
        {{ $assets->links() }}
    </div>
    @endif
</div>

@elseif($activeTab === 'assignments')
    @include('assets.partials.assignments-tab')
@elseif($activeTab === 'history')
    @include('assets.partials.history-tab')
@elseif($activeTab === 'assign')
    @include('assets.partials.assign-tab')
@endif

{{-- Return Asset Modal --}}
<div id="returnAssetModal" class="as-overlay" style="display:none;" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Return Asset</h3>
            <button type="button" onclick="closeReturnModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="as-modal-body">
            <div id="returnAssignmentInfo" class="as-summary">
                <h4 class="as-sec">Assignment Details</h4>
                <div class="as-kv">
                    <div><div class="as-k">Asset</div><div class="as-v is-strong" id="return_asset_name">Asset Name</div></div>
                    <div><div class="as-k">Employee</div><div class="as-v is-strong" id="return_employee_name">Employee Name</div></div>
                    <div><div class="as-k">Assigned Date</div><div class="as-v" id="return_assigned_date">Date</div></div>
                    <div><div class="as-k">Days Assigned</div><div class="as-v" id="return_days_assigned">0 days</div></div>
                    <div><div class="as-k">Quantity</div><div class="as-v" id="return_quantity">1</div></div>
                </div>
            </div>
            <form id="returnAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="return_assignment_id" name="assignment_id">
                <div class="as-stack">
                    <div class="as-grid">
                        <div>
                            <label class="ui-label" for="return_date">Return Date <span class="as-req">*</span></label>
                            <input type="date" name="return_date" id="return_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label" for="return_condition">Condition Returned <span class="as-req">*</span></label>
                            <select name="condition_when_returned" id="return_condition" required class="ui-select w-full">
                                <option value="">Select condition...</option>
                                <option value="new">New &#x2013; Like brand new</option>
                                <option value="good">Good &#x2013; Minor wear, fully functional</option>
                                <option value="fair">Fair &#x2013; Noticeable wear, some issues</option>
                                <option value="poor">Poor &#x2013; Significant damage</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Return Notes</label>
                        <textarea name="return_notes" rows="3" placeholder="Optional notes about the return..." class="ui-textarea w-full"></textarea>
                    </div>
                    <div>
                        <span class="ui-label">Update Asset Status</span>
                        <div class="as-choices">
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="available" checked> <svg class="mv-i" aria-hidden="true"><use href="#i-box"/></svg> Available
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="maintenance"> <svg class="mv-i" aria-hidden="true"><use href="#i-wrench"/></svg> Maintenance
                            </label>
                            <label class="as-choice">
                                <input type="radio" name="update_asset_status" value="damaged"> <svg class="mv-i" aria-hidden="true"><use href="#i-alert-triangle"/></svg> Damaged
                            </label>
                        </div>
                    </div>
                </div>
                <div class="as-modal-actions">
                    <button type="button" onclick="closeReturnModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-undo"/></svg> Process Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transfer Asset Modal --}}
<div id="transferAssetModal" class="as-overlay" style="display:none;" role="dialog" aria-modal="true">
    <div class="as-modal">
        <div class="as-modal-head">
            <h3>Transfer Asset</h3>
            <button type="button" onclick="closeTransferModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="as-modal-body">
            <div id="transferAssignmentInfo" class="as-summary">
                <h4 class="as-sec">Current Assignment</h4>
                <div class="as-kv">
                    <div><div class="as-k">Asset</div><div class="as-v is-strong" id="transfer_asset_name">Asset Name</div></div>
                    <div><div class="as-k">Current Employee</div><div class="as-v is-strong" id="transfer_current_employee">Employee Name</div></div>
                </div>
            </div>
            <form id="transferAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="transfer_assignment_id" name="assignment_id">
                <div class="as-stack">
                    <div>
                        <label class="ui-label" for="transfer_new_employee_id">Transfer To <span class="as-req">*</span></label>
                        <select name="new_employee_id" id="transfer_new_employee_id" required class="ui-select w-full">
                            <option value="">Choose new employee...</option>
                            @if(isset($employees))
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_number }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="as-grid">
                        <div>
                            <label class="ui-label" for="transfer_date">Transfer Date <span class="as-req">*</span></label>
                            <input type="date" name="transfer_date" id="transfer_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Reason <span class="as-req">*</span></label>
                            <select name="transfer_reason" required class="ui-select w-full">
                                <option value="">Select reason...</option>
                                <option value="employee_departure">Employee Departure</option>
                                <option value="role_change">Role Change</option>
                                <option value="department_transfer">Department Transfer</option>
                                <option value="project_completion">Project Completion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Transfer Notes</label>
                        <textarea name="transfer_notes" rows="3" placeholder="Additional notes..." class="ui-textarea w-full"></textarea>
                    </div>
                </div>
                <div class="as-modal-actions">
                    <button type="button" onclick="closeTransferModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-refresh"/></svg> Process Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assignment Details Modal --}}
<div id="assignmentDetailsModal" class="as-overlay" style="display:none;" role="dialog" aria-modal="true">
    <div class="as-modal is-wide">
        <div class="as-modal-head">
            <h3 id="detailsModalTitle">Assignment Details</h3>
            <button type="button" onclick="closeDetailsModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div id="detailsModalBody" class="as-modal-body"></div>
    </div>
</div>

</div>{{-- /.as-index --}}
<script>
// CSRF token setup for AJAX requests
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Global variables for modal management
let currentAssignmentForReturn = null;
let currentAssignmentForDetails = null;
let currentAssignmentForTransfer = null;

// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('assetSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const assetRows = document.querySelectorAll('.asset-row');

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterAssets();
        });
    }

    // Category filter functionality
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterAssets();
        });
    }

    function filterAssets() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value.toLowerCase() : '';

        assetRows.forEach(row => {
            const assetName = row.querySelector('td:first-child').textContent.toLowerCase();
            const category = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

            const matchesSearch = !searchTerm || assetName.includes(searchTerm);
            const matchesCategory = !selectedCategory || category.includes(selectedCategory);

            row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        });
    }
});

// Close modals when clicking outside or pressing Escape
document.addEventListener('click', function(event) {
    const returnModal = document.getElementById('returnAssetModal');
    const detailsModal = document.getElementById('assignmentDetailsModal');
    const transferModal = document.getElementById('transferAssetModal');

    if (event.target === returnModal) {
        closeReturnModal();
    }

    if (event.target === detailsModal) {
        closeDetailsModal();
    }

    if (event.target === transferModal) {
        closeTransferModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (document.getElementById('returnAssetModal').style.display === 'flex') {
            closeReturnModal();
        }
        if (document.getElementById('assignmentDetailsModal').style.display === 'flex') {
            closeDetailsModal();
        }
        if (document.getElementById('transferAssetModal').style.display === 'flex') {
            closeTransferModal();
        }
    }
});
function openReturnModal(assignmentId) {
    currentAssignmentForReturn = assignmentId;

    // Fetch assignment details and populate modal
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;

                // Populate assignment info in return modal
                document.getElementById('return_assignment_id').value = assignment.id;
                document.getElementById('return_asset_name').textContent = assignment.asset.name;
                document.getElementById('return_employee_name').textContent = assignment.employee.first_name + ' ' + assignment.employee.last_name;
                document.getElementById('return_assigned_date').textContent = new Date(assignment.assignment_date).toLocaleDateString();
                document.getElementById('return_days_assigned').textContent = (assignment.days_assigned || 0) + ' days';
                document.getElementById('return_quantity').textContent = assignment.quantity_assigned;

                // Show modal
                document.getElementById('returnAssetModal').style.display = 'flex';
            } else {
                alert('Failed to load assignment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeReturnModal() {
    document.getElementById('returnAssetModal').style.display = 'none';
    document.getElementById('returnAssetForm').reset();
}

// Transfer Asset Modal Functions
function openTransferModal(assignmentId) {
    currentAssignmentForTransfer = assignmentId;

    // Fetch assignment details and populate modal
    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;

                // Populate assignment info in transfer modal
                document.getElementById('transfer_assignment_id').value = assignment.id;
                document.getElementById('transfer_asset_name').textContent = assignment.asset.name;
                document.getElementById('transfer_current_employee').textContent = assignment.employee.first_name + ' ' + assignment.employee.last_name;

                // Show modal
                document.getElementById('transferAssetModal').style.display = 'flex';
            } else {
                alert('Failed to load assignment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeTransferModal() {
    document.getElementById('transferAssetModal').style.display = 'none';
    document.getElementById('transferAssetForm').reset();
}

// Assignment Details Modal Functions
function viewAssignmentDetails(assignmentId) {
    currentAssignmentForDetails = assignmentId;

    fetch(`/asset-assignments/${assignmentId}/data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;

                document.getElementById('detailsModalTitle').textContent =
                    `${assignment.asset.name} → ${assignment.employee.first_name} ${assignment.employee.last_name}`;

                const modalBody = document.getElementById('detailsModalBody');
                modalBody.innerHTML = `
                    <div class="as-detail">
                        <section class="as-kv">
                            <div>
                                <h4 class="as-sec">Employee Details</h4>
                                <div class="as-k">Name</div><div class="as-v is-strong">${assignment.employee.first_name} ${assignment.employee.last_name}</div>
                                <div class="as-k" style="margin-top:8px">Number</div><div class="as-v mv-mono">${assignment.employee.employee_number}</div>
                                <div class="as-k" style="margin-top:8px">Department</div><div class="as-v">${assignment.employee.department?.name || 'Not assigned'}</div>
                            </div>
                            <div>
                                <h4 class="as-sec">Asset Details</h4>
                                <div class="as-k">Name</div><div class="as-v is-strong">${assignment.asset.name}</div>
                                <div class="as-k" style="margin-top:8px">Category</div><div class="as-v">${assignment.asset.category}</div>
                                <div class="as-k" style="margin-top:8px">SKU</div><div class="as-v mv-mono">${assignment.asset.sku || 'Not assigned'}</div>
                            </div>
                        </section>

                        <section>
                            <h4 class="as-sec">Assignment Timeline</h4>
                            <div class="as-kv">
                                <div><div class="as-k">Assigned Date</div><div class="as-v">${new Date(assignment.assignment_date).toLocaleDateString()}</div></div>
                                <div><div class="as-k">Expected Return</div><div class="as-v">${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div></div>
                                <div><div class="as-k">Quantity</div><div class="as-v">${assignment.quantity_assigned}</div></div>
                                <div><div class="as-k">Status</div><div class="as-v"><span class="badge badge-green">${assignment.status.charAt(0).toUpperCase() + assignment.status.slice(1)}</span></div></div>
                            </div>
                        </section>

                        <section>
                            <h4 class="as-sec">Condition Tracking</h4>
                            <div class="as-kv">
                                <div><div class="as-k">Condition When Assigned</div><div class="as-v"><span class="badge badge-gray">${assignment.condition_when_assigned.charAt(0).toUpperCase() + assignment.condition_when_assigned.slice(1)}</span></div></div>
                            </div>
                        </section>

                        ${assignment.assignment_notes ? `
                        <section>
                            <h4 class="as-sec">Notes</h4>
                            <div class="as-k">Assignment Notes</div>
                            <p class="as-note">${assignment.assignment_notes}</p>
                        </section>
                        ` : ''}
                    </div>
                `;

                document.getElementById('assignmentDetailsModal').style.display = 'flex';
            } else {
                alert('Failed to load assignment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignment details');
        });
}

function closeDetailsModal() {
    document.getElementById('assignmentDetailsModal').style.display = 'none';
}

function requestAsset(assetId) {
    const quantity = prompt('How many units would you like to request?', '1');
    if (quantity !== null && !isNaN(quantity) && quantity > 0) {
        alert(`Request for ${quantity} units submitted!`);
    }
}

// Document Ready Functions
document.addEventListener('DOMContentLoaded', function() {
    // Handle return form submission
    const returnForm = document.getElementById('returnAssetForm');
    if (returnForm) {
        returnForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!currentAssignmentForReturn) {
                alert('No assignment selected');
                return;
            }

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = 'Processing...';
            submitBtn.disabled = true;

            fetch(`/asset-assignments/${currentAssignmentForReturn}/return`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeReturnModal();
                    alert('Asset returned successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to return asset: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to process return');
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Handle transfer form submission
    const transferForm = document.getElementById('transferAssetForm');
    if (transferForm) {
        transferForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!currentAssignmentForTransfer) {
                alert('No assignment selected');
                return;
            }

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = 'Processing...';
            submitBtn.disabled = true;

            fetch(`/asset-assignments/${currentAssignmentForTransfer}/transfer`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeTransferModal();
                    alert('Asset transferred successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to transfer asset: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to process transfer');
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Close modals when clicking outside or pressing Escape
document.addEventListener('click', function(event) {
    const stockModal = document.getElementById('stockUpdateModal');
    const returnModal = document.getElementById('returnAssetModal');
    const detailsModal = document.getElementById('assignmentDetailsModal');
    const transferModal = document.getElementById('transferAssetModal');

    if (stockModal && event.target === stockModal) {
        closeStockModal();
    }

    if (event.target === returnModal) {
        closeReturnModal();
    }

    if (event.target === detailsModal) {
        closeDetailsModal();
    }

    if (event.target === transferModal) {
        closeTransferModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeStockModal();

        if (document.getElementById('returnAssetModal').style.display === 'flex') {
            closeReturnModal();
        }
        if (document.getElementById('assignmentDetailsModal').style.display === 'flex') {
            closeDetailsModal();
        }
        if (document.getElementById('transferAssetModal').style.display === 'flex') {
            closeTransferModal();
        }
    }
});
</script>

@endsection
