@extends('layouts.app')
@php
    $activeTab = request()->get('tab', 'assets'); 
@endphp
@section('content')
<div>
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Asset Management</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage company assets and track assignments</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($activeTab === 'assets')
                <a href="{{ route('assets.export') }}" class="btn" style="background: #111827; color: white; border-color: #111827;">
                    📊 Export CSV
                </a>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">+ Add New Asset</a>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" style="background: #f0f9f0; color: #166534; padding: 15px; border-radius: 6px; margin-block-end: 20px; border: 1px solid #d4d4d8;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 6px; margin-block-end: 20px; border: 1px solid #d4d4d8;">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="tab-navigation" style="margin-block-end: 30px;">
        <div style="display: flex; border-bottom: 2px solid #e0e0e0; margin-block-end: 20px;">
            <a href="{{ route('assets.index', ['tab' => 'assets']) }}" 
               class="tab-link {{ $activeTab === 'assets' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'assets' ? '#111827' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'assets' ? '#111827' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">📦</span>
                All Assets
                @if(isset($stats))
                    <span style="background: {{ $activeTab === 'assets' ? '#111827' : '#ddd' }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 8px;">
                        {{ $stats['total_assets'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('assets.index', ['tab' => 'assignments']) }}" 
               class="tab-link {{ $activeTab === 'assignments' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'assignments' ? '#111827' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'assignments' ? '#111827' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">👥</span>
                Current Assignments
                @if(isset($assignmentStats))
                    <span style="background: {{ $activeTab === 'assignments' ? '#111827' : '#ddd' }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 8px;">
                        {{ $assignmentStats['active_assignments'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('assets.index', ['tab' => 'history']) }}" 
               class="tab-link {{ $activeTab === 'history' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'history' ? '#111827' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'history' ? '#111827' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">📋</span>
                Assignment History
            </a>
            
            <a href="{{ route('assets.index', ['tab' => 'assign']) }}" 
               class="tab-link {{ $activeTab === 'assign' ? 'active' : '' }}"
               style="padding: 15px 25px; text-decoration: none; color: {{ $activeTab === 'assign' ? '#111827' : '#666' }}; border-bottom: 3px solid {{ $activeTab === 'assign' ? '#111827' : 'transparent' }}; font-weight: 500; transition: all 0.3s ease;">
                <span style="font-size: 18px; margin-right: 8px;">🎯</span>
                Assign Assets
            </a>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'assets')
            <!-- Assets Table Section -->
            <div class="assets-table-section">
                <!-- Table Header with Search/Filters -->
                <div style="background: white; border-radius: 12px 12px 0 0; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0; font-size: 20px; font-weight: 600; color: #1f2937;">Asset Inventory</h3>
                            <span style="color: #6b7280; font-size: 14px;">{{ isset($assets) ? ($assets->count() ?? 0) : 0 }} items total</span>
                        </div>
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;">🔍</span>
                                <input type="text" id="assetSearch" placeholder="Search assets..." 
                                       style="padding: 10px 12px 10px 36px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; width: 240px; transition: all 0.2s ease;">
                            </div>
                            <select id="categoryFilter" style="padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white;">
                                <option value="">All Categories</option>
                                <option value="laptop">Laptops</option>
                                <option value="phone">Phones</option>
                                <option value="equipment">Equipment</option>
                                <option value="furniture">Furniture</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Assets Table -->
                <div style="background: white; border-radius: 0 0 12px 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div style="overflow-x: auto;">
                        <table class="assets-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Asset Details
                                    </th>
                                    <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Category
                                    </th>
                                    <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Status
                                    </th>
                                    <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Stock
                                    </th>
                                    <th style="padding: 16px 20px; text-align: center; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($assets) && $assets->count() > 0)
                                    @foreach($assets as $asset)
                                        <tr class="asset-row" style="border-bottom: 1px solid #f3f4f6; transition: all 0.2s ease;">
                                            <!-- Asset Details -->
                                            <td style="padding: 20px;">
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <div style="width: 48px; height: 48px; border-radius: 10px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                                        @if(strtolower($asset->category ?? '') === 'laptop')
                                                            💻
                                                        @elseif(strtolower($asset->category ?? '') === 'phone')
                                                            📱
                                                        @elseif(strtolower($asset->category ?? '') === 'equipment')
                                                            🔧
                                                        @elseif(strtolower($asset->category ?? '') === 'furniture')
                                                            🪑
                                                        @else
                                                            📦
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600; color: #1f2937; font-size: 15px; margin-bottom: 2px;">
                                                            {{ $asset->name }}
                                                        </div>
                                                        <div style="font-size: 12px; color: #6b7280;">
                                                            ID: #{{ $asset->id ?? 'N/A' }}
                                                            @if($asset->sku ?? false)
                                                                • SKU: {{ $asset->sku }}
                                                            @endif
                                                        </div>
                                                        @if($asset->description ?? false)
                                                            <div style="font-size: 12px; color: #9ca3af; margin-top: 2px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                {{ $asset->description }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Category -->
                                            <td style="padding: 20px;">
                                                <span style="display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
                                                    @if(strtolower($asset->category ?? '') === 'laptop') background: #dbeafe; color: #1d4ed8;
                                                    @elseif(strtolower($asset->category ?? '') === 'phone') background: #dcfce7; color: #166534;
                                                    @elseif(strtolower($asset->category ?? '') === 'equipment') background: #fef3c7; color: #92400e;
                                                    @elseif(strtolower($asset->category ?? '') === 'furniture') background: #fce7f3; color: #be185d;
                                                    @else background: #f3f4f6; color: #6b7280; @endif">
                                                    {{ ucfirst($asset->category ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            
                                            <!-- Status -->
                                            <td style="padding: 20px;">
                                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize;
                                                    @if(strtolower($asset->status ?? '') === 'active') background: #dcfce7; color: #166534;
                                                    @elseif(strtolower($asset->status ?? '') === 'inactive') background: #f3f4f6; color: #6b7280;
                                                    @elseif(strtolower($asset->status ?? '') === 'maintenance') background: #fef3c7; color: #92400e;
                                                    @elseif(strtolower($asset->status ?? '') === 'discontinued') background: #fecaca; color: #dc2626;
                                                    @else background: #f3f4f6; color: #6b7280; @endif">
                                                    <span style="width: 6px; height: 6px; border-radius: 50%;
                                                        @if(strtolower($asset->status ?? '') === 'active') background: #22c55e;
                                                        @elseif(strtolower($asset->status ?? '') === 'inactive') background: #9ca3af;
                                                        @elseif(strtolower($asset->status ?? '') === 'maintenance') background: #f59e0b;
                                                        @elseif(strtolower($asset->status ?? '') === 'discontinued') background: #ef4444;
                                                        @else background: #9ca3af; @endif">
                                                    </span>
                                                    {{ ucfirst($asset->status ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            
                                            <!-- Stock -->
                                            <td style="padding: 20px;">
                                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                                        {{ $asset->stock_quantity ?? 0 }} units
                                                    </div>
                                                    <span style="display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; width: fit-content;
                                                        @php
                                                            $stock = $asset->stock_quantity ?? 0;
                                                            if($stock > 10) {
                                                                echo 'background: #dcfce7; color: #166534;';
                                                            } elseif($stock > 0) {
                                                                echo 'background: #fef3c7; color: #92400e;';
                                                            } else {
                                                                echo 'background: #fecaca; color: #dc2626;';
                                                            }
                                                        @endphp">
                                                        @php
                                                            $stock = $asset->stock_quantity ?? 0;
                                                            if($stock > 10) echo 'In Stock';
                                                            elseif($stock > 0) echo 'Low Stock';
                                                            else echo 'Out of Stock';
                                                        @endphp
                                                    </span>
                                                </div>
                                            </td>
                                            
                                            <!-- Actions -->
                                            <td style="padding: 20px; text-align: center;">
                                                <div style="display: flex; gap: 6px; justify-content: center;">
                                                    <a href="{{ route('assets.show', $asset->id ?? 1) }}" 
                                                       style="padding: 8px 12px; border-radius: 6px; border: 1px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer; transition: all 0.2s ease; text-decoration: none; font-size: 12px; font-weight: 500;"
                                                       title="View Details">
                                                        View
                                                    </a>
                                                    <a href="{{ route('assets.edit', $asset->id ?? 1) }}" 
                                                       style="padding: 8px 12px; border-radius: 6px; border: 1px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer; transition: all 0.2s ease; text-decoration: none; font-size: 12px; font-weight: 500;"
                                                       title="Edit Asset">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="padding: 60px 20px; text-align: center;">
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                                                <div style="font-size: 48px; opacity: 0.5;">📦</div>
                                                <div>
                                                    <h4 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #374151;">No Assets Found</h4>
                                                    <p style="margin: 0; color: #6b7280; font-size: 14px;">Start by adding your first asset to the inventory</p>
                                                </div>
                                                <a href="{{ route('assets.create') }}" 
                                                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #111827; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">
                                                    <span>+</span>
                                                    Add First Asset
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Custom Pagination -->
                    @if(isset($assets) && method_exists($assets, 'hasPages') && $assets->hasPages())
                        <div style="padding: 20px; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                            <div style="color: #6b7280; font-size: 14px;">
                                Showing {{ $assets->firstItem() ?? 0 }} to {{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }} assets
                            </div>
                            <div style="display: flex; gap: 4px; align-items: center;">
                                <!-- Previous button -->
                                @if ($assets->onFirstPage())
                                    <span style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #9ca3af; background: #f9fafb;">Previous</span>
                                @else
                                    <a href="{{ $assets->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #374151; background: white; text-decoration: none; transition: all 0.2s ease;">Previous</a>
                                @endif

                                <!-- Page numbers -->
                                @php
                                    $currentPage = $assets->currentPage();
                                    $lastPage = $assets->lastPage();
                                    $start = max($currentPage - 2, 1);
                                    $end = min($currentPage + 2, $lastPage);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $assets->url(1) }}" style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #374151; background: white; text-decoration: none; transition: all 0.2s ease;">1</a>
                                    @if($start > 2)
                                        <span style="padding: 8px 4px; color: #9ca3af;">...</span>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $currentPage)
                                        <span style="padding: 8px 12px; border: 1px solid #111827; border-radius: 6px; color: white; background: #111827; font-weight: 600;">{{ $i }}</span>
                                    @else
                                        <a href="{{ $assets->url($i) }}" style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #374151; background: white; text-decoration: none; transition: all 0.2s ease;">{{ $i }}</a>
                                    @endif
                                @endfor

                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <span style="padding: 8px 4px; color: #9ca3af;">...</span>
                                    @endif
                                    <a href="{{ $assets->url($lastPage) }}" style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #374151; background: white; text-decoration: none; transition: all 0.2s ease;">{{ $lastPage }}</a>
                                @endif

                                <!-- Next button -->
                                @if ($assets->hasMorePages())
                                    <a href="{{ $assets->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #374151; background: white; text-decoration: none; transition: all 0.2s ease;">Next</a>
                                @else
                                    <span style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; color: #9ca3af; background: #f9fafb;">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($activeTab === 'assignments')
            @include('assets.partials.assignments-tab')
        @elseif($activeTab === 'history')
            @include('assets.partials.history-tab')
        @elseif($activeTab === 'assign')
            @include('assets.partials.assign-tab')
        @endif
    </div>
</div>

<!-- Return Asset Modal -->
<div id="returnAssetModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1003; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: #111827; color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>↩️</span>
                <span>Return Asset</span>
            </h3>
            <button onclick="closeReturnModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <!-- Assignment Info Display -->
            <div id="returnAssignmentInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-block-end: 20px;">
                <h4 style="margin: 0 0 15px 0; color: #333;">Assignment Details</h4>
                
                <div style="display: grid; gap: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Asset</label>
                            <div style="font-weight: bold; color: #333;" id="return_asset_name">Asset Name</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Employee</label>
                            <div style="font-weight: bold; color: #333;" id="return_employee_name">Employee Name</div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Assigned Date</label>
                            <div style="color: #333;" id="return_assigned_date">Date</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Days Assigned</label>
                            <div style="color: #333;" id="return_days_assigned">0 days</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Quantity</label>
                            <div style="color: #333;" id="return_quantity">1</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="returnAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="return_assignment_id" name="assignment_id">
                
                <div style="display: grid; gap: 20px;">
                    <!-- Return Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Return Date <span style="color: #f44336;">*</span>
                            </label>
                            <input type="date" name="return_date" id="return_date" value="{{ now()->format('Y-m-d') }}" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Condition When Returned <span style="color: #f44336;">*</span>
                            </label>
                            <select name="condition_when_returned" id="return_condition" required 
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select condition...</option>
                                <option value="new">New - Like brand new</option>
                                <option value="good">Good - Minor wear, fully functional</option>
                                <option value="fair">Fair - Noticeable wear, some issues</option>
                                <option value="poor">Poor - Significant damage/issues</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Return Notes -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Return Notes
                        </label>
                        <textarea name="return_notes" rows="3" 
                                  placeholder="Optional notes about the return (e.g., reason for return, any issues)..." 
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                    
                    <!-- Asset Status Update -->
                    <div>
                        <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                            Update Asset Status
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="available" checked>
                                <span>📦 Available</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="maintenance">
                                <span>🔧 Maintenance</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="update_asset_status" value="damaged">
                                <span>⚠️ Damaged</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; background: #111827; border-color: #111827; color: white; padding: 12px; border-radius: 6px; border: none; cursor: pointer;">
                        <span style="font-size: 16px; margin-right: 8px;">↩️</span>
                        Process Return
                    </button>
                    <button type="button" onclick="closeReturnModal()" style="padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Asset Modal -->
<div id="transferAssetModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1004; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: #ff9800; color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>🔄</span>
                <span>Transfer Asset</span>
            </h3>
            <button onclick="closeTransferModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <!-- Current Assignment Info -->
            <div id="transferAssignmentInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-block-end: 20px;">
                <h4 style="margin: 0 0 15px 0; color: #333;">Current Assignment</h4>
                
                <div style="display: grid; gap: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Asset</label>
                            <div style="font-weight: bold; color: #333;" id="transfer_asset_name">Asset Name</div>
                        </div>
                        <div>
                            <label style="font-size: 12px; color: #666; text-transform: uppercase;">Current Employee</label>
                            <div style="font-weight: bold; color: #333;" id="transfer_current_employee">Employee Name</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="transferAssetForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="transfer_assignment_id" name="assignment_id">
                
                <div style="display: grid; gap: 20px;">
                    <!-- New Employee Selection -->
                    <div>
                        <label style="display: block; margin-block-end: 8px; font-weight: 600; color: #333;">
                            Transfer To <span style="color: #f44336;">*</span>
                        </label>
                        <select name="new_employee_id" id="transfer_new_employee_id" required 
                                style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                            <option value="">Choose new employee...</option>
                            @if(isset($employees))
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_number }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <!-- Transfer Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Transfer Date <span style="color: #f44336;">*</span>
                            </label>
                            <input type="date" name="transfer_date" id="transfer_date" value="{{ now()->format('Y-m-d') }}" required 
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                                Reason <span style="color: #f44336;">*</span>
                            </label>
                            <select name="transfer_reason" required 
                                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                                <option value="">Select reason...</option>
                                <option value="employee_departure">Employee Departure</option>
                                <option value="role_change">Role Change</option>
                                <option value="department_transfer">Department Transfer</option>
                                <option value="project_completion">Project Completion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Transfer Notes -->
                    <div>
                        <label style="display: block; margin-block-end: 5px; font-weight: 600; color: #333;">
                            Transfer Notes
                        </label>
                        <textarea name="transfer_notes" rows="3" 
                                  placeholder="Additional notes about this transfer..." 
                                  style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn btn-warning" style="flex: 1; background: #ff9800; border-color: #ff9800; color: white; padding: 12px; border-radius: 6px; border: none; cursor: pointer;">
                        <span style="font-size: 16px; margin-right: 8px;">🔄</span>
                        Process Transfer
                    </button>
                    <button type="button" onclick="closeTransferModal()" style="padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assignment Details Modal -->
<div id="assignmentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: #111827; color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>📋</span>
                <span id="detailsModalTitle">Assignment Details</span>
            </h3>
            <button onclick="closeDetailsModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div id="detailsModalBody" style="padding: 20px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
/* Table Hover Effects */
.asset-row:hover {
    background: #fafbfc !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.asset-row:hover td:first-child div:first-child {
    background: #f0f9ff;
    transform: scale(1.05);
}

.asset-row:hover button,
.asset-row:hover a {
    border-color: #111827 !important;
    color: #111827 !important;
    background: #f9fafb !important;
}

.asset-row:hover button:hover,
.asset-row:hover a:hover {
    background: #111827 !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(17, 24, 39, 0.3);
}

/* Search and Filter Styles */
#assetSearch:focus,
#categoryFilter:focus {
    outline: none;
    border-color: #111827;
    box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.1);
}

/* Pagination Styles */
.pagination-container a:hover {
    border-color: #111827 !important;
    color: #111827 !important;
    background: #f9fafb !important;
}

/* Existing Styles */
.metric-card {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.asset-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.asset-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.assignment-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #111827;
}

.assignment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.assignment-card.overdue {
    border-left-color: #f44336;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-active { background: #e8f5e8; color: #2e7d32; }
.status-inactive { background: #f5f5f5; color: #666; }
.status-discontinued { background: #ffebee; color: #d32f2f; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-offline { background: #ffebee; color: #d32f2f; }

.stock-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.stock-in_stock { background: #e8f5e8; color: #2e7d32; }
.stock-low_stock { background: #fff3e0; color: #f57c00; }
.stock-out_of_stock { background: #ffebee; color: #d32f2f; }

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #111827;
    color: #111827;
    text-decoration: none;
}

.btn-primary {
    background: #111827;
    color: white;
    border-color: #111827;
}

.btn-primary:hover {
    background: #374151;
    border-color: #374151;
    color: white;
}

.btn-small {
    padding: 8px 12px;
    font-size: 14px;
}

.btn-success {
    background: #4caf50;
    color: white;
    border-color: #4caf50;
}

.btn-success:hover {
    background: #388e3c;
    border-color: #388e3c;
    color: white;
}

.btn-warning {
    background: #ff9800;
    color: white;
    border-color: #ff9800;
}

.btn-warning:hover {
    background: #f57c00;
    border-color: #f57c00;
    color: white;
}

.btn-danger {
    background: #f44336;
    color: white;
    border-color: #f44336;
}

.btn-danger:hover {
    background: #d32f2f;
    border-color: #d32f2f;
    color: white;
}

.tab-link:hover {
    color: #111827 !important;
    border-bottom-color: #111827 !important;
}

.modal-action-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: start;
    inline-size: 100%;
}

.modal-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.alert {
    border-radius: 6px;
    padding: 15px;
    margin-block-end: 20px;
}

.alert-success {
    background: #f0f9f0;
    color: #166534;
    border: 1px solid #d4d4d8;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #d4d4d8;
}

/* Table styles for assignment tabs */
.assignment-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.assignment-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.assignment-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.assignment-table tbody tr:hover {
    background: #f8f9fa;
}

.employee-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.asset-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.asset-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}
</style>

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
                    <div style="display: grid; gap: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h4 style="margin-block-end: 10px; color: #333;">👤 Employee Details</h4>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div><strong>Name:</strong> ${assignment.employee.first_name} ${assignment.employee.last_name}</div>
                                    <div><strong>Number:</strong> ${assignment.employee.employee_number}</div>
                                    <div><strong>Department:</strong> ${assignment.employee.department?.name || 'Not assigned'}</div>
                                </div>
                            </div>
                            <div>
                                <h4 style="margin-block-end: 10px; color: #333;">📦 Asset Details</h4>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div><strong>Name:</strong> ${assignment.asset.name}</div>
                                    <div><strong>Category:</strong> ${assignment.asset.category}</div>
                                    <div><strong>SKU:</strong> ${assignment.asset.sku || 'Not assigned'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">📋 Assignment Timeline</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div><strong>Assigned Date:</strong> ${new Date(assignment.assignment_date).toLocaleDateString()}</div>
                                    <div><strong>Expected Return:</strong> ${assignment.expected_return_date ? new Date(assignment.expected_return_date).toLocaleDateString() : 'Not set'}</div>
                                    <div><strong>Quantity:</strong> ${assignment.quantity_assigned}</div>
                                    <div><strong>Status:</strong> <span style="padding: 4px 8px; background: #e8f5e8; color: #2e7d32; border-radius: 12px; font-size: 12px;">${assignment.status.charAt(0).toUpperCase() + assignment.status.slice(1)}</span></div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">🔧 Condition Tracking</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div><strong>Condition When Assigned:</strong> <span style="padding: 4px 8px; background: #e3f2fd; color: #1976d2; border-radius: 12px; font-size: 12px;">${assignment.condition_when_assigned.charAt(0).toUpperCase() + assignment.condition_when_assigned.slice(1)}</span></div>
                            </div>
                        </div>
                        
                        ${assignment.assignment_notes ? `
                        <div>
                            <h4 style="margin-block-end: 10px; color: #333;">📝 Notes</h4>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                <div><strong>Assignment Notes:</strong><br>${assignment.assignment_notes}</div>
                            </div>
                        </div>
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
            submitBtn.innerHTML = '<span style="font-size: 16px; margin-right: 8px;">⏳</span>Processing...';
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
            submitBtn.innerHTML = '<span style="font-size: 16px; margin-right: 8px;">⏳</span>Processing...';
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