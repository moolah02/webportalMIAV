<!-- Import Tab -->
<div id="import-tab" class="tab-content">
    <div class="main-card">
        <h3 class="section-title">📤 Import Terminal Data</h3>
        <p class="section-description">Bulk import terminal data from bank or client Excel/CSV files with flexible column mapping</p>

        {{-- Flash & validation messages --}}
        @if(session('success'))
            <div style="margin-bottom:16px; padding:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724; border-radius:6px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="margin-bottom:16px; padding:12px; border:1px solid #f5c6cb; background:#f8d7da; color:#721c24; border-radius:6px;">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div style="margin-bottom:16px; padding:12px; border:1px solid #ffeeba; background:#fff3cd; color:#856404; border-radius:6px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pos-terminals.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
            @csrf

            <!-- Client Selection -->
            <div class="form-group">
                <label for="client_id" class="form-label">Select Client/Bank *</label>
                <select name="client_id" id="client_id" required class="form-select">
                    <option value="">Choose the client for these terminals...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                    @endforeach
                </select>
                @error('client_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Column Mapping Selection -->
            <div class="form-group">
                <label for="mapping_id" class="form-label">Column Mapping (Optional)</label>
                <div class="mapping-container">
                    <select name="mapping_id" id="mapping_id" class="form-select">
                        <option value="">Use Smart Auto-Detection</option>
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
                    <div class="mapping-actions">
                        <a href="{{ route('pos-terminals.column-mapping') }}" class="btn btn-outline btn-small" target="_blank">
                            Create New Mapping
                        </a>
                        <a href="{{ route('pos-terminals.column-mapping') }}" class="btn btn-outline btn-small" target="_blank">
                            Manage Mappings
                        </a>
                    </div>
                </div>
                <small class="help-text">Select a pre-configured mapping for specific file formats, or use auto-detection for most files</small>
                <div id="mappingDescription" class="mapping-description" style="display: none;"></div>
            </div>

            <!-- File Upload -->
            <div class="file-upload-area">
                <div class="upload-icon">📁</div>
                <h4>Upload Your Excel/CSV File</h4>
                <p>Select your terminal data Excel (XLSX/XLS) or CSV file - we support both formats</p>

                <input
                    type="file"
                    name="file"
                    id="csvFile"
                    accept=".csv,.xlsx,.xls"
                    required
                    class="file-input">

                @error('file')
                    <div class="form-error">{{ $message }}</div>
                @enderror

                <div id="fileName" class="file-name"></div>
                <div class="file-info">
                    Supported formats: Excel (XLSX, XLS), CSV • Large files supported (up to 1GB with 5-minute processing)<br>
                    <span class="file-format-note">💡 Excel files are preferred - no need to convert to CSV first</span>
                </div>
            </div>

            <!-- Import Options -->
            <div class="form-group">
                <label class="form-label">Import Options</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="options[]" value="skip_duplicates" checked>
                        <span class="checkmark"></span>
                        Skip duplicate terminal IDs
                        <small>Existing terminals with same ID will be ignored</small>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="options[]" value="update_existing">
                        <span class="checkmark"></span>
                        Update existing terminals with new data
                        <small>Override existing terminal data with imported values</small>
                    </label>
                </div>
            </div>

            <!-- Preview Button -->
            <div class="form-group">
                <button type="button" id="previewBtn" class="btn btn-outline" disabled>
                    <span class="btn-icon">👁️</span>
                    Preview Import Data
                </button>
                <small class="help-text" id="previewHelp">Upload a file to enable preview</small>
            </div>

            <!-- Data Preview -->
            <div id="dataPreview" class="data-preview" style="display: none;">
                <h4>📊 Data Preview</h4>
                <div class="preview-content">
                    <div class="preview-stats">
                        <span class="stat">Mapping: <strong id="mappingName">Auto-Detection</strong></span>
                        <span class="stat">Rows: <strong id="rowCount">0</strong></span>
                        <span class="stat">Columns: <strong id="columnCount">0</strong></span>
                    </div>
                    <div class="preview-table-container">
                        <table id="previewTable" class="preview-table"></table>
                    </div>
                    <div class="preview-actions">
                        <button type="button" class="btn btn-outline btn-small" onclick="closePreview()">Close Preview</button>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="resetForm()">Reset Form</button>
                <a href="{{ route('pos-terminals.download-template') }}" class="btn btn-outline">
                    <span class="btn-icon">📥</span>
                    Download Template
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-icon">⚡</span>
                    Process Import
                </button>
            </div>
        </form>

        <!-- Import Tips -->
        <div class="import-tips">
            <h4>💡 Import Tips</h4>
            <div class="tips-grid">
                <div class="tip-card">
                    <div class="tip-icon">📋</div>
                    <div class="tip-content">
                        <strong>Excel/CSV Support</strong>
                        <p>Both Excel (XLSX, XLS) and CSV files are fully supported with auto-detection</p>
                    </div>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">🎯</div>
                    <div class="tip-content">
                        <strong>Smart Detection</strong>
                        <p>Our system automatically detects Terminal ID and Merchant Name columns</p>
                    </div>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">🔄</div>
                    <div class="tip-content">
                        <strong>Large File Support</strong>
                        <p>Handle files up to 1GB with 30MB+ Excel files processing smoothly</p>
                    </div>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">⚙️</div>
                    <div class="tip-content">
                        <strong>Flexible Mapping</strong>
                        <p>Works with any column layout - extra fields are automatically preserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mapping-container { display:flex; gap:12px; align-items:flex-end; }
.mapping-container .form-select { flex:1; }
.mapping-actions { display:flex; gap:8px; }
.btn-small { padding:8px 16px; font-size:12px; min-width:auto; }
.help-text { color:#666; font-size:12px; margin-top:4px; display:block; }
.mapping-description { margin-top:8px; padding:8px 12px; background:#f8f9ff; border:1px solid #e3f2fd; border-radius:4px; font-size:12px; color:#666; }
.file-format-note { color:#007bff; font-weight:500; }
.checkbox-group { display:flex; flex-direction:column; gap:16px; }
.checkbox-label { display:flex; align-items:flex-start; gap:12px; cursor:pointer; padding:12px; border:1px solid #e9ecef; border-radius:8px; transition:all .2s; }
.checkbox-label:hover { border-color:#007bff; background:#f8f9ff; }
.checkbox-label input[type="checkbox"] { margin:0; width:16px; height:16px; accent-color:#007bff; }
.checkbox-label small { color:#666; font-size:12px; margin-top:2px; display:block; }
.data-preview { margin-top:24px; padding:20px; border:1px solid #dee2e6; border-radius:8px; background:#f8f9fa; }
.data-preview h4 { margin:0 0 16px 0; color:#333; }
.preview-stats { display:flex; gap:20px; margin-block-end:16px; flex-wrap:wrap; }
.preview-stats .stat { padding:6px 12px; background:white; border-radius:4px; font-size:12px; }
.preview-table-container { max-height:300px; overflow:auto; border:1px solid #dee2e6; border-radius:4px; background:white; margin-block-end:16px; }
.preview-table { width:100%; border-collapse:collapse; font-size:12px; }
.preview-table th, .preview-table td { padding:8px; border:1px solid #dee2e6; text-align:left; }
.preview-table th { background:#f8f9fa; font-weight:600; position:sticky; top:0; }
.preview-actions { text-align:right; }
.import-tips { margin-top:40px; padding-top:30px; border-top:1px solid #dee2e6; }
.import-tips h4 { margin:0 0 20px 0; color:#333; }
.tips-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }
.tip-card { display:flex; gap:12px; padding:16px; background:#f8f9ff; border:1px solid #e3f2fd; border-radius:8px; }
.tip-icon { font-size:20px; flex-shrink:0; }
.tip-content strong { display:block; margin-block-end:4px; color:#333; }
.tip-content p { margin:0; font-size:12px; color:#666; line-height:1.4; }
.btn-icon { margin-right:6px; }

/* Debug info styling */
.debug-info {
    margin-top:16px;
    padding:12px;
    background:#f8f9fa;
    border:1px solid #dee2e6;
    border-radius:4px;
    font-family:monospace;
    font-size:12px;
    white-space:pre-wrap;
}

/* Status indicators */
.status-valid { color: #28a745; font-weight: 500; }
.status-error { color: #dc3545; font-weight: 500; }
.status-badge { padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 500; text-transform: uppercase; }
.status-active { background: #d4edda; color: #155724; }
.status-offline { background: #f8d7da; color: #721c24; }
.status-faulty { background: #fff3cd; color: #856404; }

@media (max-width: 768px){
    .mapping-container{flex-direction:column; align-items:stretch;}
    .mapping-actions{justify-content:stretch;}
    .mapping-actions .btn{flex:1;}
    .tips-grid{grid-template-columns:1fr;}
    .preview-stats{flex-direction:column; gap:8px;}
}
</style>

<script>
// Enhanced file upload feedback and preview functionality with detailed error handling
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const previewBtn = document.getElementById('previewBtn');
    const dataPreview = document.getElementById('dataPreview');
    const mappingSelect = document.getElementById('mapping_id');
    const mappingDescription = document.getElementById('mappingDescription');
    const previewHelp = document.getElementById('previewHelp');

    // Debug function to show detailed information
    function showDebugInfo(info, type = 'info') {
        console.log(`[${type.toUpperCase()}]`, info);

        // Create or update debug display
        let debugDiv = document.querySelector('.debug-info');
        if (!debugDiv) {
            debugDiv = document.createElement('div');
            debugDiv.className = 'debug-info';
            document.querySelector('.import-form').appendChild(debugDiv);
        }

        const timestamp = new Date().toLocaleTimeString();
        debugDiv.textContent += `[${timestamp}] ${type.toUpperCase()}: ${JSON.stringify(info, null, 2)}\n`;
        debugDiv.scrollTop = debugDiv.scrollHeight;
    }

    // File input change handler
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showDebugInfo({
                    action: 'File selected',
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    lastModified: new Date(file.lastModified).toISOString()
                });

                fileName.innerHTML = `
                    <div style="color: #28a745; font-weight: 500;">
                        ✅ Selected: ${file.name}
                    </div>
                    <div style="color: #666; font-size: 12px; margin-top: 2px;">
                        Size: ${(file.size / 1024 / 1024).toFixed(2)} MB | Type: ${file.type || 'Unknown'}
                    </div>
                `;
                if (previewBtn) previewBtn.disabled = false;
                if (previewHelp) previewHelp.textContent = 'Click to preview how your data will be imported';
            } else {
                showDebugInfo({ action: 'File cleared' });
                fileName.textContent = '';
                if (previewBtn) previewBtn.disabled = true;
                if (previewHelp) previewHelp.textContent = 'Upload a file to enable preview';
                if (dataPreview) dataPreview.style.display = 'none';
            }
        });
    }

    // Mapping selection change handler
    if (mappingSelect && mappingDescription) {
        mappingSelect.addEventListener('change', function(e) {
            const selectedOption = e.target.selectedOptions[0];
            const description = selectedOption ? selectedOption.getAttribute('data-description') : '';

            showDebugInfo({
                action: 'Mapping changed',
                mappingId: e.target.value,
                mappingName: selectedOption ? selectedOption.text : 'Auto-Detection',
                description: description
            });

            if (description) {
                mappingDescription.textContent = description;
                mappingDescription.style.display = 'block';
            } else {
                mappingDescription.style.display = 'none';
            }
        });
    }

    // Preview button click handler
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            const file = fileInput.files[0];
            const mappingId = mappingSelect.value;

            showDebugInfo({
                action: 'Preview requested',
                hasFile: !!file,
                fileName: file ? file.name : null,
                fileSize: file ? file.size : null,
                mappingId: mappingId || 'Auto-Detection'
            });

            if (!file) {
                alert('Please select a file first.');
                return;
            }

            previewBtn.innerHTML = '<span class="btn-icon">⏳</span> Loading Preview...';
            previewBtn.disabled = true;

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        document.querySelector('input[name="_token"]')?.value || '';

            const formData = new FormData();
            formData.append('file', file);
            formData.append('mapping_id', mappingId);
            formData.append('preview_rows', 5);
            formData.append('_token', csrf);

            showDebugInfo({
                action: 'Sending preview request',
                url: '{{ route("pos-terminals.preview-import") }}',
                fileSize: file.size,
                mappingId: mappingId || 'Auto-Detection',
                csrfToken: csrf ? 'Present' : 'Missing'
            });

            fetch('{{ route("pos-terminals.preview-import") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async (response) => {
                showDebugInfo({
                    action: 'Preview response received',
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    contentType: response.headers.get('content-type')
                });

                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) {
                    const msg = contentType.includes('application/json')
                        ? (await response.json()).message || 'Preview failed'
                        : await response.text();
                    throw new Error(msg);
                }
                return response.json();
            })
            .then(data => {
                showDebugInfo({
                    action: 'Preview data received',
                    success: data.success,
                    mappingName: data.mapping_name,
                    totalRows: data.total_rows_in_file,
                    previewRows: data.preview_data ? data.preview_data.length : 0,
                    headers: data.headers
                });

                if (data.success) displayPreview(data);
                else alert('Preview failed: ' + (data.message || 'Unknown error'));
            })
            .catch(error => {
                showDebugInfo({
                    action: 'Preview error',
                    error: error.message
                }, 'error');

                console.error('Preview error:', error);
                alert('Preview failed: ' + error.message + '\nCheck the debug panel below for details.');
            })
            .finally(() => {
                previewBtn.innerHTML = '<span class="btn-icon">👁️</span> Preview Import Data';
                previewBtn.disabled = false;
            });
        });
    }

    // Enhanced form submission handler with detailed debugging
    const importForm = document.querySelector('.import-form');
    if (importForm && submitBtn) {
        importForm.addEventListener('submit', function(e) {
            console.log('Form submission started');
            showDebugInfo({ action: 'Form submission started' });

            const file = fileInput.files[0];
            const clientId = document.getElementById('client_id').value;
            const mappingId = document.getElementById('mapping_id').value;
            const options = Array.from(document.querySelectorAll('input[name="options[]"]:checked')).map(cb => cb.value);

            // Detailed validation logging
            const validationInfo = {
                hasFile: !!file,
                fileName: file ? file.name : null,
                fileSize: file ? file.size : null,
                fileType: file ? file.type : null,
                clientId: clientId,
                clientSelected: !!clientId,
                mappingId: mappingId || 'Auto-Detection',
                options: options,
                formAction: importForm.action,
                formMethod: importForm.method
            };

            showDebugInfo({
                action: 'Pre-submission validation',
                ...validationInfo
            });

            // Check file
            if (!file) {
                e.preventDefault();
                showDebugInfo({ action: 'Form submission blocked', reason: 'No file selected' }, 'error');
                alert('Please select a file before submitting.');
                return false;
            }

            // Check client
            if (!clientId) {
                e.preventDefault();
                showDebugInfo({ action: 'Form submission blocked', reason: 'No client selected' }, 'error');
                alert('Please select a client before submitting.');
                return false;
            }

            // Additional file validation
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const allowedExtensions = ['csv', 'xls', 'xlsx'];

            if (!allowedExtensions.includes(fileExtension)) {
                e.preventDefault();
                showDebugInfo({
                    action: 'Form submission blocked',
                    reason: 'Invalid file type',
                    fileExtension: fileExtension,
                    fileName: file.name
                }, 'error');
                alert(`Invalid file type. Please select an Excel (XLSX, XLS) or CSV file. Current file: ${file.name}`);
                return false;
            }

            showDebugInfo({
                action: 'Form submission proceeding',
                allValidationsPassed: true,
                submissionTime: new Date().toISOString()
            });

            submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Processing (up to 5 minutes for large files)...';
            submitBtn.disabled = true;

            // Extended timeout for large files
            const timeoutId = setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = '<span class="btn-icon">⚡</span> Process Import';
                    submitBtn.disabled = false;
                    showDebugInfo({
                        action: 'Form submission timeout',
                        reason: 'Button re-enabled after 5 minutes - check server logs'
                    }, 'warning');
                }
            }, 300000); // 5 minutes

            window.importTimeoutId = timeoutId;
        });
    }
});

function displayPreview(data) {
    const mappingName = document.getElementById('mappingName');
    const rowCount = document.getElementById('rowCount');
    const columnCount = document.getElementById('columnCount');
    const previewTable = document.getElementById('previewTable');
    const dataPreview = document.getElementById('dataPreview');

    mappingName.textContent = data.mapping_name;
    rowCount.textContent = data.preview_data.length;
    columnCount.textContent = data.headers.length;

    let tableHTML = '<thead><tr>';
    const displayHeaders = ['Row', 'Terminal ID', 'Merchant Name', 'City', 'Status', 'Validation'];
    displayHeaders.forEach(h => { tableHTML += `<th>${h}</th>`; });
    tableHTML += '</tr></thead><tbody>';

    (data.preview_data || []).forEach(row => {
        const md = row.mapped_data || {};
        const st = md.status || 'active';
        const validationClass = row.validation_status === 'valid' ? 'status-valid' : 'status-error';

        tableHTML += '<tr>';
        tableHTML += `<td>${row.row_number ?? ''}</td>`;
        tableHTML += `<td>${md.terminal_id || 'N/A'}</td>`;
        tableHTML += `<td>${md.merchant_name || 'N/A'}</td>`;
        tableHTML += `<td>${md.city || 'N/A'}</td>`;
        tableHTML += `<td><span class="status-badge status-${st}">${st}</span></td>`;
        tableHTML += `<td><span class="${validationClass}">${row.validation_message}</span></td>`;
        tableHTML += '</tr>';
    });

    tableHTML += '</tbody>';
    previewTable.innerHTML = tableHTML;
    dataPreview.style.display = 'block';
    dataPreview.scrollIntoView({ behavior: 'smooth' });
}

function closePreview() {
    const el = document.getElementById('dataPreview');
    if (el) el.style.display = 'none';
}

function resetForm() {
    const form = document.querySelector('.import-form');
    if (form) form.reset();
    const fn = document.getElementById('fileName');
    if (fn) fn.textContent = '';
    const dp = document.getElementById('dataPreview');
    if (dp) dp.style.display = 'none';
    const pb = document.getElementById('previewBtn');
    if (pb) pb.disabled = true;
    const md = document.getElementById('mappingDescription');
    if (md) md.style.display = 'none';

    // Clear debug info
    const debugDiv = document.querySelector('.debug-info');
    if (debugDiv) debugDiv.remove();

    // Clear any timeouts
    if (window.importTimeoutId) {
        clearTimeout(window.importTimeoutId);
    }
}
</script>
