$f = 'C:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\assets\index.blade.php'
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$t = [IO.File]::ReadAllText($f, [Text.Encoding]::UTF8)
$scriptStart = $t.LastIndexOf('<script>')
$scriptsSection = $t.Substring($scriptStart).Trim()

$htmlPart = @'
@extends('layouts.app')
@php $activeTab = request()->get('tab', 'assets'); @endphp

@section('title', 'Asset Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title">📦 Asset Management</h1>
        <p class="page-subtitle">Manage company assets and track assignments</p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="flash-success mb-5">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error mb-5">❌ {{ session('error') }}</div>
@endif

{{-- Tab Navigation --}}
<div class="border-b border-gray-200 mb-5">
    <nav class="flex -mb-px gap-1">
        @php $tb = 'inline-flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap'; @endphp
        <a href="{{ route('assets.index', ['tab' => 'assets']) }}"
           class="{{ $tb }} {{ $activeTab === 'assets' ? 'border-[#1a3a5c] text-[#1a3a5c]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            📦 All Assets
            @if(isset($stats))
            <span class="{{ $activeTab === 'assets' ? 'bg-[#1a3a5c] text-white' : 'bg-gray-100 text-gray-600' }} px-2 py-0.5 rounded-full text-xs font-medium">{{ $stats['total_assets'] }}</span>
            @endif
        </a>
        <a href="{{ route('assets.index', ['tab' => 'assignments']) }}"
           class="{{ $tb }} {{ $activeTab === 'assignments' ? 'border-[#1a3a5c] text-[#1a3a5c]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            👥 Current Assignments
            @if(isset($assignmentStats))
            <span class="{{ $activeTab === 'assignments' ? 'bg-[#1a3a5c] text-white' : 'bg-gray-100 text-gray-600' }} px-2 py-0.5 rounded-full text-xs font-medium">{{ $assignmentStats['active_assignments'] }}</span>
            @endif
        </a>
        <a href="{{ route('assets.index', ['tab' => 'history']) }}"
           class="{{ $tb }} {{ $activeTab === 'history' ? 'border-[#1a3a5c] text-[#1a3a5c]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            📋 Assignment History
        </a>
        <a href="{{ route('assets.index', ['tab' => 'assign']) }}"
           class="{{ $tb }} {{ $activeTab === 'assign' ? 'border-[#1a3a5c] text-[#1a3a5c]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            🎯 Assign Assets
        </a>
    </nav>
</div>

{{-- Tab Content --}}
@if($activeTab === 'assets')

{{-- Filter + Actions Bar --}}
<div class="filter-bar">
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-sm">🔍</span>
        <input type="text" id="assetSearch" placeholder="Search assets..." class="ui-input pl-9 w-56">
    </div>
    <div>
        <select id="categoryFilter" class="ui-select w-40">
            <option value="">All Categories</option>
            <option value="laptop">Laptops</option>
            <option value="phone">Phones</option>
            <option value="equipment">Equipment</option>
            <option value="furniture">Furniture</option>
            <option value="other">Other</option>
        </select>
    </div>
    <a href="{{ route('assets.export') }}" class="btn-secondary ml-auto">📊 Export CSV</a>
    <a href="{{ route('assets.create') }}" class="btn-primary">+ Add New Asset</a>
</div>

{{-- Assets Table --}}
<div class="ui-card overflow-hidden">
    <div class="ui-card-header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-800">Asset Inventory</span>
            <span class="text-xs text-gray-400">•</span>
            <span class="text-xs text-gray-500">{{ isset($assets) ? ($assets->count() ?? 0) : 0 }} items</span>
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
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($assets) && $assets->count() > 0)
                    @foreach($assets as $asset)
                    <tr class="asset-row">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl flex-shrink-0 leading-none">
                                    @if(strtolower($asset->category ?? '') === 'laptop') 💻
                                    @elseif(strtolower($asset->category ?? '') === 'phone') 📱
                                    @elseif(strtolower($asset->category ?? '') === 'equipment') 🔧
                                    @elseif(strtolower($asset->category ?? '') === 'furniture') 🪑
                                    @else 📦 @endif
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-400">ID: #{{ $asset->id ?? 'N/A' }}{{ ($asset->sku ?? false) ? ' • SKU: '.$asset->sku : '' }}</div>
                                    @if($asset->description ?? false)<div class="text-xs text-gray-400 truncate max-w-xs">{{ $asset->description }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $catBadge = ['laptop'=>'badge badge-blue','phone'=>'badge badge-green','equipment'=>'badge badge-yellow','furniture'=>'badge badge-purple']; @endphp
                            <span class="{{ $catBadge[strtolower($asset->category ?? '')] ?? 'badge badge-gray' }}">{{ ucfirst($asset->category ?? 'Unknown') }}</span>
                        </td>
                        <td>
                            @php $stBadge = ['active'=>'badge badge-green','inactive'=>'badge badge-gray','maintenance'=>'badge badge-yellow','discontinued'=>'badge badge-red']; @endphp
                            <span class="{{ $stBadge[strtolower($asset->status ?? '')] ?? 'badge badge-gray' }}">{{ ucfirst($asset->status ?? 'Unknown') }}</span>
                        </td>
                        <td>
                            @php $stock = $asset->stock_quantity ?? 0; @endphp
                            <div class="text-sm font-semibold text-gray-900">{{ $stock }} units</div>
                            <span class="{{ $stock > 10 ? 'badge badge-green' : ($stock > 0 ? 'badge badge-yellow' : 'badge badge-red') }} mt-1">
                                {{ $stock > 10 ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('assets.show', $asset->id ?? 1) }}" class="btn-secondary btn-sm">View</a>
                                <a href="{{ route('assets.edit', $asset->id ?? 1) }}" class="btn-secondary btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <p class="empty-state-msg">No assets found. <a href="{{ route('assets.create') }}" class="text-[#1a3a5c] underline">Add your first asset</a>.</p>
                        </div>
                    </td></tr>
                @endif
            </tbody>
        </table>
    </div>
    @if(isset($assets) && method_exists($assets,'hasPages') && $assets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500">
        <span>Showing {{ $assets->firstItem() ?? 0 }}–{{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }}</span>
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
<div id="returnAssetModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1003;justify-content:center;align-items:center;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
        <div class="bg-[#1a3a5c] text-white px-5 py-4 rounded-t-xl flex items-center gap-3">
            <span>↩️</span><span class="font-semibold">Return Asset</span>
            <button onclick="closeReturnModal()" class="ml-auto text-white/80 hover:text-white text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5">
            <div id="returnAssignmentInfo" class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Assignment Details</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Asset</div><div class="font-semibold text-gray-900" id="return_asset_name">Asset Name</div></div>
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Employee</div><div class="font-semibold text-gray-900" id="return_employee_name">Employee Name</div></div>
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Assigned Date</div><div class="text-gray-700" id="return_assigned_date">Date</div></div>
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Days Assigned</div><div class="text-gray-700" id="return_days_assigned">0 days</div></div>
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Quantity</div><div class="text-gray-700" id="return_quantity">1</div></div>
                </div>
            </div>
            <form id="returnAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="return_assignment_id" name="assignment_id">
                <div class="grid gap-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="ui-label">Return Date <span class="text-red-400">*</span></label>
                            <input type="date" name="return_date" id="return_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Condition Returned <span class="text-red-400">*</span></label>
                            <select name="condition_when_returned" id="return_condition" required class="ui-select w-full">
                                <option value="">Select condition...</option>
                                <option value="new">New – Like brand new</option>
                                <option value="good">Good – Minor wear, fully functional</option>
                                <option value="fair">Fair – Noticeable wear, some issues</option>
                                <option value="poor">Poor – Significant damage</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="ui-label">Return Notes</label>
                        <textarea name="return_notes" rows="3" placeholder="Optional notes about the return..." class="ui-input w-full" style="resize:vertical;"></textarea>
                    </div>
                    <div>
                        <label class="ui-label">Update Asset Status</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer text-sm hover:bg-gray-50">
                                <input type="radio" name="update_asset_status" value="available" checked> 📦 Available
                            </label>
                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer text-sm hover:bg-gray-50">
                                <input type="radio" name="update_asset_status" value="maintenance"> 🔧 Maintenance
                            </label>
                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer text-sm hover:bg-gray-50">
                                <input type="radio" name="update_asset_status" value="damaged"> ⚠️ Damaged
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-5 pt-4 border-t border-gray-100">
                    <button type="submit" class="btn-primary flex-1 justify-center">↩️ Process Return</button>
                    <button type="button" onclick="closeReturnModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transfer Asset Modal --}}
<div id="transferAssetModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1004;justify-content:center;align-items:center;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
        <div class="bg-orange-500 text-white px-5 py-4 rounded-t-xl flex items-center gap-3">
            <span>🔄</span><span class="font-semibold">Transfer Asset</span>
            <button onclick="closeTransferModal()" class="ml-auto text-white/80 hover:text-white text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5">
            <div id="transferAssignmentInfo" class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Current Assignment</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Asset</div><div class="font-semibold text-gray-900" id="transfer_asset_name">Asset Name</div></div>
                    <div><div class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Current Employee</div><div class="font-semibold text-gray-900" id="transfer_current_employee">Employee Name</div></div>
                </div>
            </div>
            <form id="transferAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="transfer_assignment_id" name="assignment_id">
                <div class="grid gap-4">
                    <div>
                        <label class="ui-label">Transfer To <span class="text-red-400">*</span></label>
                        <select name="new_employee_id" id="transfer_new_employee_id" required class="ui-select w-full">
                            <option value="">Choose new employee...</option>
                            @if(isset($employees))
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_number }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="ui-label">Transfer Date <span class="text-red-400">*</span></label>
                            <input type="date" name="transfer_date" id="transfer_date" value="{{ now()->format('Y-m-d') }}" required class="ui-input w-full">
                        </div>
                        <div>
                            <label class="ui-label">Reason <span class="text-red-400">*</span></label>
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
                        <textarea name="transfer_notes" rows="3" placeholder="Additional notes..." class="ui-input w-full" style="resize:vertical;"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-5 pt-4 border-t border-gray-100">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">🔄 Process Transfer</button>
                    <button type="button" onclick="closeTransferModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assignment Details Modal --}}
<div id="assignmentDetailsModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto relative">
        <div class="bg-[#1a3a5c] text-white px-5 py-4 rounded-t-xl flex items-center gap-3">
            <span>📋</span>
            <span id="detailsModalTitle" class="font-semibold">Assignment Details</span>
            <button onclick="closeDetailsModal()" class="ml-auto text-white/80 hover:text-white text-2xl leading-none">&times;</button>
        </div>
        <div id="detailsModalBody" class="p-5"></div>
    </div>
</div>

'@

$newContent = $htmlPart + $scriptsSection
[IO.File]::WriteAllText($f, $newContent, $utf8NoBom)
Write-Host "Assets written. Lines: $($newContent.Split("`n").Length)"
