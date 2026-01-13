@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload mr-2"></i>
                        Smart POS Terminal Import
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('pos-terminals.download-template') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download mr-1"></i>
                            Download Template
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- FLASH & VALIDATION MESSAGES --}}
                    @if (session('success'))
                      <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                      <div class="alert alert-danger" style="white-space: pre-line;">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                      <div class="alert alert-danger">
                        <ul class="mb-0">
                          @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif

                    <!-- System Template Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-table mr-2"></i>
                                        Expected Template Columns
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Required Fields:</h6>
                                            <ul class="list-unstyled mb-2">
                                                <li><span class="badge badge-danger mr-1">*</span>Terminal ID</li>
                                                <li><span class="badge badge-danger mr-1">*</span>Merchant Name</li>
                                            </ul>

                                            <h6 class="text-success">Optional Fields:</h6>
                                            <ul class="list-unstyled small">
                                                <li>• Merchant ID</li>
                                                <li>• Legal Name</li>
                                                <li>• Contact Person</li>
                                                <li>• Phone Number</li>
                                                <li>• Email Address</li>
                                                <li>• Physical Address</li>
                                                <li>• City, Province, Region</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-info">Technical Fields:</h6>
                                            <ul class="list-unstyled small">
                                                <li>• Business Type</li>
                                                <li>• Terminal Model</li>
                                                <li>• Serial Number</li>
                                                <li>• Installation Date</li>
                                                <li>• Status/Condition</li>
                                                <li>• Issues/Comments</li>
                                                <li>• Corrective Actions</li>
                                            </ul>

                                            <div class="alert alert-info alert-sm mt-2 p-2">
                                                <small>
                                                    <i class="fas fa-magic mr-1"></i>
                                                    <strong>Smart Detection:</strong> Column names are automatically detected regardless of order!
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Import Specifications
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-primary">Supported Formats:</h6>
                                            <div class="mb-2">
                                                <span class="badge badge-primary mr-1">CSV</span>
                                                <span class="badge badge-primary mr-1">XLSX</span>
                                                <span class="badge badge-primary mr-1">XLS</span>
                                                <span class="badge badge-primary">TXT</span>
                                            </div>

                                            <h6 class="text-success">File Size Limits:</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>Maximum:</strong> 40MB+ (Large file support)</li>
                                                <li><strong>Recommended:</strong> Up to 30MB for optimal speed</li>
                                                <li><strong>Processing:</strong> Chunked for large files</li>
                                            </ul>

                                            <h6 class="text-info">Smart Features:</h6>
                                            <ul class="list-unstyled small">
                                                <li>✓ Auto-detects column headers</li>
                                                <li>✓ Processes any column order</li>
                                                <li>✓ Stores extra columns as metadata</li>
                                                <li>✓ Memory-optimized for large files</li>
                                                <li>✓ Real-time preview and validation</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Import Form -->
                    <form id="importForm" action="{{ route('pos-terminals.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id" class="required">Client</label>
                                    <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
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
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mapping_id">Column Mapping (Optional)</label>
                                    <select name="mapping_id" id="mapping_id" class="form-control">
                                        <option value="">Use Smart Auto-Detection</option>
                                        @foreach($mappings as $mapping)
                                            <option value="{{ $mapping->id }}" {{ old('mapping_id') == $mapping->id ? 'selected' : '' }}>
                                                {{ $mapping->mapping_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Leave blank for automatic header detection
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="file" class="required">Import File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file"
                                           name="file"
                                           id="file"
                                           class="custom-file-input @error('file') is-invalid @enderror"
                                           accept=".csv,.xlsx,.xls,.txt"
                                           required>
                                    <label class="custom-file-label" for="file">Choose Excel, CSV, or TXT file...</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="button" id="previewBtn" class="btn btn-outline-info" disabled>
                                        <i class="fas fa-eye mr-1"></i>
                                        Preview
                                    </button>
                                </div>
                            </div>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Supported: CSV, XLSX, XLS, TXT files up to 40MB. Large files are processed in chunks automatically.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Import Options</label>
                            <div class="form-check">
                                <input type="checkbox" name="options[]" value="skip_duplicates" id="skip_duplicates" class="form-check-input" {{ in_array('skip_duplicates', old('options', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="skip_duplicates">
                                    Skip Duplicate Terminal IDs
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="options[]" value="update_existing" id="update_existing" class="form-check-input" {{ in_array('update_existing', old('options', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="update_existing">
                                    Update Existing Records
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                If neither option is selected, duplicate terminal IDs will cause import errors.
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="importBtn" disabled>
                                <i class="fas fa-upload mr-1"></i>
                                Start Smart Import
                            </button>
                            <a href="{{ route('pos-terminals.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    File Preview & Column Mapping
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading preview...</span>
                        </div>
                        <p class="mt-2">Analyzing your file...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close Preview</button>
                <button type="button" class="btn btn-success" id="proceedImport">
                    <i class="fas fa-check mr-1"></i>
                    Looks Good - Proceed with Import
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Upload Progress (shown during preview upload) -->
<div id="upload-progress-wrap" style="display:none; padding:16px 20px; background:#fff; border-top:1px solid #eee;">
  <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
    <small id="upload-progress-label" style="color:#6c757d">Uploading…</small>
    <small id="upload-progress-percent" style="color:#6c757d">0%</small>
  </div>
  <div style="width:100%; height:10px; background:#e9ecef; border-radius:6px; overflow:hidden;">
    <div id="upload-progress-bar" style="width:0%; height:100%; background:linear-gradient(90deg,#007bff,#28a745)"></div>
  </div>
  <small id="upload-speed" style="color:#6c757d; display:block; margin-top:6px;"></small>
  <button id="cancel-upload-btn" type="button" style="margin-top:10px; padding:6px 10px; background:#dc3545; color:#fff; border:none; border-radius:4px; font-size:12px; cursor:pointer; display:none;">
    Cancel upload
  </button>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Processing...</span>
                </div>
                <h5>Processing Your Import</h5>
                <p class="mb-0">Large files are processed in chunks. This may take a few minutes...</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
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
            <div class="text-center" id="uploadProgressContainer">
                <h5 class="mb-3">Uploading File...</h5>
                <p class="text-muted">File size: ${fileSizeMB} MB</p>
                <div class="progress mb-3" style="height: 30px;">
                    <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%">
                        <span id="uploadPercentText">0%</span>
                    </div>
                </div>
                <p id="uploadSpeedText" class="text-muted small"></p>
                <button type="button" id="cancelUploadBtn" class="btn btn-sm btn-danger mt-2">
                    <i class="fas fa-times mr-1"></i> Cancel Upload
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
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Processing...</span>
                        </div>
                        <h5>Analyzing File...</h5>
                        <p class="text-muted">Processing ${fileSizeMB} MB file. This may take a moment...</p>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                 role="progressbar" style="width: 100%">
                                Analyzing columns and validating data...
                            </div>
                        </div>
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
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-circle mr-2"></i>Preview Error</h5>
                                <p>${response.message}</p>
                            </div>
                        `);
                    }
                } catch (e) {
                    $('#previewContent').html(`
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-circle mr-2"></i>Processing Error</h5>
                            <p>Failed to parse server response. Please try again.</p>
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
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-circle mr-2"></i>Preview Failed</h5>
                        <p>${message}</p>
                    </div>
                `);
            }
        });

        // Handle errors
        xhr.addEventListener('error', function() {
            if (!uploadCancelled) {
                $('#previewContent').html(`
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-circle mr-2"></i>Upload Failed</h5>
                        <p>Network error occurred. Please check your connection and try again.</p>
                    </div>
                `);
            }
        });

        // Handle timeout
        xhr.addEventListener('timeout', function() {
            $('#previewContent').html(`
                <div class="alert alert-warning">
                    <h5><i class="fas fa-clock mr-2"></i>Upload Timeout</h5>
                    <p>The upload took too long. Please try a smaller file or contact support.</p>
                </div>
            `);
        });

        // Cancel button handler
        $(document).off('click', '#cancelUploadBtn').on('click', '#cancelUploadBtn', function() {
            uploadCancelled = true;
            xhr.abort();
            $('#previewContent').html(`
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>Upload Cancelled</h5>
                    <p>The upload was cancelled by user.</p>
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
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Processing...</span>
                </div>
                <h5 id="importStatusTitle">Uploading...</h5>
                <p id="importStatusText" class="mb-3">Uploading ${fileSizeMB} MB file...</p>
                <div class="progress" style="height: 30px;">
                    <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%">
                        <span id="importPercentText">0%</span>
                    </div>
                </div>
                <p id="importSpeedText" class="text-muted small mt-2"></p>
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
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle mr-1"></i> File Analysis</h6>
                        <ul class="mb-0">
                            <li><strong>Mapping:</strong> ${response.mapping_name}</li>
                            <li><strong>Columns Found:</strong> ${response.headers.length}</li>
                            <li><strong>Preview Rows:</strong> ${response.preview_data.length}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle mr-1"></i> Column Mapping</h6>
                        <ul class="mb-0">
                            <li><strong>Mapped Fields:</strong> ${response.column_mapping_info.mapped_fields.length}</li>
                            <li><strong>Extra Fields:</strong> ${response.column_mapping_info.extra_fields.length}</li>
                            ${
                                response.column_mapping_info.missing_required.length > 0
                                ? `<li class="text-danger"><strong>Missing Required:</strong> ${response.column_mapping_info.missing_required.join(', ')}</li>`
                                : '<li class="text-success"><strong>All Required Fields Found!</strong></li>'
                            }
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Detected Columns in Your File:</h6>
                    <div class="table-responsive" style="max-height: 200px;">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
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
                    <td><code>${header}</code></td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Preview Data (First ${response.preview_data.length} rows):</h6>
                    <div class="table-responsive" style="max-height: 200px;">
                        <table class="table table-sm table-striped">
                            <thead class="bg-primary text-white">
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
                ? '<span class="badge badge-success">Valid</span>'
                : '<span class="badge badge-danger">Error</span>';

            html += `
                <tr>
                    <td>${row.row_number}</td>
                    <td><code>${row.mapped_data?.terminal_id || 'N/A'}</code></td>
                    <td>${row.mapped_data?.merchant_name || 'N/A'}</td>
                    <td>${row.mapped_data?.status || 'N/A'}</td>
                    <td>
                        ${statusBadge}
                        ${row.validation_status !== 'valid' ? `<br><small class="text-danger">${row.validation_message}</small>` : ''}
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
                <div class="alert alert-info">
                    <h6><i class="fas fa-plus-circle mr-1"></i> Extra Fields Found</h6>
                    <p>These columns will be stored as additional metadata:</p>
                    <p><strong>${response.column_mapping_info.extra_fields.join(', ')}</strong></p>
                </div>
            `;
        }

        $('#previewContent').html(html);
    }
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.bg-light .card-header {
    background: #f8f9fa !important;
    color: #333 !important;
}

.table th {
    font-size: 0.85rem;
    font-weight: 600;
}

.table td {
    font-size: 0.85rem;
}

.progress {
    height: 8px;
}

.spinner-border {
    width: 1.5rem;
    height: 1.5rem;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

code {
    font-size: 0.8rem;
    padding: 0.2rem 0.4rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}
</style>
@endpush
