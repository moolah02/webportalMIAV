@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <!-- Statistics Header with Toggle -->
    <div id="stats-section" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin: 0; color: #333; font-size: 20px;">📊 Terminal Statistics</h2>
            <button onclick="toggleStats()" id="stats-toggle" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; color: #666;">
                <span id="toggle-text">Hide Details</span> <span id="toggle-icon">▲</span>
            </button>
        </div>
        
        <!-- Quick Stats Row (Always Visible) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                <div id="total-count" style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 4px;">{{ $stats['total_terminals'] ?? 0 }}</div>
                <div style="font-size: 12px; color: #666; text-transform: uppercase;">Total</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 6px;">
                <div id="active-count" style="font-size: 24px; font-weight: 700; color: #155724; margin-bottom: 4px;">{{ $stats['active_terminals'] ?? 0 }}</div>
                <div style="font-size: 12px; color: #155724; text-transform: uppercase;">Active</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f8d7da; border-radius: 6px;">
                <div id="faulty-count" style="font-size: 24px; font-weight: 700; color: #721c24; margin-bottom: 4px;">{{ $stats['faulty_terminals'] ?? 0 }}</div>
                <div style="font-size: 12px; color: #721c24; text-transform: uppercase;">Need Attention</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 6px;">
                <div id="offline-count" style="font-size: 24px; font-weight: 700; color: #856404; margin-bottom: 4px;">{{ $stats['offline_terminals'] ?? 0 }}</div>
                <div style="font-size: 12px; color: #856404; text-transform: uppercase;">Offline</div>
            </div>
        </div>

        <!-- Detailed Stats & Charts (Collapsible) - FIXED -->
        <div id="detailed-stats" class="detailed-stats-container">
            <!-- Charts Section with Fixed Heights -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">📊 Status Distribution</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <div class="chart-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">🗺️ Terminals by Location</h3>
                    <div style="position: relative; height: 250px;">
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-navigation" style="display: flex; border-bottom: 2px solid #dee2e6; margin-bottom: 30px; background: white; border-radius: 8px 8px 0 0; overflow: hidden;">
        <button class="tab-btn active" onclick="switchTab('overview')" style="padding: 16px 24px; background: white; border: none; border-bottom: 3px solid #007bff; cursor: pointer; font-weight: 500; color: #007bff; transition: all 0.2s ease; flex: 1;">
            📊 Terminal Overview
        </button>
        <button class="tab-btn" onclick="switchTab('import')" style="padding: 16px 24px; background: #f8f9fa; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; transition: all 0.2s ease; flex: 1;">
            📤 Import Bank Data
        </button>
        <button class="tab-btn" onclick="switchTab('field')" style="padding: 16px 24px; background: #f8f9fa; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; transition: all 0.2s ease; flex: 1;">
            🔧 Field Updates
        </button>
    </div>

    <!-- Terminal Overview Tab -->
    <div id="overview-tab" class="tab-content active">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
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
                            @if(is_array($statusOptions) || is_object($statusOptions))
                                @foreach($statusOptions as $slug => $name)
                                    <option value="{{ $slug }}" {{ request('status') == $slug ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                            @endif
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

    <!-- Import Tab -->
    <div id="import-tab" class="tab-content" style="display: none;">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #333; font-size: 24px;">📤 Import Terminal Data</h3>
            <p style="margin: 0 0 30px 0; color: #666; font-size: 16px;">Bulk import terminal data from bank or client CSV files with flexible column mapping</p>

            <form action="{{ route('pos-terminals.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
                @csrf
                
                <!-- Client Selection -->
                <div style="margin-bottom: 24px;">
                    <label for="client_id" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Select Client/Bank *</label>
                    <select name="client_id" id="client_id" required style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                        <option value="">Choose the client for these terminals...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Column Mapping Selection -->
                <div style="margin-bottom: 24px;">
                    <label for="mapping_id" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Column Mapping (Optional)</label>
                    <div style="display: flex; gap: 12px; align-items: flex-end;">
                        <select name="mapping_id" id="mapping_id" style="flex: 1; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                            <option value="">Use Default Mapping</option>
                            @if(isset($mappings) && $mappings->count() > 0)
                                @foreach($mappings as $mapping)
                                    <option value="{{ $mapping->id }}" data-description="{{ $mapping->description }}">
                                        {{ $mapping->mapping_name }}
                                        @if($mapping->client)
                                            ({{ $mapping->client->company_name }})
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('pos-terminals.column-mapping') }}" style="padding: 8px 16px; font-size: 12px; min-width: auto; display: inline-block; border: 1px solid #dee2e6; border-radius: 6px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: white; color: #333;" target="_blank">
                                Create New Mapping
                            </a>
                        </div>
                    </div>
                    <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">Select a pre-configured mapping for this bank's specific CSV format, or use the default mapping</small>
                </div>

                <!-- File Upload -->
                <div style="border: 3px dashed #dee2e6; border-radius: 8px; padding: 40px; text-align: center; background: #f8f9fa; margin-bottom: 24px; transition: border-color 0.2s ease;">
                    <div style="font-size: 48px; margin-bottom: 16px;">📁</div>
                    <h4 style="margin: 0 0 8px 0; color: #333;">Upload Your CSV File</h4>
                    <p style="margin: 0 0 20px 0; color: #666;">Select your terminal data CSV file with bank information</p>
                    
                    <input type="file" 
                           name="file" 
                           id="csvFile"
                           accept=".csv" 
                           required
                           style="margin: 0 auto 16px auto; display: block; padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; background: white;">
                    
                    @error('file')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                    
                    <div id="fileName" style="font-weight: 500; color: #28a745; margin-bottom: 8px;"></div>
                    <div style="font-size: 12px; color: #666;">
                        Supported formats: CSV only • Max size: 10MB<br>
                        <span style="color: #007bff; font-weight: 500;">💡 Excel files: Save as CSV (Comma delimited) before uploading</span>
                    </div>
                </div>

                <!-- Import Options -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Import Options</label>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border: 1px solid #e9ecef; border-radius: 8px; transition: all 0.2s ease;">
                            <input type="checkbox" name="options[]" value="skip_duplicates" checked style="margin: 0; width: 16px; height: 16px; accent-color: #007bff;">
                            <div>
                                <div>Skip duplicate terminal IDs</div>
                                <small style="color: #666; font-size: 12px; margin-top: 2px; display: block;">Existing terminals with same ID will be ignored</small>
                            </div>
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border: 1px solid #e9ecef; border-radius: 8px; transition: all 0.2s ease;">
                            <input type="checkbox" name="options[]" value="update_existing" style="margin: 0; width: 16px; height: 16px; accent-color: #007bff;">
                            <div>
                                <div>Update existing terminals with new data</div>
                                <small style="color: #666; font-size: 12px; margin-top: 2px; display: block;">Override existing terminal data with imported values</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button type="button" onclick="resetForm()" style="padding: 10px 20px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: white; color: #333;">Reset Form</button>
                    <a href="{{ route('pos-terminals.download-template') }}" style="display: inline-block; padding: 10px 20px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: white; color: #333;">
                        <span style="margin-right: 6px;">📥</span>
                        Download Template
                    </a>
                    <button type="submit" id="submitBtn" style="display: inline-block; padding: 10px 20px; border: 1px solid #007bff; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: #007bff; color: white;">
                        <span style="margin-right: 6px;">⚡</span>
                        Process Import
                    </button>
                </div>
            </form>

            <!-- Import Tips -->
            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #dee2e6;">
                <h4 style="margin: 0 0 20px 0; color: #333;">💡 Import Tips</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div style="display: flex; gap: 12px; padding: 16px; background: #f8f9ff; border: 1px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 20px; flex-shrink: 0;">📋</div>
                        <div>
                            <strong style="display: block; margin-bottom: 4px; color: #333;">CSV Format</strong>
                            <p style="margin: 0; font-size: 12px; color: #666; line-height: 1.4;">Ensure your file is saved as CSV (Comma delimited) format</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; padding: 16px; background: #f8f9ff; border: 1px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 20px; flex-shrink: 0;">🎯</div>
                        <div>
                            <strong style="display: block; margin-bottom: 4px; color: #333;">Required Fields</strong>
                            <p style="margin: 0; font-size: 12px; color: #666; line-height: 1.4;">Terminal ID and Merchant Name are required for each row</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; padding: 16px; background: #f8f9ff; border: 1px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 20px; flex-shrink: 0;">🔄</div>
                        <div>
                            <strong style="display: block; margin-bottom: 4px; color: #333;">Dynamic Updates</strong>
                            <p style="margin: 0; font-size: 12px; color: #666; line-height: 1.4;">Technicians can update imported terminals via mobile app</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; padding: 16px; background: #f8f9ff; border: 1px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 20px; flex-shrink: 0;">⚙️</div>
                        <div>
                            <strong style="display: block; margin-bottom: 4px; color: #333;">Column Mapping</strong>
                            <p style="margin: 0; font-size: 12px; color: #666; line-height: 1.4;">Create custom mappings for different bank CSV formats</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Updates Tab -->
    <div id="field-tab" class="tab-content" style="display: none;">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #333; font-size: 24px;">🔧 Technician Field Updates</h3>
            <p style="margin: 0 0 30px 0; color: #666; font-size: 16px;">Update terminal status and service information after field visits</p>

            <form class="field-update-form">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Terminal ID</label>
                        <input type="text" placeholder="Enter Terminal ID" style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Service Type</label>
                        <select style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                            <option>Maintenance</option>
                            <option>Installation</option>
                            <option>Repair</option>
                            <option>Inspection</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Current Status</label>
                        <select style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                            <option>Active</option>
                            <option>Offline</option>
                            <option>Maintenance</option>
                            <option>Faulty</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Visit Date</label>
                        <input type="datetime-local" style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Service Notes</label>
                    <textarea placeholder="Describe the work performed..." rows="4" style="width: 100%; padding: 12px 16px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease; resize: vertical; min-height: 100px;"></textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button type="submit" style="display: inline-block; padding: 10px 20px; border: 1px solid #007bff; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: #007bff; color: white;">Update Terminal</button>
                    <button type="reset" style="display: inline-block; padding: 10px 20px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; background: white; color: #333;">Clear Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Fixed detailed stats container */
.detailed-stats-container {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
    overflow: hidden;
    max-height: 400px; /* Fixed maximum height */
    transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;
}

.detailed-stats-container.collapsed {
    max-height: 0;
    padding-top: 0;
    opacity: 0;
}

/* Ensure chart containers have fixed dimensions */
.chart-container {
    position: relative;
    overflow: hidden;
}

.chart-container canvas {
    max-width: 100% !important;
    height: auto !important;
}

/* Professional Pagination Styles */
nav ul li a:hover {
    background: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
}

.custom-pagination a:hover {
    background: #f8f9fa !important;
    border-color: #007bff !important;
    color: #007bff !important;
}

.custom-pagination button:disabled {
    opacity: 0.5;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.tab-btn:hover {
    background: #e9ecef;
    color: #333;
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

.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #007bff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .tab-navigation {
        flex-direction: column;
    }
    
    .custom-pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    [style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    [style*="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    [style*="display: flex"]:not(.custom-pagination) {
        flex-direction: column !important;
        align-items: stretch !important;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Toggle stats section - FIXED
function toggleStats() {
    const detailedStats = document.getElementById('detailed-stats');
    const toggleText = document.getElementById('toggle-text');
    const toggleIcon = document.getElementById('toggle-icon');
    
    if (detailedStats.classList.contains('collapsed')) {
        detailedStats.classList.remove('collapsed');
        toggleText.textContent = 'Hide Details';
        toggleIcon.textContent = '▲';
        // Re-render charts when showing
        setTimeout(() => {
            if (statusChart) statusChart.resize();
            if (locationChart) locationChart.resize();
        }, 350);
    } else {
        detailedStats.classList.add('collapsed');
        toggleText.textContent = 'Show Details';
        toggleIcon.textContent = '▼';
    }
}

// Tab Switching
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

// Global variables for charts
let statusChart = null;
let locationChart = null;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Initialize detailed stats as expanded
    const detailedStats = document.getElementById('detailed-stats');
    if (detailedStats) {
        detailedStats.classList.remove('collapsed');
    }
    
    // Initialize charts after a short delay to ensure DOM is ready
    setTimeout(() => {
        initializeCharts();
    }, 100);
    setupFileUpload();
});

// Initialize charts with current data - FIXED
function initializeCharts() {
    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    const locationCtx = document.getElementById('locationChart')?.getContext('2d');
    
    if (!statusCtx || !locationCtx) {
        console.log('Charts not ready, retrying...');
        setTimeout(initializeCharts, 500);
        return;
    }

    // Get current stats from the page
    const totalTerminals = parseInt(document.getElementById('total-count').textContent) || 0;
    const activeTerminals = parseInt(document.getElementById('active-count').textContent) || 0;
    const offlineTerminals = parseInt(document.getElementById('offline-count').textContent) || 0;
    const faultyTerminals = parseInt(document.getElementById('faulty-count').textContent) || 0;

    // Destroy existing charts if they exist
    if (statusChart) {
        statusChart.destroy();
    }
    if (locationChart) {
        locationChart.destroy();
    }

    // Status Distribution Chart with fixed size
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Offline', 'Need Attention'],
            datasets: [{
                data: [activeTerminals, offlineTerminals, faultyTerminals],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Get location data from the table
    const locationData = getLocationDataFromTable();
    
    // Location Chart with fixed size and proper data display
    locationChart = new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: locationData.labels.length > 0 ? locationData.labels : ['No Data'],
            datasets: [{
                label: 'Terminals',
                data: locationData.data.length > 0 ? locationData.data : [0],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#dc3545',
                    '#ffc107',
                    '#17a2b8',
                    '#6c757d',
                    '#e83e8c',
                    '#fd7e14',
                    '#6610f2',
                    '#20c997'
                ].slice(0, locationData.data.length),
                borderRadius: 4,
                borderSkipped: false,
                barThickness: 40,
                maxBarThickness: 60
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' terminals';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        autoSkip: false
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Extract location data from the current table - FIXED
function getLocationDataFromTable() {
    const locationCounts = {};
    const rows = document.querySelectorAll('.terminals-table tbody tr');
    
    rows.forEach((row) => {
        // Skip empty state row
        if (row.querySelector('td[colspan]')) {
            return;
        }
        
        // Get the 5th column (location) - index 4
        const locationCell = row.children[4];
        if (locationCell) {
            const locationDiv = locationCell.querySelector('div:first-child');
            if (locationDiv) {
                const location = locationDiv.textContent.trim();
                
                if (location && location !== 'No region' && location !== '') {
                    locationCounts[location] = (locationCounts[location] || 0) + 1;
                }
            }
        }
    });

    // If no data from table, use actual data from PHP
    if (Object.keys(locationCounts).length === 0) {
        @if(isset($terminals) && $terminals->count() > 0)
            @php
                $regionCounts = [];
                foreach($terminals as $terminal) {
                    $region = $terminal->region ?: 'Unknown';
                    if (!isset($regionCounts[$region])) {
                        $regionCounts[$region] = 0;
                    }
                    $regionCounts[$region]++;
                }
                arsort($regionCounts);
                $topRegions = array_slice($regionCounts, 0, 10, true);
            @endphp
            
            @foreach($topRegions as $region => $count)
                locationCounts['{{ $region }}'] = {{ $count }};
            @endforeach
        @else
            // Sample data for demo
            locationCounts['HARARE'] = 12;
            locationCounts['BULAWAYO'] = 8;
            locationCounts['GWERU'] = 6;
            locationCounts['KWEKWE'] = 5;
            locationCounts['MUTARE'] = 4;
        @endif
    }

    // Sort by count and get top 10
    const sortedLocations = Object.entries(locationCounts)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 10);

    return {
        labels: sortedLocations.map(item => item[0]),
        data: sortedLocations.map(item => item[1])
    };
}

// Apply filters
function applyFilters() {
    const form = document.getElementById('filter-form');
    if (form) form.submit();
}

// Clear all filters
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
    
    // Remove URL parameters and reload
    window.location.href = window.location.pathname;
}

// Handle search input
function handleSearch(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        applyFilters();
    }
}

// Setup file upload functionality
function setupFileUpload() {
    const fileInput = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileName.innerHTML = `
                    <div style="color: #28a745; font-weight: 500;">
                        ✅ Selected: ${file.name}
                    </div>
                    <div style="color: #666; font-size: 12px; margin-top: 2px;">
                        Size: ${(file.size / 1024).toFixed(1)} KB
                    </div>
                `;
            } else {
                fileName.textContent = '';
            }
        });
    }
    
    // Form submission handler
    const importForm = document.querySelector('.import-form');
    if (importForm && submitBtn) {
        importForm.addEventListener('submit', function(e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please select a CSV file before submitting.');
                return false;
            }
            
            submitBtn.innerHTML = '<span style="margin-right: 6px;">⏳</span> Processing...';
            submitBtn.disabled = true;
        });
    }
}

function resetForm() {
    const importForm = document.querySelector('.import-form');
    if (importForm) {
        importForm.reset();
    }
    
    const fileName = document.getElementById('fileName');
    if (fileName) {
        fileName.textContent = '';
    }
}
</script>
@endsection