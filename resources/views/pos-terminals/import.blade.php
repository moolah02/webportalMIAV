@extends('layouts.app')
@section('title', 'Import Terminals')

@push('styles')
<style>
/* ── POS terminals · import ────────────────────────────── */
.mv-page > .mv-flash .mv-flash-text { white-space: pre-line; }

.pt-imp .ui-card-header { padding: 12px 18px; gap: 12px; }
.pt-imp .ui-card-header h2, .pt-imp .ui-card-header h3 { font-size: 14px; font-weight: 600; margin: 0; }
.pt-imp .ui-card-body { padding: 18px; }
.pt-imp .pt-stack > * + * { margin-top: 18px; }
.pt-imp .pt-btn { height: 34px; padding: 0 12px; font-size: 13px; }
.pt-imp .pt-btn-sm { height: 30px; padding: 0 10px; font-size: 12.5px; }
.pt-imp .ui-label { margin-bottom: 6px; }
.pt-imp .required::after { content: " *"; color: var(--mv-crit); }
.pt-imp .ui-hint { display: block; font-size: 12px; margin-top: 5px; }
.pt-imp .ui-input { height: 38px; padding: 0 12px; font-size: 13.5px; }
.pt-imp .ui-select { height: 38px; padding: 0 32px 0 12px; font-size: 13.5px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236A7686' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; }
.pt-imp .is-invalid { border-color: var(--mv-crit) !important; }
.pt-imp .invalid-feedback { display: block; margin-top: 5px; font-size: 12px; color: var(--mv-crit); }
.pt-imp button:disabled { opacity: .5; cursor: not-allowed; }

.pt-imp .pt-errors { display: flex; align-items: flex-start; gap: 10px; padding: 11px 14px; border: 1px solid #F2CACA; border-radius: 8px; background: var(--mv-crit-soft); color: var(--mv-crit); font-size: 13px; }
.pt-imp .pt-errors ul { margin: 0; padding-left: 16px; }
.pt-imp .pt-errors .mv-i { margin-top: 1px; }

/* File picker (Bootstrap custom-file kept for the script) */
.pt-imp .pt-file-row { display: flex; gap: 8px; align-items: stretch; }
.pt-imp .custom-file { position: relative; flex: 1; min-width: 0; height: 38px; margin: 0; }
.pt-imp .custom-file-input { position: absolute; inset: 0; width: 100%; height: 100%; margin: 0; padding: 0; opacity: 0; z-index: 2; cursor: pointer; }
.pt-imp .custom-file-label { position: absolute; inset: 0; z-index: 1; height: 38px; margin: 0; display: flex; align-items: center; padding: 0 96px 0 12px; border: 1px solid var(--mv-line-strong); border-radius: 8px; background: var(--mv-surface); font-size: 13.5px; font-weight: 400; color: var(--mv-ink-2); overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.pt-imp .custom-file-label::after { content: "Browse"; position: absolute; top: 0; right: 0; bottom: 0; height: auto; display: flex; align-items: center; padding: 0 14px; border-left: 1px solid var(--mv-line); border-radius: 0 8px 8px 0; background: var(--mv-surface-2); color: var(--mv-ink); font-size: 13px; font-weight: 500; line-height: 1; }
.pt-imp .custom-file-input:focus ~ .custom-file-label { border-color: var(--mv-accent); box-shadow: 0 0 0 3px rgba(43, 100, 168, .15); }
.pt-imp .custom-file-input.is-invalid ~ .custom-file-label { border-color: var(--mv-crit); }
.pt-imp .pt-file-row .btn-secondary { height: 38px; }

/* Options */
.pt-imp .pt-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
.pt-imp .pt-option { display: flex; align-items: center; gap: 10px; padding: 10px 12px; margin: 0; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface); }
.pt-imp .pt-option:hover { border-color: var(--mv-line-strong); }
.pt-imp .pt-option .form-check-input { position: static; width: 16px; height: 16px; margin: 0; accent-color: var(--mv-accent); flex-shrink: 0; }
.pt-imp .pt-option .form-check-label { flex: 1; font-size: 13px; color: var(--mv-ink); cursor: pointer; margin: 0; }
@media (max-width: 700px) { .pt-imp .pt-options { grid-template-columns: 1fr; } }
.pt-imp .pt-footer-end { justify-content: flex-end; gap: 8px; padding: 12px 18px; }

/* Side cards */
.pt-imp .pt-side { display: flex; flex-direction: column; gap: 16px; }
.pt-imp .pt-side-label { margin: 0 0 6px; font-size: 12px; font-weight: 500; color: var(--mv-muted); }
.pt-imp .pt-list { list-style: none; margin: 0 0 14px; padding: 0; }
.pt-imp .pt-list:last-child { margin-bottom: 0; }
.pt-imp .pt-list li { display: flex; align-items: center; gap: 8px; padding: 2px 0; font-size: 13px; color: var(--mv-ink-2); }
.pt-imp .pt-list .mv-i { width: 14px; height: 14px; color: var(--mv-muted); }
.pt-imp .pt-list-dots li::before { content: ""; width: 4px; height: 4px; border-radius: 50%; background: var(--mv-line-strong); flex-shrink: 0; }
.pt-imp .pt-req { color: var(--mv-crit); font-weight: 600; }
.pt-imp .pt-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 16px; }
.pt-imp .pt-formats { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
.pt-imp .pt-code { display: inline-block; padding: 1px 6px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); font-family: var(--mv-mono); font-size: 12px; color: var(--mv-ink); }
.pt-imp .pt-note { display: flex; align-items: flex-start; gap: 8px; margin-top: 12px; padding: 9px 12px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); font-size: 12.5px; color: var(--mv-ink-2); }
.pt-imp .pt-note .mv-i { width: 15px; height: 15px; margin-top: 1px; color: var(--mv-muted); }
.pt-imp .pt-kv-list li strong { font-weight: 500; color: var(--mv-ink); }

/* Modals (Bootstrap) */
.pt-imp .modal-content { border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 16px 40px rgba(22, 32, 44, .14); overflow: hidden; }
.pt-imp .modal-header { align-items: center; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
.pt-imp .modal-title { font-size: 14.5px; font-weight: 600; color: var(--mv-ink); margin: 0; }
.pt-imp .modal-body { padding: 18px; }
.pt-imp .modal-footer { padding: 12px 18px; border-top: 1px solid var(--mv-line); gap: 8px; }
.pt-imp .modal-footer > * { margin: 0; }
.pt-imp #loadingModal .modal-dialog { max-width: 420px; }
.modal-backdrop.show { opacity: 1; background: rgba(22, 32, 44, .45); }
.pt-imp .pt-icon-btn { width: 30px; height: 30px; display: grid; place-items: center; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); cursor: pointer; }
.pt-imp .pt-icon-btn:hover { background: var(--mv-surface-2); color: var(--mv-ink); }

/* Progress / states (rendered by the script) */
.pt-imp .pt-state { padding: 16px 8px; text-align: center; }
.pt-imp .pt-state-icon { display: block; width: 22px; height: 22px; margin: 0 auto 12px; color: var(--mv-accent); }
.pt-imp .pt-state-title { margin: 0 0 4px; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
.pt-imp .pt-state-text { margin: 0 0 16px; font-size: 13px; color: var(--mv-muted); }
.pt-imp .pt-state-meta { display: block; margin: 8px 0 0; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.pt-imp .pt-progress-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; }
.pt-imp .pt-progress-row.is-end { justify-content: flex-end; }
.pt-imp .pt-progress { height: 6px; border-radius: 6px; background: var(--mv-line); overflow: hidden; box-shadow: none; }
.pt-imp .pt-progress .progress-bar { background-color: var(--mv-accent); background-image: none; transition: width .2s ease; }
.pt-imp .pt-progress .progress-bar.bg-success { background-color: var(--mv-good) !important; }
.pt-imp .pt-bar { position: relative; height: 4px; border-radius: 4px; background: var(--mv-line); overflow: hidden; }
.pt-imp .pt-bar span { position: absolute; top: 0; bottom: 0; left: -35%; width: 35%; border-radius: 4px; background: var(--mv-accent); animation: pt-slide 1.4s ease-in-out infinite; }
.pt-imp .pt-state .btn-danger { margin-top: 14px; }
.pt-imp .pt-spin { animation: pt-spin 1s linear infinite; }
@keyframes pt-spin { to { transform: rotate(360deg); } }
@keyframes pt-slide { to { left: 100%; } }
@media (prefers-reduced-motion: reduce) { .pt-imp .pt-spin, .pt-imp .pt-bar span { animation: none; } }
.pt-imp .pt-upload-wrap { padding: 14px 18px; background: var(--mv-surface); border-top: 1px solid var(--mv-line); }

.pt-imp .pt-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 8px; border: 1px solid var(--mv-line); background: var(--mv-surface-2); color: var(--mv-ink-2); font-size: 13px; }
.pt-imp .pt-alert.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.pt-imp .pt-alert.is-warn { background: var(--mv-warn-soft); border-color: #F0DDB6; color: var(--mv-warn); }
.pt-imp .pt-alert.is-info { background: var(--mv-accent-soft); border-color: #C9D9EE; color: var(--mv-accent-ink); }
.pt-imp .pt-alert .mv-i { margin-top: 1px; }
.pt-imp .pt-alert-title { margin: 0; font-weight: 600; }
.pt-imp .pt-alert-text { margin: 2px 0 0; }

.pt-imp .pt-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
.pt-imp .pt-summary-box { padding: 12px 14px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); }
.pt-imp .pt-summary-title { margin: 0 0 4px; font-size: 13px; font-weight: 600; color: var(--mv-ink); }
.pt-imp .pt-summary-list { list-style: none; margin: 0; padding: 0; font-size: 12.5px; color: var(--mv-ink-2); font-variant-numeric: tabular-nums; }
.pt-imp .pt-summary-list li { padding: 1px 0; }
.pt-imp .pt-summary-list strong { font-weight: 500; color: var(--mv-ink); }
.pt-imp .pt-text-crit, .pt-imp .pt-text-crit strong { color: var(--mv-crit); }
.pt-imp .pt-text-good, .pt-imp .pt-text-good strong { color: var(--mv-good); }
.pt-imp .pt-preview-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 2fr); gap: 12px; margin-bottom: 16px; }
.pt-imp .pt-subcard { border: 1px solid var(--mv-line); border-radius: 10px; overflow: hidden; }
.pt-imp .pt-subcard-head { padding: 10px 14px; font-size: 13px; font-weight: 600; color: var(--mv-ink); border-bottom: 1px solid var(--mv-line); }
.pt-imp .pt-scroll { max-height: 220px; overflow: auto; }
.pt-imp .pt-subcard .ui-table thead th { padding: 8px 12px; position: sticky; top: 0; }
.pt-imp .pt-subcard .ui-table tbody td { padding: 8px 12px; font-size: 13px; }
.pt-imp .status-badge { gap: 6px; padding: 2px 8px; font-size: 12px; line-height: 18px; }
.pt-imp .status-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.pt-imp .pt-row-error { display: block; margin-top: 3px; font-size: 12px; color: var(--mv-crit); }
@media (max-width: 760px) { .pt-imp .pt-summary, .pt-imp .pt-preview-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="pt-imp">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

        {{-- Import form --}}
        <div class="ui-card lg:col-span-2">
            <div class="ui-card-header">
                <h2>Smart POS Terminal Import</h2>
                <a href="{{ route('pos-terminals.download-template') }}" class="btn-secondary pt-btn">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg>
                    Download Template
                </a>
            </div>

            <form id="importForm" action="{{ route('pos-terminals.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="ui-card-body pt-stack">
                    {{-- Validation messages (success / error flashes are shown by the layout) --}}
                    @if ($errors->any())
                      <div class="pt-errors" role="alert">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-alert-circle"/></svg>
                        <ul>
                          @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="client_id" class="ui-label required">Client</label>
                            <select name="client_id" id="client_id" class="ui-select @error('client_id') is-invalid @enderror" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="mapping_id" class="ui-label">Column Mapping (Optional)</label>
                            <select name="mapping_id" id="mapping_id" class="ui-select">
                                <option value="">Use Smart Auto-Detection</option>
                                @foreach($mappings as $mapping)
                                    <option value="{{ $mapping->id }}" {{ old('mapping_id') == $mapping->id ? 'selected' : '' }}>
                                        {{ $mapping->mapping_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="ui-hint">Leave blank for automatic header detection</small>
                        </div>
                    </div>

                    <div>
                        <label for="file" class="ui-label required">Import File</label>
                        <div class="pt-file-row">
                            <div class="custom-file">
                                <input type="file"
                                       name="file"
                                       id="file"
                                       class="custom-file-input @error('file') is-invalid @enderror"
                                       accept=".csv,.xlsx,.xls,.txt"
                                       required>
                                <label class="custom-file-label" for="file">Choose Excel, CSV, or TXT file...</label>
                            </div>
                            <button type="button" id="previewBtn" class="btn-secondary" disabled>
                                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-eye"/></svg>
                                Preview
                            </button>
                        </div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="ui-hint">
                            Supported: CSV, XLSX, XLS, TXT files up to 40MB. Large files are processed in chunks automatically.
                        </small>
                    </div>

                    <div>
                        <p class="ui-label">Import Options</p>
                        <div class="pt-options">
                            <div class="form-check pt-option">
                                <input type="checkbox" name="options[]" value="skip_duplicates" id="skip_duplicates" class="form-check-input" {{ in_array('skip_duplicates', old('options', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="skip_duplicates">
                                    Skip Duplicate Terminal IDs
                                </label>
                            </div>
                            <div class="form-check pt-option">
                                <input type="checkbox" name="options[]" value="update_existing" id="update_existing" class="form-check-input" {{ in_array('update_existing', old('options', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="update_existing">
                                    Update Existing Records
                                </label>
                            </div>
                        </div>
                        <small class="ui-hint">
                            If neither option is selected, duplicate terminal IDs will cause import errors.
                        </small>
                    </div>
                </div>

                <div class="ui-card-footer pt-footer-end">
                    <a href="{{ route('pos-terminals.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary" id="importBtn" disabled>
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-upload"/></svg>
                        Start Smart Import
                    </button>
                </div>
            </form>
        </div>

        {{-- System Template Information --}}
        <div class="pt-side">
            <div class="ui-card">
                <div class="ui-card-header">
                    <h3>Expected Template Columns</h3>
                </div>
                <div class="ui-card-body">
                    <p class="pt-side-label">Required Fields</p>
                    <ul class="pt-list">
                        <li><span class="pt-req" aria-hidden="true">*</span> Terminal ID</li>
                        <li><span class="pt-req" aria-hidden="true">*</span> Merchant Name</li>
                    </ul>

                    <div class="pt-cols">
                        <div>
                            <p class="pt-side-label">Optional Fields</p>
                            <ul class="pt-list pt-list-dots">
                                <li>Merchant ID</li>
                                <li>Legal Name</li>
                                <li>Contact Person</li>
                                <li>Phone Number</li>
                                <li>Email Address</li>
                                <li>Physical Address</li>
                                <li>City, Province, Region</li>
                            </ul>
                        </div>
                        <div>
                            <p class="pt-side-label">Technical Fields</p>
                            <ul class="pt-list pt-list-dots">
                                <li>Business Type</li>
                                <li>Terminal Model</li>
                                <li>Serial Number</li>
                                <li>Installation Date</li>
                                <li>Status/Condition</li>
                                <li>Issues/Comments</li>
                                <li>Corrective Actions</li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-note">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-info"/></svg>
                        <span><strong>Smart Detection:</strong> Column names are automatically detected regardless of order.</span>
                    </div>
                </div>
            </div>

            <div class="ui-card">
                <div class="ui-card-header">
                    <h3>Import Specifications</h3>
                </div>
                <div class="ui-card-body">
                    <p class="pt-side-label">Supported Formats</p>
                    <div class="pt-formats">
                        <span class="pt-code">CSV</span>
                        <span class="pt-code">XLSX</span>
                        <span class="pt-code">XLS</span>
                        <span class="pt-code">TXT</span>
                    </div>

                    <p class="pt-side-label">File Size Limits</p>
                    <ul class="pt-list pt-list-dots pt-kv-list">
                        <li><span><strong>Maximum:</strong> 40MB+ (Large file support)</span></li>
                        <li><span><strong>Recommended:</strong> Up to 30MB for optimal speed</span></li>
                        <li><span><strong>Processing:</strong> Chunked for large files</span></li>
                    </ul>

                    <p class="pt-side-label">Smart Features</p>
                    <ul class="pt-list">
                        <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Auto-detects column headers</li>
                        <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Processes any column order</li>
                        <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Stores extra columns as metadata</li>
                        <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Memory-optimized for large files</li>
                        <li><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg> Real-time preview and validation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">File Preview & Column Mapping</h5>
                <button type="button" class="pt-icon-btn" data-dismiss="modal" aria-label="Close" title="Close">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Initial content will be replaced by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-dismiss="modal">Close Preview</button>
                <button type="button" class="btn-primary" id="proceedImport">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg>
                    Looks Good - Proceed with Import
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Upload Progress (shown during preview upload) -->
<div id="upload-progress-wrap" class="pt-upload-wrap" style="display:none;">
  <div class="pt-progress-row">
    <small id="upload-progress-label">Uploading…</small>
    <small id="upload-progress-percent">0%</small>
  </div>
  <div class="progress pt-progress">
    <div id="upload-progress-bar" class="progress-bar" style="width:0%"></div>
  </div>
  <small id="upload-speed" class="pt-state-meta"></small>
  <button id="cancel-upload-btn" type="button" class="btn-danger pt-btn-sm" style="display:none; margin-top:10px;">
    Cancel upload
  </button>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="pt-state">
                    <svg class="mv-i pt-spin pt-state-icon" aria-hidden="true"><use href="#i-refresh"/></svg>
                    <h5 class="pt-state-title">Processing Your Import</h5>
                    <p class="pt-state-text">Large files are processed in chunks. This may take a few minutes...</p>
                    <div class="pt-bar"><span></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('POS Terminal Import JavaScript loaded successfully');

    // File input change handler
    $('#file').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Update file label
            $(this).next('.custom-file-label').html(file.name);

            // Enable preview and import buttons
            $('#previewBtn').prop('disabled', false);
            $('#importBtn').prop('disabled', false);

            // Validate file size (40MB = 40 * 1024 * 1024 bytes)
            const maxSize =  64 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size exceeds 40MB limit. Please choose a smaller file or contact support for larger imports.');
                $(this).val('');
                $(this).next('.custom-file-label').html('Choose Excel, CSV, or TXT file...');
                $('#previewBtn').prop('disabled', true);
                $('#importBtn').prop('disabled', true);
                return;
            }

            // Check file type
            const allowedTypes = ['.csv', '.xlsx', '.xls', '.txt'];
            const fileExtension = file.name.toLowerCase().substring(file.name.lastIndexOf('.'));
            if (!allowedTypes.includes(fileExtension)) {
                alert('Please select a valid file type: CSV, XLSX, XLS, or TXT');
                $(this).val('');
                $(this).next('.custom-file-label').html('Choose Excel, CSV, or TXT file...');
                $('#previewBtn').prop('disabled', true);
                $('#importBtn').prop('disabled', true);
                return;
            }
        } else {
            $('#previewBtn').prop('disabled', true);
            $('#importBtn').prop('disabled', true);
        }
    });

    // Preview button handler
    $('#previewBtn').on('click', function() {
        console.log('Preview button clicked');

        const formData = new FormData();
        const fileInput = document.getElementById('file');
        const mappingId = $('#mapping_id').val();
        const clientId = $('#client_id').val();

        if (!fileInput.files[0]) {
            alert('Please select a file first');
            return;
        }

        if (!clientId) {
            alert('Please select a client first');
            return;
        }

        console.log('Starting file upload with progress tracking');

        const csrf = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
        const file = fileInput.files[0];
        const fileSize = file.size;
        const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);

        formData.append('file', file);
        formData.append('client_id', clientId);
        if (mappingId) formData.append('mapping_id', mappingId);
        formData.append('preview_rows', '5');

        // Show modal with initial upload progress
        $('#previewModal').modal('show');
        $('#previewContent').html(`
            <div class="pt-state" id="uploadProgressContainer">
                <h5 class="pt-state-title">Uploading File...</h5>
                <p class="pt-state-text">File size: ${fileSizeMB} MB</p>
                <div class="pt-progress-row is-end"><span id="uploadPercentText">0%</span></div>
                <div class="progress pt-progress">
                    <div id="uploadProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <p id="uploadSpeedText" class="pt-state-meta"></p>
                <button type="button" id="cancelUploadBtn" class="btn-danger pt-btn-sm">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg> Cancel Upload
                </button>
            </div>
        `);

        // Create XHR request with progress tracking
        const xhr = new XMLHttpRequest();
        let uploadStartTime = Date.now();
        let uploadCancelled = false;

        // Handle upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                const loadedMB = (e.loaded / (1024 * 1024)).toFixed(2);
                const totalMB = (e.total / (1024 * 1024)).toFixed(2);

                // Calculate upload speed
                const elapsedTime = (Date.now() - uploadStartTime) / 1000; // seconds
                const speedMBps = (e.loaded / (1024 * 1024)) / elapsedTime;
                const remainingBytes = e.total - e.loaded;
                const remainingTime = Math.round(remainingBytes / (e.loaded / elapsedTime));

                $('#uploadProgressBar').css('width', percentComplete + '%');
                $('#uploadPercentText').text(percentComplete + '%');
                $('#uploadSpeedText').html(`
                    Uploaded: ${loadedMB} MB / ${totalMB} MB<br>
                    Speed: ${speedMBps.toFixed(2)} MB/s
                    ${remainingTime > 0 ? ` | ETA: ${remainingTime}s` : ''}
                `);
            }
        });

        // Handle upload completion (start server processing)
        xhr.upload.addEventListener('load', function() {
            if (!uploadCancelled) {
                $('#previewContent').html(`
                    <div class="pt-state">
                        <svg class="mv-i pt-spin pt-state-icon" aria-hidden="true"><use href="#i-refresh"/></svg>
                        <h5 class="pt-state-title">Analyzing File...</h5>
                        <p class="pt-state-text">Processing ${fileSizeMB} MB file. This may take a moment...</p>
                        <div class="pt-bar"><span></span></div>
                        <p class="pt-state-meta">Analyzing columns and validating data...</p>
                    </div>
                `);
            }
        });

        // Handle response
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        displayPreview(response);
                    } else {
                        $('#previewContent').html(`
                            <div class="pt-alert is-crit">
                                <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
                                <div><p class="pt-alert-title">Preview Error</p><p class="pt-alert-text">${response.message}</p></div>
                            </div>
                        `);
                    }
                } catch (e) {
                    $('#previewContent').html(`
                        <div class="pt-alert is-crit">
                            <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
                            <div><p class="pt-alert-title">Processing Error</p><p class="pt-alert-text">Failed to parse server response. Please try again.</p></div>
                        </div>
                    `);
                }
            } else {
                let message = 'Failed to preview file. Please check the file format and try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    message = errorResponse.message || message;
                } catch (e) {
                    // Use default message
                }
                $('#previewContent').html(`
                    <div class="pt-alert is-crit">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
                        <div><p class="pt-alert-title">Preview Failed</p><p class="pt-alert-text">${message}</p></div>
                    </div>
                `);
            }
        });

        // Handle errors
        xhr.addEventListener('error', function() {
            if (!uploadCancelled) {
                $('#previewContent').html(`
                    <div class="pt-alert is-crit">
                        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
                        <div><p class="pt-alert-title">Upload Failed</p><p class="pt-alert-text">Network error occurred. Please check your connection and try again.</p></div>
                    </div>
                `);
            }
        });

        // Handle timeout
        xhr.addEventListener('timeout', function() {
            $('#previewContent').html(`
                <div class="pt-alert is-warn">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-clock"/></svg>
                    <div><p class="pt-alert-title">Upload Timeout</p><p class="pt-alert-text">The upload took too long. Please try a smaller file or contact support.</p></div>
                </div>
            `);
        });

        // Cancel button handler
        $(document).off('click', '#cancelUploadBtn').on('click', '#cancelUploadBtn', function() {
            uploadCancelled = true;
            xhr.abort();
            $('#previewContent').html(`
                <div class="pt-alert is-info">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-info"/></svg>
                    <div><p class="pt-alert-title">Upload Cancelled</p><p class="pt-alert-text">The upload was cancelled by user.</p></div>
                </div>
            `);
        });

        // Send the request
        xhr.open('POST', '{{ route("pos-terminals.preview-import") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.timeout = 300000; // 5 minutes timeout
        xhr.send(formData);
    });

    // Proceed with import button
    $('#proceedImport').on('click', function() {
        $('#previewModal').modal('hide');
        // Auto-submit the form to start the real import
        $('#importForm').trigger('submit');
    });

    // Form submission handler with progress tracking
    $('#importForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const file = $('#file')[0].files[0];
        const fileSizeMB = file ? (file.size / (1024 * 1024)).toFixed(2) : '0';
        const csrf = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

        // Show loading modal with progress
        $('#loadingModal').modal('show');

        // Update modal content to show progress
        $('#loadingModal .modal-body').html(`
            <div class="pt-state">
                <svg class="mv-i pt-spin pt-state-icon" aria-hidden="true"><use href="#i-refresh"/></svg>
                <h5 id="importStatusTitle" class="pt-state-title">Uploading...</h5>
                <p id="importStatusText" class="pt-state-text">Uploading ${fileSizeMB} MB file...</p>
                <div class="pt-progress-row is-end"><span id="importPercentText">0%</span></div>
                <div class="progress pt-progress">
                    <div id="importProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <p id="importSpeedText" class="pt-state-meta"></p>
            </div>
        `);

        const xhr = new XMLHttpRequest();
        let uploadStartTime = Date.now();

        // Upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                const loadedMB = (e.loaded / (1024 * 1024)).toFixed(2);
                const totalMB = (e.total / (1024 * 1024)).toFixed(2);
                const elapsedTime = (Date.now() - uploadStartTime) / 1000;
                const speedMBps = (e.loaded / (1024 * 1024)) / elapsedTime;

                $('#importProgressBar').css('width', percentComplete + '%');
                $('#importPercentText').text(percentComplete + '%');
                $('#importSpeedText').html(`
                    Uploaded: ${loadedMB} MB / ${totalMB} MB | Speed: ${speedMBps.toFixed(2)} MB/s
                `);
            }
        });

        // Upload complete, now processing
        xhr.upload.addEventListener('load', function() {
            $('#importStatusTitle').text('Processing Import...');
            $('#importStatusText').text('Processing data in chunks. This may take several minutes for large files...');
            $('#importProgressBar').removeClass('bg-primary').addClass('bg-success');
            $('#importProgressBar').css('width', '100%');
            $('#importPercentText').text('Processing...');
            $('#importSpeedText').html('Please wait while records are being imported...');
        });

        // Request complete
        xhr.addEventListener('load', function() {
            if (xhr.status === 200 || xhr.status === 302) {
                // Success - redirect will be handled automatically
                $('#loadingModal').modal('hide');
                window.location.href = '{{ route("pos-terminals.index") }}';
            } else {
                $('#loadingModal').modal('hide');
                alert('Import failed. Please check the error messages and try again.');
                window.location.reload();
            }
        });

        // Error handling
        xhr.addEventListener('error', function() {
            $('#loadingModal').modal('hide');
            alert('Network error occurred during import. Please try again.');
        });

        xhr.addEventListener('timeout', function() {
            $('#loadingModal').modal('hide');
            alert('Import timeout. The file may be too large or processing took too long.');
        });

        // Send request
        xhr.open('POST', '{{ route("pos-terminals.import") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.timeout = 600000; // 10 minutes
        xhr.send(formData);

        return false;
    });

    function displayPreview(response) {
        let html = `
            <div class="pt-summary">
                <div class="pt-summary-box">
                    <p class="pt-summary-title">File Analysis</p>
                    <ul class="pt-summary-list">
                        <li><strong>Mapping:</strong> ${response.mapping_name}</li>
                        <li><strong>Columns Found:</strong> ${response.headers.length}</li>
                        <li><strong>Preview Rows:</strong> ${response.preview_data.length}</li>
                    </ul>
                </div>
                <div class="pt-summary-box">
                    <p class="pt-summary-title">Column Mapping</p>
                    <ul class="pt-summary-list">
                        <li><strong>Mapped Fields:</strong> ${response.column_mapping_info.mapped_fields.length}</li>
                        <li><strong>Extra Fields:</strong> ${response.column_mapping_info.extra_fields.length}</li>
                        ${
                            response.column_mapping_info.missing_required.length > 0
                            ? `<li class="pt-text-crit"><strong>Missing Required:</strong> ${response.column_mapping_info.missing_required.join(', ')}</li>`
                            : '<li class="pt-text-good"><strong>All Required Fields Found</strong></li>'
                        }
                    </ul>
                </div>
            </div>

            <div class="pt-preview-grid">
                <div class="pt-subcard">
                    <div class="pt-subcard-head">Detected Columns in Your File</div>
                    <div class="pt-scroll">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Column Header</th>
                                </tr>
                            </thead>
                            <tbody>`;

        // We show headers plainly; mapping indices aren’t returned by API
        response.headers.forEach((header, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><span class="pt-code">${header}</span></td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pt-subcard">
                    <div class="pt-subcard-head">Preview Data (First ${response.preview_data.length} rows)</div>
                    <div class="pt-scroll">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Terminal ID</th>
                                    <th>Merchant</th>
                                    <th>Status</th>
                                    <th>Validation</th>
                                </tr>
                            </thead>
                            <tbody>`;

        response.preview_data.forEach(row => {
            const statusBadge = row.validation_status === 'valid'
                ? '<span class="status-badge badge-green">Valid</span>'
                : '<span class="status-badge badge-red">Error</span>';

            html += `
                <tr>
                    <td>${row.row_number}</td>
                    <td><span class="pt-code">${row.mapped_data?.terminal_id || 'N/A'}</span></td>
                    <td>${row.mapped_data?.merchant_name || 'N/A'}</td>
                    <td>${row.mapped_data?.status || 'N/A'}</td>
                    <td>
                        ${statusBadge}
                        ${row.validation_status !== 'valid' ? `<span class="pt-row-error">${row.validation_message}</span>` : ''}
                    </td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        if (response.column_mapping_info.extra_fields.length > 0) {
            html += `
                <div class="pt-alert is-info">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-plus-circle"/></svg>
                    <div>
                        <p class="pt-alert-title">Extra Fields Found</p>
                        <p class="pt-alert-text">These columns will be stored as additional metadata:</p>
                        <p class="pt-alert-text"><strong>${response.column_mapping_info.extra_fields.join(', ')}</strong></p>
                    </div>
                </div>
            `;
        }

        $('#previewContent').html(html);
    }
});
</script>
@endpush
