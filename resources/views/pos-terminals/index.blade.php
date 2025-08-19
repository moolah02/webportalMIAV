@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    
    <!-- Navigation Tabs with 4 Tabs -->
    <div class="tab-navigation" style="display: flex; border-bottom: 2px solid #dee2e6; margin-bottom: 30px; background: white; border-radius: 8px 8px 0 0; overflow: hidden;">
        <button class="tab-btn active" onclick="switchTab('overview')" style="padding: 16px 20px; background: white; border: none; border-bottom: 3px solid #007bff; cursor: pointer; font-weight: 500; color: #007bff; transition: all 0.2s ease; flex: 1; text-align: center;">
            📋 Terminal Overview
        </button>
        <button class="tab-btn" onclick="switchTab('analytics')" style="padding: 16px 20px; background: #f8f9fa; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; transition: all 0.2s ease; flex: 1; text-align: center;">
            📊 Statistics & Analytics
        </button>
        <button class="tab-btn" onclick="switchTab('import')" style="padding: 16px 20px; background: #f8f9fa; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; transition: all 0.2s ease; flex: 1; text-align: center;">
            📤 Import Bank Data
        </button>
        <button class="tab-btn" onclick="switchTab('field')" style="padding: 16px 20px; background: #f8f9fa; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; transition: all 0.2s ease; flex: 1; text-align: center;">
            🔧 Field Updates
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

    <!-- Statistics & Analytics Tab (NEW) -->
    <div id="analytics-tab" class="tab-content" style="display: none;">
        <div class="main-card" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            
            <!-- Analytics Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h2 style="margin: 0; color: #333; font-size: 24px;">📊 Terminal Analytics Dashboard</h2>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 16px;">Comprehensive insights and statistics for your terminal network</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <select id="chart-view-selector" onchange="switchChartView()" style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        <option value="overview">Complete Overview</option>
                        <option value="service">Service Focus</option>
                        <option value="distribution">Distribution Analysis</option>
                        <option value="performance">Performance Metrics</option>
                    </select>
                    <button onclick="exportAnalytics()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        📄 Export Data
                    </button>
                </div>
            </div>

            <!-- Detailed Statistics Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div class="stat-card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border: 1px solid #dee2e6; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 8px;">{{ $stats['total_terminals'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #666; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Total Terminals</div>
                    <div style="font-size: 12px; color: #999;">Active network size</div>
                </div>
                
                <div class="stat-card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-radius: 12px; border: 1px solid #b8dacc; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 32px; font-weight: 700; color: #155724; margin-bottom: 8px;">{{ $stats['active_terminals'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #155724; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Active</div>
                    <div style="font-size: 12px; color: #155724;">{{ $stats['uptime_percentage'] ?? 0 }}% uptime</div>
                </div>
                
                <div class="stat-card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%); border-radius: 12px; border: 1px solid #f1b0b7; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 32px; font-weight: 700; color: #721c24; margin-bottom: 8px;">{{ $stats['faulty_terminals'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #721c24; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Need Attention</div>
                    <div style="font-size: 12px; color: #721c24;">Require service</div>
                </div>
                
                <div class="stat-card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 12px; border: 1px solid #ffeaa7; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 32px; font-weight: 700; color: #856404; margin-bottom: 8px;">{{ $stats['offline_terminals'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #856404; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Offline</div>
                    <div style="font-size: 12px; color: #856404;">Not responding</div>
                </div>
                
                <div class="stat-card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #e7f3ff 0%, #cce7ff 100%); border-radius: 12px; border: 1px solid #b3d9ff; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 32px; font-weight: 700; color: #0066cc; margin-bottom: 8px;">{{ $stats['recently_serviced'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #0066cc; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Recently Serviced</div>
                    <div style="font-size: 12px; color: #0066cc;">Last 30 days</div>
                </div>
                
                <div class="stat-card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #fff0e6 0%, #ffe6cc 100%); border-radius: 12px; border: 1px solid #ffcc99; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 32px; font-weight: 700; color: #cc6600; margin-bottom: 8px;">{{ $stats['service_due'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #cc6600; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Service Due</div>
                    <div style="font-size: 12px; color: #cc6600;">Maintenance needed</div>
                </div>
            </div>

            <!-- Charts Section with Multiple Views -->
            <div id="charts-container">
                
                <!-- Complete Overview (Default) -->
                <div id="overview-charts" class="chart-view active">
                    <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; text-align: center;">📈 Complete Analytics Overview</h3>
                    
                    <!-- Row 1: Service & Location -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                                🔧 Service Timeline Analysis
                            </h4>
                            <div style="position: relative; height: 300px;">
                                <canvas id="serviceDueChart"></canvas>
                            </div>
                            <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center; line-height: 1.4;">
                                Maintenance schedule tracking and service compliance overview
                            </div>
                        </div>

                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                                🗺️ Regional Distribution
                            </h4>
                            <div style="position: relative; height: 300px;">
                                <canvas id="locationChart"></canvas>
                            </div>
                            <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center; line-height: 1.4;">
                                Terminal distribution across different regions and cities
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Client & Models -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                                🏦 Client Portfolio Distribution
                            </h4>
                            <div style="position: relative; height: 300px;">
                                <canvas id="clientChart"></canvas>
                            </div>
                            <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center; line-height: 1.4;">
                                Terminal allocation across different banks and financial institutions
                            </div>
                        </div>

                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                                📱 Device Model Analysis
                            </h4>
                            <div style="position: relative; height: 300px;">
                                <canvas id="modelsChart"></canvas>
                            </div>
                            <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center; line-height: 1.4;">
                                Distribution of terminal models and hardware types in network
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Performance & Trends -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                                ⚡ Performance Overview
                            </h4>
                            <div style="position: relative; height: 300px;">
                                <canvas id="performanceChart"></canvas>
                            </div>
                            <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center; line-height: 1.4;">
                                Multi-dimensional performance metrics and KPI tracking
                            </div>
                        </div>

                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                                📈 Monthly Trends
                            </h4>
                            <div style="position: relative; height: 300px;">
                                <canvas id="trendsChart"></canvas>
                            </div>
                            <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center; line-height: 1.4;">
                                Installation and service completion trends over time
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Focus View -->
                <div id="service-charts" class="chart-view" style="display: none;">
                    <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; text-align: center;">🔧 Service & Maintenance Focus</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Service Timeline (Detailed)</h4>
                            <div style="position: relative; height: 350px;">
                                <canvas id="serviceDueChart2"></canvas>
                            </div>
                        </div>
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Service Trends</h4>
                            <div style="position: relative; height: 350px;">
                                <canvas id="trendsChart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribution Focus View -->
                <div id="distribution-charts" class="chart-view" style="display: none;">
                    <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; text-align: center;">📊 Distribution Analysis</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Client Distribution (Detailed)</h4>
                            <div style="position: relative; height: 350px;">
                                <canvas id="clientChart2"></canvas>
                            </div>
                        </div>
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Geographic Distribution</h4>
                            <div style="position: relative; height: 350px;">
                                <canvas id="locationChart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Focus View -->
                <div id="performance-charts" class="chart-view" style="display: none;">
                    <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; text-align: center;">⚡ Performance Metrics</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Performance Overview (Detailed)</h4>
                            <div style="position: relative; height: 350px;">
                                <canvas id="performanceChart2"></canvas>
                            </div>
                        </div>
                        <div class="chart-container" style="background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Hardware Performance</h4>
                            <div style="position: relative; height: 350px;">
                                <canvas id="modelsChart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Summary Footer -->
            <div style="margin-top: 40px; padding: 25px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border: 1px solid #dee2e6;">
                <h4 style="margin: 0 0 15px 0; color: #333; font-size: 18px;">📋 Analytics Summary</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 600; color: #007bff; margin-bottom: 5px;">
                            {{ number_format(($stats['uptime_percentage'] ?? 0), 1) }}%
                        </div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Network Uptime</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 600; color: #28a745; margin-bottom: 5px;">
                            {{ count($stats['model_distribution'] ?? []) }}
                        </div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Device Types</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 600; color: #17a2b8; margin-bottom: 5px;">
                            {{ count($stats['client_distribution'] ?? []) }}
                        </div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Active Clients</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 600; color: #ffc107; margin-bottom: 5px;">
                            {{ $stats['recent_installations'] ?? 0 }}
                        </div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Recent Installs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Tab (Existing) -->
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

    <!-- Field Updates Tab (Existing) -->
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

<!-- Enhanced CSS -->
<style>
/* Chart view switching */
.chart-view {
    display: none;
}

.chart-view.active {
    display: block;
}

/* Enhanced chart containers with hover effects */
.chart-container {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.chart-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.chart-container canvas {
    max-width: 100% !important;
    height: auto !important;
}

/* Enhanced stat cards with animations */
.stat-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

/* Loading state */
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
    width: 30px;
    height: 30px;
    margin: -15px 0 0 -15px;
    border: 3px solid #007bff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
    z-index: 1000;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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
    
    [style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .chart-container {
        margin-bottom: 20px;
    }
}

@media (max-width: 480px) {
    [style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    [style*="grid-template-columns: repeat(2, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<!-- Chart Data Script (Injected by Controller) -->
<script>
window.chartData = {
    stats: @json($stats ?? []),
    serviceDue: {
        recentlyServiced: {{ $stats['recently_serviced'] ?? 0 }},
        serviceDueSoon: {{ max(0, ($stats['service_due'] ?? 0) - ($stats['overdue_service'] ?? 0)) }},
        overdueService: {{ $stats['overdue_service'] ?? 0 }},
        neverServiced: {{ $stats['never_serviced'] ?? 0 }}
    },
    clientDistribution: @json($stats['client_distribution'] ?? []),
    modelDistribution: @json($stats['model_distribution'] ?? [])
};
</script>

<!-- Load Chart.js first -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- Initialize everything -->
<script>
// Tab switching function with analytics support
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

    // Initialize charts if analytics tab is opened
    if (tabName === 'analytics') {
        setTimeout(initializeAnalyticsCharts, 100);
    }
}

// Chart view switching for analytics tab
function switchChartView() {
    const selector = document.getElementById('chart-view-selector');
    const selectedView = selector.value;
    
    // Hide all chart views
    document.querySelectorAll('.chart-view').forEach(view => {
        view.style.display = 'none';
        view.classList.remove('active');
    });
    
    // Show selected view
    const targetView = document.getElementById(selectedView + '-charts');
    if (targetView) {
        targetView.style.display = 'block';
        targetView.classList.add('active');
        
        // Re-render charts for the new view
        setTimeout(() => {
            if (typeof Chart !== 'undefined') {
                initializeAnalyticsCharts();
            }
        }, 100);
    }
}

// Initialize analytics charts
function initializeAnalyticsCharts() {
    if (typeof Chart === 'undefined') {
        console.log('Chart.js not loaded yet');
        return;
    }

    // Destroy existing charts
    Chart.helpers.each(Chart.instances, (instance) => {
        instance.destroy();
    });

    const stats = window.chartData?.stats || {};
    const serviceDue = window.chartData?.serviceDue || {};
    const clientDistribution = window.chartData?.clientDistribution || {};
    const modelDistribution = window.chartData?.modelDistribution || {};

    // Service Due Chart
    const serviceDueCtx = document.getElementById('serviceDueChart')?.getContext('2d');
    if (serviceDueCtx) {
        new Chart(serviceDueCtx, {
            type: 'bar',
            data: {
                labels: ['Recently Serviced', 'Due Soon', 'Overdue', 'Never Serviced'],
                datasets: [{
                    label: 'Terminals',
                    data: [
                        serviceDue.recentlyServiced || 0,
                        serviceDue.serviceDueSoon || 0,
                        serviceDue.overdueService || 0,
                        serviceDue.neverServiced || 0
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const labels = [
                                    'Recently Serviced (Last 30 days)',
                                    'Service Due Soon (60-90 days)',
                                    'Overdue Service (90+ days)', 
                                    'Never Serviced'
                                ];
                                return `${labels[context.dataIndex]}: ${context.parsed.y} terminals`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { display: true, color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Location Chart (simplified example)
    const locationCtx = document.getElementById('locationChart')?.getContext('2d');
    if (locationCtx) {
        new Chart(locationCtx, {
            type: 'bar',
            data: {
                labels: ['HARARE', 'BULAWAYO', 'GWERU', 'KWEKWE', 'MUTARE'],
                datasets: [{
                    label: 'Terminals',
                    data: [25, 15, 12, 8, 6],
                    backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8'],
                    borderRadius: 4,
                    borderSkipped: false,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 5 } }
                }
            }
        });
    }

    // Add other charts as needed...
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

function exportAnalytics() {
    if (!window.chartData) {
        alert('No analytics data available for export');
        return;
    }
    
    const data = {
        timestamp: new Date().toISOString(),
        statistics: window.chartData.stats,
        distributions: {
            clients: window.chartData.clientDistribution,
            models: window.chartData.modelDistribution
        }
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `terminal-analytics-${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // File upload handler
    const fileInput = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');
    
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
});
</script>
@endsection