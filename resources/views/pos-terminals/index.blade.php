@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">

    <!-- Navigation Tabs with 4 Tabs -->
    <div class="tab-navigation" style="display: flex; border-bottom: 2px solid #dee2e6; margin-bottom: 30px; background: white; border-radius: 8px 8px 0 0; overflow: hidden;">
        <button class="tab-btn active" onclick="switchTab('overview')" style="padding: 16px 20px; background: white; border: none; border-bottom: 3px solid #007bff; cursor: pointer; font-weight: 500; color: #007bff; transition: all 0.2s ease; flex: 1; text-align: center;">
            📋 Terminal Overview
        </button>

        <button class="tab-btn" onclick="switchTab('import')" style="padding: 16px 20px; background: #f8f9fa; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; transition: all 0.2s ease; flex: 1; text-align: center;">
            📤 Smart Import
        </button>
    </div>

    <!-- Terminal Overview Tab (Clean - Table Only) -->
    <div id="overview-tab" class="tab-content active">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

            <!-- Quick Stats Summary Row (Condensed) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 4px;">{{ $stats['total_terminals'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #666; text-transform: uppercase;">Total</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #28a745; margin-bottom: 4px;">{{ $stats['active_terminals'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #28a745; text-transform: uppercase;">Active</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #dc3545; margin-bottom: 4px;">{{ $stats['faulty_terminals'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #dc3545; text-transform: uppercase;">Need Attention</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #ffc107; margin-bottom: 4px;">{{ $stats['offline_terminals'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #ffc107; text-transform: uppercase;">Offline</div>
                </div>
                <div style="text-align: center;">
                    <a href="javascript:void(0)" onclick="switchTab('analytics')" style="display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 500; transition: all 0.2s ease;">
                        📊 View Analytics
                    </a>
                </div>
            </div>

            <!-- Enhanced Filters Section -->
            <div class="filters-section" style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="font-size: 20px; color: #1e293b;">🔍 Filters & Search</h2>
                    <button onclick="clearAllFilters()" style="background: #f1f5f9; color: #475569; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">Clear All Filters</button>
                </div>

                <form method="GET" action="{{ route('pos-terminals.index') }}" class="filters-form" id="filter-form">
                    <!-- Search and Actions Row -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px;">
                        <div style="flex: 1; max-width: 400px;">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   placeholder="Search terminals..."
                                   value="{{ request('search') }}"
                                   onkeyup="handleSearch(event)"
                                   style="width: 100%; padding: 10px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" style="display: inline-block; padding: 10px 20px; border: 1px solid #6c757d; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: #6c757d; color: white;">Search</button>
                            <a href="{{ route('pos-terminals.index') }}" style="display: inline-block; padding: 10px 20px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: white; color: #333;">Clear</a>
                            <a href="{{ route('pos-terminals.create') }}" style="display: inline-block; padding: 10px 20px; border: 1px solid #007bff; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: #007bff; color: white;">Add Terminal</a>
                            <a href="{{ route('pos-terminals.export', request()->query()) }}" style="display: inline-block; padding: 10px 20px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: white; color: #333;">Export</a>
                        </div>
                    </div>

                    <!-- Filters Row -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
                        <select name="client" onchange="applyFilters()" style="padding: 10px 16px; border: 2px solid #dee2e6; border-radius: 6px; background: white; font-size: 14px; cursor: pointer; transition: border-color 0.2s ease;">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" onchange="applyFilters()" style="padding: 10px 16px; border: 2px solid #dee2e6; border-radius: 6px; background: white; font-size: 14px; cursor: pointer; transition: border-color 0.2s ease;">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>

                        <select name="region" onchange="applyFilters()" style="padding: 10px 16px; border: 2px solid #dee2e6; border-radius: 6px; background: white; font-size: 14px; cursor: pointer; transition: border-color 0.2s ease;">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>

                        <select name="city" onchange="applyFilters()" style="padding: 10px 16px; border: 2px solid #dee2e6; border-radius: 6px; background: white; font-size: 14px; cursor: pointer; transition: border-color 0.2s ease;">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>

                        <select name="province" onchange="applyFilters()" style="padding: 10px 16px; border: 2px solid #dee2e6; border-radius: 6px; background: white; font-size: 14px; cursor: pointer; transition: border-color 0.2s ease;">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Terminals Table -->
            <div style="overflow-x: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                <table class="terminals-table" style="width: 100%; border-collapse: collapse; background: white;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Terminal ID</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Client/Bank</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Merchant</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Contact</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Location</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Status</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Last Service</th>
                            <th style="background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #333; border-bottom: 2px solid #dee2e6; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terminals as $terminal)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <div style="font-weight: 600; color: #333; font-size: 14px;">{{ $terminal->terminal_id }}</div>
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <div style="font-weight: 500; color: #333; font-size: 14px;">{{ $terminal->client->company_name }}</div>
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <div style="font-weight: 500; color: #333; font-size: 14px;">{{ $terminal->merchant_name }}</div>
                                @if($terminal->business_type)
                                <div style="font-size: 12px; color: #666; margin-top: 2px;">{{ $terminal->business_type }}</div>
                                @endif
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                @if($terminal->merchant_contact_person)
                                <div style="font-weight: 500; color: #333; font-size: 13px;">{{ $terminal->merchant_contact_person }}</div>
                                @endif
                                @if($terminal->merchant_phone)
                                <div style="font-size: 12px; color: #666; margin-top: 2px;">{{ $terminal->merchant_phone }}</div>
                                @endif
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <div style="font-weight: 500; color: #333; font-size: 13px;">{{ $terminal->region ?: 'No region' }}</div>
                                @if($terminal->city)
                                <div style="font-size: 12px; color: #666; margin-top: 2px;">{{ $terminal->city }}</div>
                                @endif
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <span class="status-badge status-{{ $terminal->status }}" style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
                                    @if($terminal->status == 'active') background: #d4edda; color: #155724; @endif
                                    @if($terminal->status == 'offline') background: #fff3cd; color: #856404; @endif
                                    @if($terminal->status == 'maintenance') background: #d1ecf1; color: #0c5460; @endif
                                    @if($terminal->status == 'faulty') background: #f8d7da; color: #721c24; @endif">
                                    {{ ucfirst($terminal->status) }}
                                </span>
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                @if($terminal->last_service_date)
                                <div style="font-weight: 500; color: #333; font-size: 13px;">{{ $terminal->last_service_date->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: #666; margin-top: 2px;">{{ $terminal->last_service_date->diffForHumans() }}</div>
                                @else
                                <div style="font-size: 13px; color: #999; font-style: italic;">Never serviced</div>
                                @endif
                            </td>
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <div style="display: flex; gap: 6px;">
                                    <a href="{{ route('pos-terminals.show', $terminal) }}" style="display: inline-block; padding: 6px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; font-size: 12px; text-decoration: none; color: #333; transition: all 0.2s ease;">View</a>
                                    <a href="{{ route('pos-terminals.edit', $terminal) }}" style="display: inline-block; padding: 6px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; font-size: 12px; text-decoration: none; color: #333; transition: all 0.2s ease;">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 60px 20px;">
                                <div>
                                    <h4 style="margin: 0 0 10px 0; color: #333;">No terminals found</h4>
                                    <p style="margin: 0; color: #666;">Try adjusting your filters or <a href="{{ route('pos-terminals.create') }}" style="color: #007bff; text-decoration: none;">add your first terminal</a></p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($terminals->hasPages())
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <div style="color: #666; font-size: 14px;">
                    Showing {{ $terminals->firstItem() ?? 0 }} to {{ $terminals->lastItem() ?? 0 }} of {{ $terminals->total() }} terminals
                </div>
                <nav aria-label="Terminals pagination">
                    <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 4px;">
                        {{-- Previous Page Link --}}
                        @if ($terminals->onFirstPage())
                            <li>
                                <span style="display: inline-block; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; color: #adb5bd; font-size: 14px;">
                                    <span style="font-size: 12px;">←</span> Previous
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $terminals->previousPageUrl() }}" rel="prev" style="display: inline-block; padding: 8px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; color: #495057; text-decoration: none; font-size: 14px; transition: all 0.2s;">
                                    <span style="font-size: 12px;">←</span> Previous
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $currentPage = $terminals->currentPage();
                            $lastPage = $terminals->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        {{-- First Page --}}
                        @if($start > 1)
                            <li>
                                <a href="{{ $terminals->url(1) }}" style="display: inline-block; padding: 8px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; color: #495057; text-decoration: none; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.2s;">1</a>
                            </li>
                            @if($start > 2)
                                <li><span style="display: inline-block; padding: 8px 4px; color: #adb5bd;">...</span></li>
                            @endif
                        @endif

                        {{-- Page Links --}}
                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $currentPage)
                                <li>
                                    <span style="display: inline-block; padding: 8px 12px; background: #007bff; border: 1px solid #007bff; border-radius: 4px; color: white; font-size: 14px; font-weight: 500; min-width: 40px; text-align: center;">{{ $i }}</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $terminals->url($i) }}" style="display: inline-block; padding: 8px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; color: #495057; text-decoration: none; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.2s;">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        {{-- Last Page --}}
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <li><span style="display: inline-block; padding: 8px 4px; color: #adb5bd;">...</span></li>
                            @endif
                            <li>
                                <a href="{{ $terminals->url($lastPage) }}" style="display: inline-block; padding: 8px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; color: #495057; text-decoration: none; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.2s;">{{ $lastPage }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($terminals->hasMorePages())
                            <li>
                                <a href="{{ $terminals->nextPageUrl() }}" rel="next" style="display: inline-block; padding: 8px 12px; background: white; border: 1px solid #dee2e6; border-radius: 4px; color: #495057; text-decoration: none; font-size: 14px; transition: all 0.2s;">
                                    Next <span style="font-size: 12px;">→</span>
                                </a>
                            </li>
                        @else
                            <li>
                                <span style="display: inline-block; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; color: #adb5bd; font-size: 14px;">
                                    Next <span style="font-size: 12px;">→</span>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics & Analytics Tab -->
    <div id="analytics-tab" class="tab-content" style="display: none;">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="text-align: center; padding: 40px;">
                <h3 style="color: #666;">Analytics Dashboard</h3>
                <p style="color: #999;">Charts and analytics will be displayed here</p>
            </div>
        </div>
    </div>

    <!-- FIXED Smart Import Tab -->
    <div id="import-tab" class="tab-content" style="display: none;">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div>
                    <h3 style="margin: 0 0 10px 0; color: #333; font-size: 24px;">🚀 Terminal Data Import</h3>
                    <p style="margin: 0; color: #666; font-size: 16px;">Import terminals from Excel, CSV, or TXT files with smart column detection</p>
                </div>
                <a href="{{ route('pos-terminals.download-template') }}" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
                    📥 Download Template
                </a>
            </div>

            <!-- System Template Information -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
                <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                    <h4 style="margin: 0 0 15px 0; color: #333; font-size: 18px;">📋 Required Fields</h4>
                    <div style="margin-bottom: 15px;">
                        <span style="color: #dc3545; font-weight: 600;">Required:</span>
                        <ul style="margin: 5px 0 0 0; padding-left: 20px; color: #333;">
                            <li>Terminal ID</li>
                            <li>Merchant Name</li>
                        </ul>
                    </div>
                    <div>
                        <span style="color: #28a745; font-weight: 600;">Optional:</span>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            Contact Person, Phone, Email, Address, City, Province, Region, Business Type, Terminal Model, Serial Number, Installation Date, Status, Issues, Actions, etc.
                        </small>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                    <h4 style="margin: 0 0 15px 0; color: #333; font-size: 18px;">⚡ Smart Features</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #333; line-height: 1.6;">
                        <li>Auto-detects column headers</li>
                        <li>Processes any column order</li>
                        <li>Supports CSV, XLSX, XLS, TXT</li>
                        <li>Handles files up to 50MB</li>
                        <li>Preview before importing</li>
                        <li>Duplicate detection</li>
                    </ul>
                </div>
            </div>

            <!-- Import Form -->
            <form id="smart-import-form" action="{{ route('pos-terminals.import') }}" method="POST" enctype="multipart/form-data" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                @csrf

                <!-- Client and Mapping Selection -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label for="client_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">Select Client/Bank <span style="color: #dc3545;">*</span></label>
                        <select name="client_id" id="client_id" required style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; background: white; transition: border-color 0.2s ease;">
                            <option value="">Choose the client for these terminals...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="mapping_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">Column Mapping <span style="color: #6c757d;">(Optional)</span></label>
                        <div style="display: flex; gap: 10px;">
                            <select name="mapping_id" id="mapping_id" style="flex: 1; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; background: white; transition: border-color 0.2s ease;">
                                <option value="">Column Mapping</option>
                                @if(isset($mappings) && $mappings->count() > 0)
                                    @foreach($mappings as $mapping)
                                        <option value="{{ $mapping->id }}">
                                            {{ $mapping->mapping_name }}
                                            @if($mapping->client) ({{ $mapping->client->company_name }}) @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <a href="{{ route('pos-terminals.column-mapping') }}" target="_blank" style="padding: 12px 16px; background: white; border: 2px solid #007bff; color: #007bff; text-decoration: none; border-radius: 6px; font-size: 12px; white-space: nowrap; transition: all 0.2s ease; display: flex; align-items: center;">
                                ⚙️ Manage
                            </a>
                        </div>
                        <small style="color: #666; font-size: 12px; margin-top: 4px; display: block; line-height: 1.3;">Leave blank for automatic header detection - works with most Excel/CSV formats</small>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">Upload Data File <span style="color: #dc3545;">*</span></label>

                    <div style="border: 3px dashed #007bff; border-radius: 12px; padding: 40px; text-align: center; background: white; transition: all 0.2s ease; position: relative;" id="drop-zone">
                        <div style="font-size: 48px; margin-bottom: 16px;">📁</div>
                        <h4 style="margin: 0 0 8px 0; color: #333;">Drop your file here or click to browse</h4>
                        <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">Supports Excel (.xlsx, .xls), CSV, and TXT files up to 50MB</p>

                        <div style="display: flex; gap: 15px; align-items: center; justify-content: center;">
                            <input type="file"
                                   name="file"
                                   id="smart-file-input"
                                   accept=".csv,.xlsx,.xls,.txt"
                                   required
                                   style="display: none;">

                            <button type="button" onclick="document.getElementById('smart-file-input').click()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;">
                                📂 Choose File
                            </button>

                            <button type="button" id="preview-btn" disabled style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s ease; opacity: 0.6;">
                                👁️ Preview & Analyze
                            </button>
                        </div>

                        @error('file')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 8px;">{{ $message }}</div>
                        @enderror

                        <div id="file-info" style="margin-top: 15px; display: none;">
                            <div style="background: #e8f5e8; padding: 12px 16px; border-radius: 6px; border-left: 4px solid #28a745;">
                                <div id="file-name" style="font-weight: 500; color: #155724; font-size: 14px;"></div>
                                <div id="file-details" style="font-size: 12px; color: #155724; margin-top: 4px;"></div>
                            </div>
                        </div>

                        <div style="font-size: 12px; color: #666; margin-top: 15px; line-height: 1.4;">
                            <strong>Pro Tip:</strong> No need to worry about column order - our smart system detects and maps columns automatically!
                        </div>
                    </div>
                </div>

                <!-- Import Options -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #333; font-size: 14px;">Import Options</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 15px; background: white; border: 2px solid #e9ecef; border-radius: 8px; transition: all 0.2s ease;">
                            <input type="checkbox" name="options[]" value="skip_duplicates" checked style="margin: 0; width: 18px; height: 18px; accent-color: #007bff; margin-top: 2px;">
                            <div>
                                <div style="font-weight: 500; color: #333; margin-bottom: 4px;">Skip Duplicate Terminal IDs</div>
                                <small style="color: #666; font-size: 12px; line-height: 1.3;">Existing terminals with the same ID will be ignored during import</small>
                            </div>
                        </label>

                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 15px; background: white; border: 2px solid #e9ecef; border-radius: 8px; transition: all 0.2s ease;">
                            <input type="checkbox" name="options[]" value="update_existing" style="margin: 0; width: 18px; height: 18px; accent-color: #007bff; margin-top: 2px;">
                            <div>
                                <div style="font-weight: 500; color: #333; margin-bottom: 4px;">Update Existing Records</div>
                                <small style="color: #666; font-size: 12px; line-height: 1.3;">Override existing terminal data with new imported values</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 12px; margin-top: 30px; justify-content: flex-start;">
                    <button type="submit" id="import-submit-btn" disabled style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s ease; opacity: 0.6;">
                        <span style="font-size: 16px;">🚀</span>
                        Start Smart Import
                    </button>
                    <button type="button" onclick="resetImportForm()" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #6c757d; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;">
                        🔄 Reset Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto;">
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
        <div style="background: white; border-radius: 12px; width: 100%; max-width: 1200px; max-height: 90vh; overflow-y: auto;">
            <!-- Modal Header -->
            <div style="padding: 25px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #333; font-size: 20px; display: flex; align-items: center; gap: 10px;">
                    👁️ Smart Import Preview & Analysis
                </h3>
                <button onclick="closePreviewModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 5px;">&times;</button>
            </div>

            <!-- Modal Body -->
            <div id="preview-content" style="padding: 25px;">
                <div style="text-align: center; padding: 40px;">
                    <div style="font-size: 48px; margin-bottom: 20px;">🔄</div>
                    <h4 style="margin: 0 0 10px 0; color: #333;">Analyzing Your File...</h4>
                    <p style="margin: 0; color: #666;">Please wait while we detect columns and validate data</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="padding: 20px 25px; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                <button onclick="closePreviewModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                    Close Preview
                </button>
                <button id="proceed-import-btn" onclick="proceedWithImport()" disabled style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; opacity: 0.6;">
                    ✅ Looks Good - Proceed with Import
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal -->
<div id="processing-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1001;">
    <div style="display: flex; align-items: center; justify-content: center; height: 100vh;">
        <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; min-width: 300px;">
            <div style="font-size: 48px; margin-bottom: 20px;">⚡</div>
            <h4 style="margin: 0 0 15px 0; color: #333;">Processing Your Smart Import</h4>
            <p style="margin: 0 0 20px 0; color: #666; line-height: 1.4;">Large files are processed in chunks automatically.<br>This may take a few minutes...</p>
            <div style="width: 100%; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                <div style="width: 100%; height: 100%; background: linear-gradient(90deg, #007bff, #28a745); animation: loading 2s infinite;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced CSS -->
<style>
@keyframes loading {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Tab styles */
.tab-btn:hover {
    background: #e9ecef !important;
    color: #333 !important;
}

.tab-btn.active {
    background: white !important;
    color: #007bff !important;
    border-bottom-color: #007bff !important;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.terminals-table tbody tr:hover {
    background: #f8f9fa;
}

/* Import form enhancements */
#drop-zone:hover {
    border-color: #0056b3;
    background: #f8f9fa;
}

#drop-zone.dragover {
    border-color: #28a745;
    background: #e8f5e8;
}

/* Checkbox styling */
input[type="checkbox"]:hover + div {
    background: #f8f9fa;
}

label:hover {
    background: #f8f9fa !important;
    border-color: #007bff !important;
}

/* Responsive design */
@media (max-width: 1024px) {
    .tab-navigation {
        flex-wrap: wrap;
    }

    .tab-btn {
        flex: 1 1 50%;
        min-width: 200px;
    }
}

@media (max-width: 768px) {
    .tab-navigation {
        flex-direction: column;
    }

    .tab-btn {
        flex: 1;
        min-width: auto;
    }

    [style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<!-- CORRECTED JavaScript - This replaces all your existing JavaScript -->
<script>
// Global variables
window.importData = {
    currentFile: null,
    previewData: null,
    isProcessing: false
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Smart Import: Initializing...');

    // Initialize all components
    initializeFileUpload();
    initializePreview();
    initializeFormHandlers();

    console.log('Smart Import: Ready!');
});

// File upload initialization
function initializeFileUpload() {
    const fileInput = document.getElementById('smart-file-input');
    const dropZone = document.getElementById('drop-zone');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileDetails = document.getElementById('file-details');
    const previewBtn = document.getElementById('preview-btn');
    const submitBtn = document.getElementById('import-submit-btn');

    if (!fileInput || !dropZone) {
        console.warn('File upload elements not found');
        return;
    }

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFileSelection(file);
        }
    });

    // Drag and drop handlers
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#007bff';
        dropZone.style.backgroundColor = '#f8f9fa';
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '';
        dropZone.style.backgroundColor = '';
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '';
        dropZone.style.backgroundColor = '';

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            // Manually set the file to the input
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;

            handleFileSelection(files[0]);
        }
    });

    function handleFileSelection(file) {
        console.log('File selected:', file.name, 'Size:', formatFileSize(file.size));

        // Validate file type
        const allowedTypes = ['.csv', '.xlsx', '.xls', '.txt'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            showError('Please select a valid file type: CSV, XLSX, XLS, or TXT');
            resetFileInput();
            return;
        }

        // Validate file size (50MB limit)
        const maxSize = 50 * 1024 * 1024;
        if (file.size > maxSize) {
            showError('File size exceeds 50MB limit. Please choose a smaller file.');
            resetFileInput();
            return;
        }

        // Store file reference
        window.importData.currentFile = file;

        // Display file info
        if (fileName && fileDetails && fileInfo) {
            fileName.textContent = `✅ ${file.name}`;
            fileDetails.innerHTML = `
                <strong>Size:</strong> ${formatFileSize(file.size)} |
                <strong>Type:</strong> ${fileExtension.toUpperCase()} |
                <strong>Modified:</strong> ${new Date(file.lastModified).toLocaleDateString()}
            `;
            fileInfo.style.display = 'block';
        }

        // Enable buttons
        if (previewBtn) {
            previewBtn.disabled = false;
            previewBtn.style.opacity = '1';
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }

        console.log('File ready for processing');
    }

    function resetFileInput() {
        if (fileInput) fileInput.value = '';
        if (fileInfo) fileInfo.style.display = 'none';
        if (previewBtn) {
            previewBtn.disabled = true;
            previewBtn.style.opacity = '0.6';
        }
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
        }
        window.importData.currentFile = null;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Preview functionality
function initializePreview() {
    const previewBtn = document.getElementById('preview-btn');
    if (!previewBtn) return;

    previewBtn.addEventListener('click', function() {
        if (window.importData.isProcessing) {
            console.log('Already processing, ignoring click');
            return;
        }

        const fileInput = document.getElementById('smart-file-input');
        const clientId = document.getElementById('client_id')?.value;
        const mappingId = document.getElementById('mapping_id')?.value;

        // Validation
        if (!fileInput?.files[0]) {
            showError('Please select a file first');
            return;
        }

        if (!clientId) {
            showError('Please select a client first');
            return;
        }

        // Start preview
        startPreview(fileInput.files[0], clientId, mappingId);
    });
}

function startPreview(file, clientId, mappingId) {
    console.log('Starting preview for:', file.name);

    window.importData.isProcessing = true;
    showPreviewModal();

    // Prepare form data
    const formData = new FormData();
    formData.append('file', file);
    formData.append('client_id', clientId);
    if (mappingId) formData.append('mapping_id', mappingId);
    formData.append('preview_rows', '5');

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        formData.append('_token', csrfToken);
    }

    // Create AbortController for timeout handling
    const controller = new AbortController();
    const timeoutId = setTimeout(() => {
        controller.abort();
        console.log('Preview request timed out');
        displayPreviewError('Request timed out. Large files may need to be processed differently. Try breaking your file into smaller chunks (under 10MB each).');
    }, 480000); // 2 minute timeout

    // Make request with timeout
    fetch('/pos-terminals/preview-import', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json',
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Preview response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return response.json();
    })
    .then(data => {
        console.log('Preview response:', data);
        if (data.success) {
            displayPreviewData(data);
        } else {
            displayPreviewError(data.message || 'Preview failed');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Preview error:', error);

        if (error.name === 'AbortError') {
            displayPreviewError('Preview timed out. For large files (>10MB), try splitting them into smaller files or contact support for assistance.');
        } else {
            displayPreviewError(`Failed to preview file: ${error.message}. Please check the file format and try again.`);
        }
    })
    .finally(() => {
        window.importData.isProcessing = false;
    });
}
function showPreviewModal() {
    const modal = document.getElementById('preview-modal');
    const content = document.getElementById('preview-content');

    if (modal && content) {
        content.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <div style="font-size: 48px; margin-bottom: 20px;">🔄</div>
                <h4 style="margin: 0 0 10px 0; color: #333;">Analyzing Your File...</h4>
                <p style="margin: 0; color: #666;">Please wait while we detect columns and validate data</p>
            </div>
        `;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closePreviewModal() {
    const modal = document.getElementById('preview-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function displayPreviewData(data) {
    const content = document.getElementById('preview-content');
    const proceedBtn = document.getElementById('proceed-import-btn');

    if (!content) return;

    // Store preview data
    window.importData.previewData = data;

    let html = `
        <!-- File Analysis Summary -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745;">
                <h4 style="margin: 0 0 10px 0; color: #155724;">📊 File Analysis Results</h4>
                <div style="font-size: 14px; color: #155724; line-height: 1.6;">
                    <strong>Mapping Used:</strong> ${data.mapping_name}<br>
                    <strong>Total Columns:</strong> ${data.headers.length}<br>
                    <strong>Preview Rows:</strong> ${data.preview_data.length}<br>
                    <strong>Detection:</strong> Smart auto-mapping active
                </div>
            </div>

            <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff;">
                <h4 style="margin: 0 0 10px 0; color: #0066cc;">🎯 Column Mapping Status</h4>
                <div style="font-size: 14px; color: #0066cc; line-height: 1.6;">
                    <strong>Mapped Fields:</strong> ${(data.column_mapping_info?.mapped_fields || []).length}<br>
                    <strong>Extra Fields:</strong> ${(data.column_mapping_info?.extra_fields || []).length}<br>
                    <strong>Missing Required:</strong> ${(data.column_mapping_info?.missing_required || []).length}<br>
                    ${(data.column_mapping_info?.missing_required || []).length === 0
                        ? '<span style="color: #28a745;">✅ All required fields found!</span>'
                        : '<span style="color: #dc3545;">❌ Missing: ' + (data.column_mapping_info?.missing_required || []).join(', ') + '</span>'
                    }
                </div>
            </div>
        </div>
    `;

    // Add column mapping table
    html += `
        <div style="margin-bottom: 30px;">
            <h4 style="margin: 0 0 15px 0; color: #333;">📋 Detected Columns</h4>
            <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">#</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Column Header</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

    data.headers.forEach((header, index) => {
        const isMapped = (data.column_mapping_info?.mapped_fields || []).includes(header.toLowerCase().replace(/\s+/g, '_'));
        const isExtra = (data.column_mapping_info?.extra_fields || []).includes(header.toLowerCase().replace(/\s+/g, '_'));

        let status = '<span style="background: #ffc107; color: #856404; padding: 3px 8px; border-radius: 12px; font-size: 11px;">UNMAPPED</span>';
        if (isMapped) {
            status = '<span style="background: #28a745; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px;">MAPPED</span>';
        } else if (isExtra) {
            status = '<span style="background: #007bff; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px;">EXTRA</span>';
        }

        html += `
            <tr style="border-bottom: 1px solid #f8f9fa;">
                <td style="padding: 10px 12px;">${index + 1}</td>
                <td style="padding: 10px 12px;"><code style="background: #f8f9fa; padding: 2px 6px; border-radius: 3px;">${header}</code></td>
                <td style="padding: 10px 12px;">${status}</td>
            </tr>
        `;
    });

    html += '</tbody></table></div></div>';

    // Add preview data table
    html += `
        <div style="margin-bottom: 30px;">
            <h4 style="margin: 0 0 15px 0; color: #333;">👁️ Data Preview</h4>
            <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #007bff; color: white;">
                        <tr>
                            <th style="padding: 12px; white-space: nowrap;">Row</th>
                            <th style="padding: 12px; white-space: nowrap;">Terminal ID</th>
                            <th style="padding: 12px; white-space: nowrap;">Merchant</th>
                            <th style="padding: 12px; white-space: nowrap;">Status</th>
                            <th style="padding: 12px; white-space: nowrap;">Validation</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

    data.preview_data.forEach(row => {
        const validationBadge = row.validation_status === 'valid'
            ? '<span style="background: #28a745; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px;">✅ VALID</span>'
            : '<span style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px;">❌ ERROR</span>';

        html += `
            <tr style="border-bottom: 1px solid #f8f9fa;">
                <td style="padding: 10px 12px;">${row.row_number}</td>
                <td style="padding: 10px 12px;"><code>${row.mapped_data.terminal_id || 'N/A'}</code></td>
                <td style="padding: 10px 12px;">${row.mapped_data.merchant_name || 'N/A'}</td>
                <td style="padding: 10px 12px;">${row.mapped_data.status || 'active'}</td>
                <td style="padding: 10px 12px;">
                    ${validationBadge}
                    ${row.validation_status !== 'valid' ? `<br><small style="color: #dc3545; font-size: 11px;">${row.validation_message}</small>` : ''}
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div></div>';

    // Import readiness
    const hasErrors = data.preview_data.some(row => row.validation_status !== 'valid');
    const missingRequired = (data.column_mapping_info?.missing_required || []).length > 0;
    const canImport = !hasErrors && !missingRequired;

    html += `
        <div style="background: ${canImport ? '#e8f5e8' : '#f8d7da'}; padding: 20px; border-radius: 8px; border-left: 4px solid ${canImport ? '#28a745' : '#dc3545'};">
            <h4 style="margin: 0 0 10px 0; color: ${canImport ? '#155724' : '#721c24'};">
                ${canImport ? '✅ Ready for Import' : '❌ Import Issues Detected'}
            </h4>
            <p style="margin: 0; color: ${canImport ? '#155724' : '#721c24'};">
                ${canImport
                    ? 'Your file looks great! All required fields are present and data validation passed.'
                    : 'There are issues that need to be resolved before importing.'
                }
            </p>
        </div>
    `;

    content.innerHTML = html;

    // Enable/disable proceed button
    if (proceedBtn) {
        proceedBtn.disabled = !canImport;
        proceedBtn.style.opacity = canImport ? '1' : '0.6';
    }
}

function displayPreviewError(message) {
    const content = document.getElementById('preview-content');
    if (!content) return;

    content.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 48px; margin-bottom: 20px;">❌</div>
            <h4 style="margin: 0 0 10px 0; color: #dc3545;">Preview Failed</h4>
            <p style="margin: 0 0 20px 0; color: #666;">${message}</p>
            <div style="background: #f8d7da; padding: 15px; border-radius: 6px; color: #721c24; text-align: left;">
                <strong>Troubleshooting:</strong><br>
                • Ensure your file is a valid CSV, XLSX, XLS, or TXT format<br>
                • Check that the file contains Terminal ID and Merchant Name columns<br>
                • Verify the file is not corrupted or password-protected<br>
                • Try downloading our template and formatting your data accordingly
            </div>
        </div>
    `;
}

// Form handlers
function initializeFormHandlers() {
    // Reset form button
    const resetBtn = document.querySelector('button[onclick="resetImportForm()"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            resetImportForm();
        });
    }

    // Proceed with import
    const proceedBtn = document.getElementById('proceed-import-btn');
    if (proceedBtn) {
        proceedBtn.addEventListener('click', proceedWithImport);
    }

    // Form submission
    const form = document.getElementById('smart-import-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (window.importData.isProcessing) {
                e.preventDefault();
                return false;
            }

            showProcessingModal();
        });
    }
}

function resetImportForm() {
    console.log('Resetting import form');

    const form = document.getElementById('smart-import-form');
    if (form) form.reset();

    const fileInfo = document.getElementById('file-info');
    const previewBtn = document.getElementById('preview-btn');
    const submitBtn = document.getElementById('import-submit-btn');

    if (fileInfo) fileInfo.style.display = 'none';
    if (previewBtn) {
        previewBtn.disabled = true;
        previewBtn.style.opacity = '0.6';
    }
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
    }

    window.importData.currentFile = null;
    window.importData.previewData = null;
}

function proceedWithImport() {
    console.log('Proceeding with import');
    closePreviewModal();
    showProcessingModal();

    const form = document.getElementById('smart-import-form');
    if (form) {
        form.submit();
    }
}

function showProcessingModal() {
    const modal = document.getElementById('processing-modal');
    if (modal) {
        modal.style.display = 'block';
    }
}

// Tab switching function
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
        tab.style.display = 'none';
    });

    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = '#f8f9fa';
        btn.style.color = '#666';
        btn.style.borderBottomColor = 'transparent';
    });

    // Show selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.add('active');
        selectedTab.style.display = 'block';
    }

    // Mark button as active
    event.target.classList.add('active');
    event.target.style.background = 'white';
    event.target.style.color = '#007bff';
    event.target.style.borderBottomColor = '#007bff';
}

// Filter functions
function applyFilters() {
    const form = document.getElementById('filter-form');
    if (form) form.submit();
}

function clearAllFilters() {
    const form = document.getElementById('filter-form');
    if (!form) return;

    const inputs = form.querySelectorAll('select, input[type="text"]');

    inputs.forEach(input => {
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    });

    window.location.href = window.location.pathname;
}

function handleSearch(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        applyFilters();
    }
}

// Utility functions
function showError(message) {
    console.error('Import Error:', message);
    alert(message); // Replace with your preferred error display method
}

// Modal handlers
document.addEventListener('click', function(e) {
    const modal = document.getElementById('preview-modal');
    if (modal && e.target === modal) {
        closePreviewModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
    }
});
</script>
@endsection
