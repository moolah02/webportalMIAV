{{-- Terminal Upload Section for Project Create/Edit --}}
<div class="form-section" id="terminal-upload-section">
    <div class="form-section-title">
        <i class="fas fa-upload"></i>
        Terminal Assignment
        @if(isset($project) && $project->exists)
            <span class="badge bg-info ms-2">
                {{ $project->projectTerminals()->where('is_active', true)->count() }} assigned
            </span>
        @endif
    </div>

    @if(isset($project) && $project->exists)
        {{-- Edit Mode: Show existing terminals summary --}}
        <div class="alert alert-light border mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-desktop text-primary me-2"></i>
                    <strong>{{ $project->projectTerminals()->where('is_active', true)->count() }}</strong> terminals currently assigned to this project
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewProjectTerminals()">
                    <i class="fas fa-list me-1"></i> View List
                </button>
            </div>
        </div>
    @endif

    {{-- Upload Area --}}
    <div class="upload-area p-3 border rounded bg-light mb-3">
        <div class="row align-items-end">
            <div class="col-md-7">
                <label class="form-label fw-semibold">
                    <i class="fas fa-file-csv text-success me-1"></i>
                    Upload Terminal List (CSV/Excel)
                </label>
                <div class="input-group">
                    <input type="file"
                           id="terminal_file"
                           class="form-control"
                           accept=".csv,.xlsx,.xls,.txt"
                           onchange="handleTerminalFileSelect(this)">
                    <button type="button"
                            id="previewTerminalsBtn"
                            class="btn btn-outline-info"
                            onclick="previewTerminalUpload()"
                            disabled>
                        <i class="fas fa-eye me-1"></i> Preview
                    </button>
                </div>
                <small class="text-muted d-block mt-1">
                    Upload CSV/Excel with <code>terminal_id</code> column.
                    <a href="{{ route('projects.terminals.download-template') }}" class="text-decoration-none">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </small>
            </div>
            <div class="col-md-5">
                <div class="bg-white p-2 rounded border">
                    <label class="form-label small fw-semibold mb-2">Options:</label>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="skip_duplicates" class="form-check-input" checked>
                        <label class="form-check-label small" for="skip_duplicates">Skip already assigned</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="create_missing" class="form-check-input">
                        <label class="form-check-label small" for="create_missing">Create missing terminals</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload Progress --}}
    <div id="uploadProgress" class="mb-3" style="display: none;">
        <div class="progress" style="height: 25px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated"
                 role="progressbar"
                 style="width: 0%"
                 id="uploadProgressBar">
                <span id="uploadProgressText">Uploading...</span>
            </div>
        </div>
    </div>

    {{-- Upload Results Summary (shown after preview confirmation) --}}
    <div id="terminalUploadSummary" style="display: none;">
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <span id="terminalUploadSummaryText"></span>
        </div>
    </div>

    {{-- Hidden fields to store terminal data for form submission --}}
    <input type="hidden" name="uploaded_terminal_ids" id="uploaded_terminal_ids" value="">
    <input type="hidden" name="missing_terminals_data" id="missing_terminals_data" value="">
    <input type="hidden" name="terminal_inclusion_reason" id="terminal_inclusion_reason" value="Bulk Upload">
</div>

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
                    <td><span class="badge bg-${terminal.status === 'active' ? 'success' : 'secondary'}">${terminal.status || '-'}</span></td>
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
                            '<span class="badge bg-warning">Can create</span>' :
                            '<span class="badge bg-secondary">No data</span>'}
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
    const modal = new bootstrap.Modal(document.getElementById('terminalPreviewModal'));
    modal.show();
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
    bootstrap.Modal.getInstance(document.getElementById('terminalPreviewModal')).hide();

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
                            <td><span class="badge bg-${terminal.status === 'active' ? 'success' : 'secondary'}">${terminal.status}</span></td>
                            <td>${terminal.included_at || '-'}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTerminalFromProject(${terminal.id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                document.getElementById('terminalListCount').textContent = data.count;
                const modal = new bootstrap.Modal(document.getElementById('terminalListModal'));
                modal.show();
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
