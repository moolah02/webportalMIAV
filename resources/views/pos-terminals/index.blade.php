@extends('layouts.app')
@section('title', 'POS Terminals')

@section('content')

{{-- ── Stats ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">💳</div>
        <div>
            <div class="stat-number">{{ $stats['total_terminals'] ?? 0 }}</div>
            <div class="stat-label">Total Terminals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div>
            <div class="stat-number">{{ $stats['active_terminals'] ?? 0 }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">⚠️</div>
        <div>
            <div class="stat-number">{{ $stats['faulty_terminals'] ?? 0 }}</div>
            <div class="stat-label">Need Attention</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">🔴</div>
        <div>
            <div class="stat-number">{{ $stats['offline_terminals'] ?? 0 }}</div>
            <div class="stat-label">Offline</div>
        </div>
    </div>
</div>

{{-- ── Tab Navigation ──────────────────────────────────────── --}}
<div class="tab-nav mb-5">
    <button class="tab-btn active" onclick="switchTab('overview', this)">
        💳 Terminal Overview
    </button>
    <button class="tab-btn" onclick="switchTab('import', this)">
        📤 Smart Import
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAB 1 — TERMINAL OVERVIEW
════════════════════════════════════════════════════════════ --}}
<div id="overview-tab" class="tab-content">

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('pos-terminals.index') }}" class="filter-bar" id="filter-form">
        <div class="filter-group">
            <label class="ui-label">Search</label>
            <input type="text" name="search" id="search-input"
                   placeholder="Search terminals…"
                   value="{{ request('search') }}"
                   class="ui-input"
                   onkeydown="if(event.key==='Enter'){this.form.submit();}">
        </div>
        <div class="filter-group">
            <label class="ui-label">Client</label>
            <select name="client" class="ui-select" onchange="this.form.submit()">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                        {{ $client->company_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">Status</label>
            <select name="status" class="ui-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"       {{ request('status') == 'active'       ? 'selected' : '' }}>Active</option>
                <option value="offline"      {{ request('status') == 'offline'      ? 'selected' : '' }}>Offline</option>
                <option value="faulty"       {{ request('status') == 'faulty'       ? 'selected' : '' }}>Faulty</option>
                <option value="maintenance"  {{ request('status') == 'maintenance'  ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">Region</label>
            <select name="region" class="ui-select" onchange="this.form.submit()">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="ui-label">City</label>
            <select name="city" class="ui-select" onchange="this.form.submit()">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply</button>
            <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">Reset</a>
            <a href="{{ route('pos-terminals.export', request()->query()) }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </a>
            <a href="{{ route('pos-terminals.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Terminal
            </a>
        </div>
    </form>

    {{-- Terminals table --}}
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-800">Terminal Inventory</span>
                <span class="badge badge-gray">{{ $terminals->total() }} terminals</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Terminal ID</th>
                        <th>Client / Bank</th>
                        <th>Merchant</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Last Service</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terminals as $terminal)
                    <tr>
                        <td>
                            <span class="code-chip">{{ $terminal->terminal_id }}</span>
                        </td>
                        <td>
                            <div class="cell-primary">{{ $terminal->client->company_name }}</div>
                        </td>
                        <td>
                            <div class="cell-primary">{{ $terminal->merchant_name }}</div>
                            @if($terminal->business_type)
                            <div class="cell-sub">{{ $terminal->business_type }}</div>
                            @endif
                        </td>
                        <td>
                            @if($terminal->merchant_contact_person)
                            <div class="cell-primary">{{ $terminal->merchant_contact_person }}</div>
                            @endif
                            @if($terminal->merchant_phone)
                            <div class="cell-sub">{{ $terminal->merchant_phone }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="cell-primary">{{ $terminal->region ?: 'No region' }}</div>
                            @if($terminal->city)
                            <div class="cell-sub">{{ $terminal->city }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = match($terminal->status) {
                                    'active'      => 'badge-green',
                                    'offline'     => 'badge-yellow',
                                    'maintenance' => 'badge-blue',
                                    'faulty'      => 'badge-red',
                                    default       => 'badge-gray',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ ucfirst($terminal->status) }}</span>
                        </td>
                        <td>
                            @if($terminal->last_service_date)
                            <div class="cell-primary">{{ $terminal->last_service_date->format('M d, Y') }}</div>
                            <div class="cell-sub">{{ $terminal->last_service_date->diffForHumans() }}</div>
                            @else
                            <span class="text-gray-400 text-xs italic">Never serviced</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('pos-terminals.show', $terminal) }}" class="action-btn action-view" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('pos-terminals.edit', $terminal) }}" class="action-btn action-edit" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center text-gray-400">
                            <div class="text-4xl mb-3">💳</div>
                            <p class="text-sm">No terminals found. Try adjusting your filters or
                                <a href="{{ route('pos-terminals.create') }}" class="link">add your first terminal</a>.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($terminals->hasPages())
        <div class="ui-card-footer justify-between">
            <span class="text-xs text-gray-500">
                Showing {{ $terminals->firstItem() ?? 0 }}–{{ $terminals->lastItem() ?? 0 }}
                of {{ $terminals->total() }} terminals
            </span>
            {{ $terminals->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAB 2 — SMART IMPORT
════════════════════════════════════════════════════════════ --}}
<div id="import-tab" class="tab-content hidden">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Terminal Data Import</h2>
            <p class="text-sm text-gray-500 mt-0.5">Import terminals from Excel, CSV, or TXT files with smart column detection</p>
        </div>
        <a href="{{ route('pos-terminals.download-template') }}" class="btn-success">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Template
        </a>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <div class="ui-card ui-card-body">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Required Fields</h3>
            <p class="text-xs font-semibold text-red-600 mb-1">Required</p>
            <ul class="text-sm text-gray-700 ml-4 list-disc space-y-0.5 mb-3">
                <li>Terminal ID</li>
                <li>Merchant Name</li>
            </ul>
            <p class="text-xs font-semibold text-green-600 mb-1">Optional</p>
            <p class="text-xs text-gray-500 leading-relaxed">
                Contact Person, Phone, Email, Address, City, Province, Region,
                Business Type, Terminal Model, Serial Number, Installation Date, Status, etc.
            </p>
        </div>
        <div class="ui-card ui-card-body">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Smart Features</h3>
            <ul class="text-sm text-gray-700 ml-4 list-disc space-y-1">
                <li>Auto-detects column headers</li>
                <li>Processes any column order</li>
                <li>Supports CSV, XLSX, XLS, TXT</li>
                <li>Handles files up to 50 MB</li>
                <li>Preview before importing</li>
                <li>Duplicate detection</li>
            </ul>
        </div>
    </div>

    {{-- Import form --}}
    <form id="smart-import-form" action="{{ route('pos-terminals.import') }}" method="POST"
          enctype="multipart/form-data" class="ui-card ui-card-body space-y-5">
        @csrf

        {{-- Client + mapping row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="ui-label" for="client_id">Client / Bank <span class="text-red-500">*</span></label>
                <select name="client_id" id="client_id" required class="ui-select">
                    <option value="">Choose the client for these terminals…</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="ui-hint text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="ui-label" for="mapping_id">Column Mapping <span class="text-gray-400 normal-case font-normal">(optional)</span></label>
                <div class="flex gap-2">
                    <select name="mapping_id" id="mapping_id" class="ui-select flex-1">
                        <option value="">Auto-detect columns</option>
                        @if(isset($mappings) && $mappings->count() > 0)
                            @foreach($mappings as $mapping)
                                <option value="{{ $mapping->id }}">
                                    {{ $mapping->mapping_name }}
                                    @if($mapping->client) ({{ $mapping->client->company_name }}) @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <a href="{{ route('pos-terminals.column-mapping') }}" target="_blank" class="btn-secondary">
                        ⚙️ Manage
                    </a>
                </div>
                <p class="ui-hint">Leave blank for automatic header detection</p>
            </div>
        </div>

        {{-- File drop zone --}}
        <div>
            <label class="ui-label">Upload Data File <span class="text-red-500">*</span></label>
            <div id="drop-zone"
                 class="border-2 border-dashed border-[#1a3a5c]/30 rounded-xl p-10 text-center bg-gray-50 hover:bg-blue-50 hover:border-[#1a3a5c]/60 transition-colors cursor-pointer"
                 onclick="document.getElementById('smart-file-input').click()">
                <div class="text-5xl mb-3">📁</div>
                <p class="text-sm font-medium text-gray-700 mb-1">Drop your file here or click to browse</p>
                <p class="text-xs text-gray-500 mb-5">Supports Excel (.xlsx, .xls), CSV, and TXT files up to 50 MB</p>
                <div class="flex gap-3 justify-center" onclick="event.stopPropagation()">
                    <input type="file" name="file" id="smart-file-input"
                           accept=".csv,.xlsx,.xls,.txt" required class="hidden">
                    <button type="button" class="btn-primary btn-sm"
                            onclick="document.getElementById('smart-file-input').click()">
                        📂 Choose File
                    </button>
                    <button type="button" id="preview-btn" disabled class="btn-success btn-sm opacity-50 cursor-not-allowed">
                        👁️ Preview & Analyze
                    </button>
                </div>
                @error('file')
                    <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
                @enderror
                <div id="file-info" class="hidden mt-4">
                    <div class="flash-success">
                        <span class="text-lg">✅</span>
                        <div>
                            <div id="file-name" class="font-medium text-sm"></div>
                            <div id="file-details" class="text-xs mt-0.5"></div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">
                    <strong>Pro Tip:</strong> No need to worry about column order — our smart system detects and maps columns automatically!
                </p>
            </div>
        </div>

        {{-- Import options --}}
        <div>
            <p class="ui-label">Import Options</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#1a3a5c]/40 hover:bg-blue-50 transition-colors">
                    <input type="checkbox" name="options[]" value="skip_duplicates" checked
                           class="mt-0.5 w-4 h-4 accent-[#1a3a5c]">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Skip Duplicate Terminal IDs</p>
                        <p class="text-xs text-gray-500 mt-0.5">Existing terminals with the same ID will be ignored during import</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#1a3a5c]/40 hover:bg-blue-50 transition-colors">
                    <input type="checkbox" name="options[]" value="update_existing"
                           class="mt-0.5 w-4 h-4 accent-[#1a3a5c]">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Update Existing Records</p>
                        <p class="text-xs text-gray-500 mt-0.5">Override existing terminal data with new imported values</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-3 pt-2">
            <button type="submit" id="import-submit-btn" disabled
                    class="btn-primary opacity-50 cursor-not-allowed">
                🚀 Start Smart Import
            </button>
            <button type="button" onclick="resetImportForm()" class="btn-secondary">
                🔄 Reset Form
            </button>
        </div>
    </form>
</div>

{{-- ── Preview Modal ───────────────────────────────────────── --}}
<div id="preview-modal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="ui-card w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="ui-card-header">
                <h3 class="text-base font-semibold text-gray-900">👁️ Smart Import Preview & Analysis</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-700 text-2xl leading-none bg-transparent border-none cursor-pointer">&times;</button>
            </div>
            <div id="preview-content" class="ui-card-body">
                <div class="text-center py-16 text-gray-400">
                    <div class="text-5xl mb-3">🔄</div>
                    <p class="text-sm">Analyzing your file…</p>
                </div>
            </div>
            <div class="ui-card-footer">
                <button onclick="closePreviewModal()" class="btn-secondary">Close Preview</button>
                <button id="proceed-import-btn" onclick="proceedWithImport()" disabled
                        class="btn-success opacity-50 cursor-not-allowed">
                    ✅ Looks Good — Proceed with Import
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Processing Modal ────────────────────────────────────── --}}
<div id="processing-modal" class="hidden fixed inset-0 bg-black/70 z-[60]">
    <div class="flex items-center justify-center h-full">
        <div class="ui-card p-10 text-center max-w-sm w-full">
            <div class="text-5xl mb-4">⚡</div>
            <h4 class="text-base font-semibold text-gray-900 mb-2">Processing Your Smart Import</h4>
            <p class="text-sm text-gray-500 mb-5">Large files are processed in chunks automatically. This may take a few minutes…</p>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#1a3a5c] to-green-500 animate-pulse rounded-full"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Tab switching ────────────────────────────────────────────
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const tab = document.getElementById(tabName + '-tab');
    if (tab) tab.classList.remove('hidden');
    if (btn) btn.classList.add('active');
}

// ── Filter helpers ───────────────────────────────────────────
function applyFilters() {
    document.getElementById('filter-form')?.submit();
}

// ── File upload ──────────────────────────────────────────────
window.importData = { currentFile: null, previewData: null, isProcessing: false };

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('smart-file-input');
    const dropZone  = document.getElementById('drop-zone');
    const previewBtn = document.getElementById('preview-btn');
    const submitBtn  = document.getElementById('import-submit-btn');

    if (!fileInput) return;

    fileInput.addEventListener('change', e => { if (e.target.files[0]) handleFileSelection(e.target.files[0]); });

    if (dropZone) {
        dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('bg-blue-50'); });
        dropZone.addEventListener('dragleave', e => { e.preventDefault(); dropZone.classList.remove('bg-blue-50'); });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('bg-blue-50');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                handleFileSelection(file);
            }
        });
    }

    function handleFileSelection(file) {
        const allowed = ['.csv', '.xlsx', '.xls', '.txt'];
        const ext = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) { alert('Please select a valid file type: CSV, XLSX, XLS, or TXT'); return; }
        if (file.size > 50 * 1024 * 1024) { alert('File size exceeds 50 MB limit.'); return; }

        window.importData.currentFile = file;

        const nameEl    = document.getElementById('file-name');
        const detailEl  = document.getElementById('file-details');
        const infoEl    = document.getElementById('file-info');
        if (nameEl)   nameEl.textContent   = file.name;
        if (detailEl) detailEl.textContent = `${formatBytes(file.size)} · ${ext.toUpperCase()} · Modified ${new Date(file.lastModified).toLocaleDateString()}`;
        if (infoEl)   infoEl.classList.remove('hidden');

        if (previewBtn) { previewBtn.disabled = false; previewBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
        if (submitBtn)  { submitBtn.disabled  = false; submitBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
    }

    function formatBytes(b) {
        if (b === 0) return '0 B';
        const k = 1024, s = ['B','KB','MB','GB'], i = Math.floor(Math.log(b)/Math.log(k));
        return (b/Math.pow(k,i)).toFixed(2)+' '+s[i];
    }

    if (previewBtn) {
        previewBtn.addEventListener('click', function () {
            if (window.importData.isProcessing) return;
            const clientId = document.getElementById('client_id')?.value;
            if (!fileInput.files[0]) { alert('Please select a file first'); return; }
            if (!clientId) { alert('Please select a client first'); return; }
            startPreview(fileInput.files[0], clientId, document.getElementById('mapping_id')?.value);
        });
    }
});

function startPreview(file, clientId, mappingId) {
    window.importData.isProcessing = true;
    showPreviewModal();

    const formData = new FormData();
    formData.append('file', file);
    formData.append('client_id', clientId);
    if (mappingId) formData.append('mapping_id', mappingId);
    formData.append('preview_rows', '5');

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const ctrl = new AbortController();
    const tid  = setTimeout(() => { ctrl.abort(); displayPreviewError('Request timed out. Try a smaller file or split into chunks.'); }, 120000);

    fetch('/pos-terminals/preview-import', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        signal: ctrl.signal
    })
    .then(r => { clearTimeout(tid); if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(data => { data.success ? displayPreviewData(data) : displayPreviewError(data.message || 'Preview failed'); })
    .catch(err  => { clearTimeout(tid); displayPreviewError(err.name === 'AbortError' ? 'Timed out. Try a smaller file.' : err.message); })
    .finally(() => { window.importData.isProcessing = false; });
}

function showPreviewModal() {
    document.getElementById('preview-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreviewModal() {
    document.getElementById('preview-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function displayPreviewData(data) {
    const mapped   = data.column_mapping_info?.mapped_fields   || [];
    const missing  = data.column_mapping_info?.missing_required || [];
    const hasErrors = data.preview_data.some(r => r.validation_status !== 'valid');
    const canImport = !hasErrors && missing.length === 0;

    const content = document.getElementById('preview-content');
    content.innerHTML = `
        <div class="grid grid-cols-2 gap-4 mb-5">
            <div class="flash-success">
                <div>
                    <p class="font-semibold text-sm">File Analysis</p>
                    <p class="text-xs mt-1">Mapping: ${data.mapping_name} · ${data.headers.length} columns · ${data.preview_data.length} preview rows</p>
                </div>
            </div>
            <div class="${missing.length ? 'flash-error' : 'flash-success'}">
                <div>
                    <p class="font-semibold text-sm">Column Mapping</p>
                    <p class="text-xs mt-1">${mapped.length} mapped · ${missing.length} missing required · ${missing.length === 0 ? 'All required fields found ✅' : 'Missing: ' + missing.join(', ')}</p>
                </div>
            </div>
        </div>
        <div class="ui-card overflow-hidden mb-5">
            <div class="ui-card-header"><span class="text-sm font-semibold">Detected Columns</span></div>
            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead><tr><th>#</th><th>Column Header</th><th>Status</th></tr></thead>
                    <tbody>
                        ${data.headers.map((h, i) => {
                            const cls = mapped.includes(h.toLowerCase().replace(/\s+/g,'_')) ? 'badge-green' : 'badge-yellow';
                            return `<tr><td>${i+1}</td><td><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">${h}</code></td><td><span class="status-badge ${cls}">${cls === 'badge-green' ? 'Mapped' : 'Unmapped'}</span></td></tr>`;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ui-card overflow-hidden mb-5">
            <div class="ui-card-header"><span class="text-sm font-semibold">Data Preview</span></div>
            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead><tr><th>Row</th><th>Terminal ID</th><th>Merchant</th><th>Status</th><th>Validation</th></tr></thead>
                    <tbody>
                        ${data.preview_data.map(r => `
                            <tr>
                                <td>${r.row_number}</td>
                                <td><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">${r.mapped_data.terminal_id||'N/A'}</code></td>
                                <td>${r.mapped_data.merchant_name||'N/A'}</td>
                                <td>${r.mapped_data.status||'active'}</td>
                                <td><span class="status-badge ${r.validation_status==='valid'?'badge-green':'badge-red'}">${r.validation_status==='valid'?'Valid':'Error'}</span>${r.validation_status!=='valid'?'<br><span class="text-xs text-red-500">'+r.validation_message+'</span>':''}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="${canImport ? 'flash-success' : 'flash-error'}">
            <span class="text-xl">${canImport ? '✅' : '❌'}</span>
            <div>
                <p class="font-semibold text-sm">${canImport ? 'Ready for Import' : 'Issues Detected'}</p>
                <p class="text-xs mt-0.5">${canImport ? 'All required fields present and validation passed.' : 'Resolve issues before importing.'}</p>
            </div>
        </div>
    `;

    const proceedBtn = document.getElementById('proceed-import-btn');
    if (proceedBtn) {
        proceedBtn.disabled = !canImport;
        proceedBtn.classList.toggle('opacity-50', !canImport);
        proceedBtn.classList.toggle('cursor-not-allowed', !canImport);
    }
}

function displayPreviewError(msg) {
    document.getElementById('preview-content').innerHTML = `
        <div class="text-center py-16">
            <div class="text-5xl mb-3">❌</div>
            <p class="text-sm font-semibold text-gray-800 mb-2">Preview Failed</p>
            <p class="text-sm text-gray-500 mb-5">${msg}</p>
            <div class="flash-warning text-left">
                <span>⚠️</span>
                <div class="text-xs">
                    <strong>Troubleshooting:</strong><br>
                    • Ensure the file is valid CSV, XLSX, XLS, or TXT<br>
                    • Check it contains Terminal ID and Merchant Name columns<br>
                    • Verify the file is not corrupted or password-protected
                </div>
            </div>
        </div>
    `;
}

function proceedWithImport() {
    closePreviewModal();
    document.getElementById('processing-modal').classList.remove('hidden');
    document.getElementById('smart-import-form')?.submit();
}

function resetImportForm() {
    document.getElementById('smart-import-form')?.reset();
    document.getElementById('file-info')?.classList.add('hidden');
    const pBtn = document.getElementById('preview-btn');
    const sBtn = document.getElementById('import-submit-btn');
    if (pBtn) { pBtn.disabled = true; pBtn.classList.add('opacity-50', 'cursor-not-allowed'); }
    if (sBtn) { sBtn.disabled = true; sBtn.classList.add('opacity-50', 'cursor-not-allowed'); }
    window.importData = { currentFile: null, previewData: null, isProcessing: false };
}

// Close modal on backdrop click or Escape
document.addEventListener('click', e => {
    if (e.target.id === 'preview-modal') closePreviewModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePreviewModal();
});
</script>
@endpush
