{{-- Terminal Upload Section for Project Create/Edit --}}
<div id="terminal-upload-section" class="tus">

    <div class="tus-head">
        <div>
            <div class="tus-title">Terminal Assignment
                @if(isset($project) && $project->exists)
                    <span class="badge badge-blue">{{ $project->projectTerminals()->where('is_active', true)->count() }} assigned</span>
                @else
                    <span class="tus-muted">optional</span>
                @endif
            </div>
            <div class="tus-muted">Upload a CSV/Excel list to bulk-assign terminals</div>
        </div>
        @if(isset($project) && $project->exists)
        <button type="button" class="btn-secondary btn-sm" onclick="viewProjectTerminals()">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-list"/></svg> View List
        </button>
        @endif
    </div>

    @if(isset($project) && $project->exists)
    <div class="tus-info">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-monitor"/></svg>
        <span><strong>{{ $project->projectTerminals()->where('is_active', true)->count() }}</strong> terminals currently assigned to this project.</span>
    </div>
    @endif

    <div class="tus-zone">
        <div>
            <label class="ui-label" for="terminal_file">File <span class="tus-muted">(CSV, Excel, TXT)</span></label>
            <div class="tus-file">
                <input type="file" id="terminal_file" class="ui-input" accept=".csv,.xlsx,.xls,.txt" onchange="handleTerminalFileSelect(this)">
                <button type="button" id="previewTerminalsBtn" class="btn-primary" onclick="previewTerminalUpload()" disabled>Preview</button>
            </div>
            <div class="tus-muted" style="margin-top:8px">
                File must contain a <code class="tus-code">terminal_id</code> column.
                <a href="{{ route('projects.terminals.download-template') }}" class="tus-link"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Download Template</a>
            </div>
        </div>
        <div class="tus-options">
            <div class="tus-options-title">Options</div>
            <label class="tus-check"><input type="checkbox" id="skip_duplicates" checked><span>Skip already assigned</span></label>
            <label class="tus-check"><input type="checkbox" id="create_missing"><span>Create missing terminals</span></label>
        </div>
    </div>

    <div id="uploadProgress" style="display: none; margin-top: 12px;">
        <div class="tus-bar"><div id="uploadProgressBar" style="width: 0%"></div></div>
        <div class="tus-muted" id="uploadProgressText" style="margin-top:4px">Uploading...</div>
    </div>

    <div id="terminalUploadSummary" style="display: none; margin-top: 12px;">
        <div class="alert-success tus-summary">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg>
            <span id="terminalUploadSummaryText"></span>
        </div>
    </div>

    <input type="hidden" name="uploaded_terminal_ids" id="uploaded_terminal_ids" value="">
    <input type="hidden" name="missing_terminals_data" id="missing_terminals_data" value="">
    <input type="hidden" name="terminal_inclusion_reason" id="terminal_inclusion_reason" value="Bulk Upload">
</div>

<style>
.tus-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
.tus-title { font-size: 13.5px; font-weight: 600; color: var(--mv-ink); display: flex; align-items: center; gap: 8px; }
.tus-muted { font-size: 12px; color: var(--mv-muted); font-weight: 400; }
.tus-info { display: flex; align-items: center; gap: 8px; padding: 9px 12px; margin-bottom: 12px; border: 1px solid var(--mv-line); border-radius: 8px; background: var(--mv-surface-2); font-size: 13px; color: var(--mv-ink-2); }
.tus-info .mv-i { color: var(--mv-muted); }
.tus-info strong { color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.tus-zone { display: grid; grid-template-columns: minmax(0, 1fr) 220px; gap: 16px; align-items: center; padding: 14px 16px; border: 1px dashed var(--mv-line-strong); border-radius: 10px; background: var(--mv-surface-2); }
@media (max-width: 760px) { .tus-zone { grid-template-columns: 1fr; } }
.tus-file { display: flex; gap: 8px; }
.tus-file .ui-input { flex: 1; min-width: 0; padding: 6px; background: var(--mv-surface); }
.tus-code { font-family: var(--mv-mono); font-size: 11.5px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 4px; padding: 0 4px; color: var(--mv-ink-2); }
.tus-link { color: var(--mv-accent-ink); text-decoration: none; margin-left: 6px; display: inline-flex; align-items: center; gap: 4px; }
.tus-link:hover { text-decoration: underline; }
.tus-options { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 8px; padding: 10px 12px; }
.tus-options-title { font-size: 11.5px; font-weight: 600; color: var(--mv-muted); letter-spacing: .05em; text-transform: uppercase; margin-bottom: 6px; }
.tus-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--mv-ink); padding: 3px 0; cursor: pointer; }
.tus-check input { width: 15px; height: 15px; accent-color: var(--mv-accent); }
.tus-bar { height: 6px; background: var(--mv-line); border-radius: 3px; overflow: hidden; }
.tus-bar > div { height: 100%; background: var(--mv-accent); border-radius: 3px; transition: width .2s ease; }
.tus-summary { display: flex; align-items: center; gap: 8px; padding: 10px 12px; font-size: 13px; border: 1px solid; border-radius: 8px; }
</style>

{{-- Include the preview modal --}}
@include('projects.partials.terminal-preview-modal')

{{-- Include terminals list modal for edit mode --}}
@if(isset($project) && $project->exists)
@include('projects.partials.terminal-list-modal')
@endif

<script>
// Terminal Upload JavaScript
let selectedTerminalIds = [];
let missingTerminalsData = [];
let previewData = null;

function handleTerminalFileSelect(input) {
    const file = input.files[0];
    const previewBtn = document.getElementById('previewTerminalsBtn');

    if (file) {
        // Validate file type
        const allowedTypes = ['.csv', '.xlsx', '.xls', '.txt'];
        const fileName = file.name.toLowerCase();
        const isValidType = allowedTypes.some(ext => fileName.endsWith(ext));

        if (!isValidType) {
            alert('Please select a CSV or Excel file (.csv, .xlsx, .xls)');
            input.value = '';
            previewBtn.disabled = true;
            return;
        }

        // Validate file size (50MB max)
        if (file.size > 50 * 1024 * 1024) {
            alert('File size exceeds 50MB limit');
            input.value = '';
            previewBtn.disabled = true;
            return;
        }

        previewBtn.disabled = false;
    } else {
        previewBtn.disabled = true;
    }
}

function previewTerminalUpload() {
    const fileInput = document.getElementById('terminal_file');
    const file = fileInput.files[0];

    if (!file) {
        alert('Please select a file first');
        return;
    }

    // Get project ID (for edit mode) or use 'new' for create mode
    const projectId = '{{ $project->id ?? "new" }}';

    if (projectId === 'new') {
        // For create mode, we need to validate client is selected first
        const clientSelect = document.getElementById('client_id');
        if (!clientSelect || !clientSelect.value) {
            alert('Please select a client first before uploading terminals');
            return;
        }
    }

    // Show progress
    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    progressDiv.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = 'Uploading file...';

    // Create form data
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    // Determine URL based on mode
    let previewUrl;
    @if(isset($project) && $project->exists)
        previewUrl = '{{ route("projects.terminals.preview-upload", $project) }}';
    @else
        // For create mode, we'll use a temporary preview endpoint
        previewUrl = '/projects/terminals/preview-upload-temp?client_id=' + document.getElementById('client_id').value;
    @endif

    // Use XHR for progress tracking
    const xhr = new XMLHttpRequest();
    xhr.open('POST', previewUrl, true);

    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = 'Uploading... ' + percent + '%';
        }
    });

    xhr.onload = function() {
        progressDiv.style.display = 'none';

        if (xhr.status === 200) {
            try {
                previewData = JSON.parse(xhr.responseText);
                if (previewData.success) {
                    showPreviewModal(previewData);
                } else {
                    alert('Preview failed: ' + (previewData.message || 'Unknown error'));
                }
            } catch (e) {
                alert('Failed to parse server response');
                console.error(e);
            }
        } else {
            try {
                const error = JSON.parse(xhr.responseText);
                alert('Preview failed: ' + (error.message || 'Server error'));
            } catch (e) {
                alert('Preview failed: Server error (' + xhr.status + ')');
            }
        }
    };

    xhr.onerror = function() {
        progressDiv.style.display = 'none';
        alert('Network error occurred during upload');
    };

    xhr.send(formData);
}

function showPreviewModal(data) {
    // Update summary counts
    document.getElementById('previewTotalCount').textContent = data.summary.total_in_file;
    document.getElementById('previewFoundCount').textContent = data.summary.can_assign;
    document.getElementById('previewAlreadyCount').textContent = data.summary.already_assigned;
    document.getElementById('previewNotFoundCount').textContent = data.summary.not_found;

    // Populate found terminals table
    const foundTable = document.getElementById('foundTerminalsTable');
    foundTable.innerHTML = '';

    if (data.results.found.length > 0) {
        data.results.found.forEach(terminal => {
            foundTable.innerHTML += `
                <tr>
                    <td><input type="checkbox" class="form-check-input terminal-checkbox" value="${terminal.id}" checked></td>
                    <td>${terminal.terminal_id}</td>
                    <td>${terminal.merchant_name || '-'}</td>
                    <td>${terminal.city || '-'}</td>
                    <td><span class="badge ${terminal.status === 'active' ? 'badge-green' : 'badge-gray'}">${terminal.status || '-'}</span></td>
                </tr>
            `;
        });
        document.getElementById('foundTerminalsSection').style.display = 'block';
    } else {
        document.getElementById('foundTerminalsSection').style.display = 'none';
    }

    // Populate already assigned table
    const alreadyTable = document.getElementById('alreadyAssignedTable');
    alreadyTable.innerHTML = '';

    if (data.results.already_assigned.length > 0) {
        data.results.already_assigned.forEach(terminal => {
            alreadyTable.innerHTML += `
                <tr>
                    <td>${terminal.terminal_id}</td>
                    <td>${terminal.merchant_name || '-'}</td>
                    <td>${terminal.city || '-'}</td>
                </tr>
            `;
        });
        document.getElementById('alreadyAssignedSection').style.display = 'block';
    } else {
        document.getElementById('alreadyAssignedSection').style.display = 'none';
    }

    // Populate not found table
    const notFoundTable = document.getElementById('notFoundTerminalsTable');
    notFoundTable.innerHTML = '';

    if (data.results.not_found.length > 0) {
        data.results.not_found.forEach(terminal => {
            const hasData = terminal.has_full_data;
            notFoundTable.innerHTML += `
                <tr>
                    <td>
                        ${hasData ? `<input type="checkbox" class="form-check-input missing-terminal-checkbox" data-terminal='${JSON.stringify(terminal.row_data)}'>` : '-'}
                    </td>
                    <td>${terminal.terminal_id}</td>
                    <td>${terminal.reason}</td>
                    <td>
                        ${hasData ?
                            '<span class="badge badge-yellow">Can create</span>' :
                            '<span class="badge badge-gray">No data</span>'}
                    </td>
                </tr>
            `;
        });
        document.getElementById('notFoundSection').style.display = 'block';
    } else {
        document.getElementById('notFoundSection').style.display = 'none';
    }

    // Store preview data
    previewData = data;

    // Show modal
    $('#terminalPreviewModal').modal('show');
}

function confirmTerminalUpload() {
    // Collect selected terminal IDs
    selectedTerminalIds = [];
    document.querySelectorAll('.terminal-checkbox:checked').forEach(cb => {
        selectedTerminalIds.push(parseInt(cb.value));
    });

    // Collect missing terminals to create (if option enabled)
    missingTerminalsData = [];
    if (document.getElementById('create_missing').checked) {
        document.querySelectorAll('.missing-terminal-checkbox:checked').forEach(cb => {
            try {
                const data = JSON.parse(cb.dataset.terminal);
                missingTerminalsData.push(data);
            } catch (e) {
                console.error('Failed to parse terminal data', e);
            }
        });
    }

    if (selectedTerminalIds.length === 0 && missingTerminalsData.length === 0) {
        alert('Please select at least one terminal to assign');
        return;
    }

    // Store in hidden fields
    document.getElementById('uploaded_terminal_ids').value = JSON.stringify(selectedTerminalIds);
    document.getElementById('missing_terminals_data').value = JSON.stringify(missingTerminalsData);

    // Update summary
    const summaryDiv = document.getElementById('terminalUploadSummary');
    const summaryText = document.getElementById('terminalUploadSummaryText');
    summaryText.textContent = `${selectedTerminalIds.length} terminals ready to assign` +
        (missingTerminalsData.length > 0 ? `, ${missingTerminalsData.length} new terminals to create` : '');
    summaryDiv.style.display = 'block';

    // Close modal
    $('#terminalPreviewModal').modal('hide');

    // Clear file input
    document.getElementById('terminal_file').value = '';
    document.getElementById('previewTerminalsBtn').disabled = true;
}

function selectAllFound(checkbox) {
    document.querySelectorAll('.terminal-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

function selectAllMissing(checkbox) {
    document.querySelectorAll('.missing-terminal-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

@if(isset($project) && $project->exists)
function viewProjectTerminals() {
    // Fetch and show current terminals
    fetch('{{ route("projects.terminals.list", $project) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tableBody = document.getElementById('currentTerminalsTable');
                tableBody.innerHTML = '';

                data.terminals.forEach(terminal => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${terminal.terminal_id}</td>
                            <td>${terminal.merchant_name}</td>
                            <td>${terminal.city || '-'}</td>
                            <td>${terminal.region || '-'}</td>
                            <td><span class="badge ${terminal.status === 'active' ? 'badge-green' : 'badge-gray'}">${terminal.status}</span></td>
                            <td>${terminal.included_at || '-'}</td>
                            <td>
                                <button type="button" class="action-btn action-delete" title="Remove" onclick="removeTerminalFromProject(${terminal.id})"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
                            </td>
                        </tr>
                    `;
                });

                document.getElementById('terminalListCount').textContent = data.count;
                $('#terminalListModal').modal('show');
            }
        })
        .catch(error => {
            console.error('Failed to load terminals:', error);
            alert('Failed to load terminal list');
        });
}

function removeTerminalFromProject(terminalId) {
    if (!confirm('Remove this terminal from the project?')) return;

    fetch(`{{ url('projects/' . $project->id . '/terminals') }}/${terminalId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            viewProjectTerminals(); // Refresh the list
        } else {
            alert('Failed to remove terminal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove terminal');
    });
}
@endif
</script>
