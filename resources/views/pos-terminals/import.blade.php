<!-- Import Tab -->
    <div id="import-tab" class="tab-content">
        <div class="main-card">
            <h3 class="section-title">📤 Import Terminal Data</h3>
            <p class="section-description">Bulk import terminal data from bank or client CSV files with flexible column mapping</p>

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
                        <div class="mapping-actions">
                            <a href="{{ route('pos-terminals.column-mapping') }}" class="btn btn-outline btn-small" target="_blank">
                                Create New Mapping
                            </a>
                            <a href="{{ route('pos-terminals.column-mapping') }}" class="btn btn-outline btn-small" target="_blank">
                                Manage Mappings
                            </a>
                        </div>
                    </div>
                    <small class="help-text">Select a pre-configured mapping for this bank's specific CSV format, or use the default mapping</small>
                    <div id="mappingDescription" class="mapping-description" style="display: none;"></div>
                </div>

                <!-- File Upload -->
                <div class="file-upload-area">
                    <div class="upload-icon">📁</div>
                    <h4>Upload Your CSV File</h4>
                    <p>Select your terminal data CSV file with bank information</p>
                    
                    <input type="file" 
                           name="file" 
                           id="csvFile"
                           accept=".csv" 
                           required
                           class="file-input">
                    
                    @error('file')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    
                    <div id="fileName" class="file-name"></div>
                    <div class="file-info">
                        Supported formats: CSV only • Max size: 10MB<br>
                        <span class="file-format-note">💡 Excel files: Save as CSV (Comma delimited) before uploading</span>
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
                    <small class="help-text">Upload a file to enable preview</small>
                </div>

                <!-- Data Preview -->
                <div id="dataPreview" class="data-preview" style="display: none;">
                    <h4>📊 Data Preview</h4>
                    <div class="preview-content">
                        <div class="preview-stats">
                            <span class="stat">Mapping: <strong id="mappingName">Default</strong></span>
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
                            <strong>CSV Format</strong>
                            <p>Ensure your file is saved as CSV (Comma delimited) format</p>
                        </div>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">🎯</div>
                        <div class="tip-content">
                            <strong>Required Fields</strong>
                            <p>Terminal ID and Merchant Name are required for each row</p>
                        </div>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">🔄</div>
                        <div class="tip-content">
                            <strong>Dynamic Updates</strong>
                            <p>Technicians can update imported terminals via mobile app</p>
                        </div>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">⚙️</div>
                        <div class="tip-content">
                            <strong>Column Mapping</strong>
                            <p>Create custom mappings for different bank CSV formats</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
.mapping-container {
    display: flex;
    gap: 12px;
    align-items: flex-end;
}

.mapping-container .form-select {
    flex: 1;
}

.mapping-actions {
    display: flex;
    gap: 8px;
}

.btn-small {
    padding: 8px 16px;
    font-size: 12px;
    min-width: auto;
}

.help-text {
    color: #666;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.mapping-description {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f8f9ff;
    border: 1px solid #e3f2fd;
    border-radius: 4px;
    font-size: 12px;
    color: #666;
}

.file-format-note {
    color: #007bff;
    font-weight: 500;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    padding: 12px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.checkbox-label:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
    width: 16px;
    height: 16px;
    accent-color: #007bff;
}

.checkbox-label small {
    color: #666;
    font-size: 12px;
    margin-top: 2px;
    display: block;
}

.data-preview {
    margin-top: 24px;
    padding: 20px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}

.data-preview h4 {
    margin: 0 0 16px 0;
    color: #333;
}

.preview-stats {
    display: flex;
    gap: 20px;
    margin-block-end: 16px;
    flex-wrap: wrap;
}

.preview-stats .stat {
    padding: 6px 12px;
    background: white;
    border-radius: 4px;
    font-size: 12px;
}

.preview-table-container {
    max-height: 300px;
    overflow: auto;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    margin-block-end: 16px;
}

.preview-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.preview-table th,
.preview-table td {
    padding: 8px;
    border: 1px solid #dee2e6;
    text-align: left;
}

.preview-table th {
    background: #f8f9fa;
    font-weight: 600;
    position: sticky;
    top: 0;
}

.preview-actions {
    text-align: right;
}

.import-tips {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #dee2e6;
}

.import-tips h4 {
    margin: 0 0 20px 0;
    color: #333;
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.tip-card {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f8f9ff;
    border: 1px solid #e3f2fd;
    border-radius: 8px;
}

.tip-icon {
    font-size: 20px;
    flex-shrink: 0;
}

.tip-content strong {
    display: block;
    margin-block-end: 4px;
    color: #333;
}

.tip-content p {
    margin: 0;
    font-size: 12px;
    color: #666;
    line-height: 1.4;
}

.btn-icon {
    margin-right: 6px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .mapping-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .mapping-actions {
        justify-content: stretch;
    }
    
    .mapping-actions .btn {
        flex: 1;
    }
    
    .tips-grid {
        grid-template-columns: 1fr;
    }
    
    .preview-stats {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<script>
// Enhanced file upload feedback and preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const previewBtn = document.getElementById('previewBtn');
    const dataPreview = document.getElementById('dataPreview');
    const mappingSelect = document.getElementById('mapping_id');
    const mappingDescription = document.getElementById('mappingDescription');
    
    // File input change handler
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
                previewBtn.disabled = false;
                previewBtn.querySelector('.help-text').textContent = 'Click to preview how your data will be imported';
            } else {
                fileName.textContent = '';
                previewBtn.disabled = true;
                previewBtn.querySelector('.help-text').textContent = 'Upload a file to enable preview';
                dataPreview.style.display = 'none';
            }
        });
    }
    
    // Mapping selection change handler
    if (mappingSelect && mappingDescription) {
        mappingSelect.addEventListener('change', function(e) {
            const selectedOption = e.target.selectedOptions[0];
            const description = selectedOption.getAttribute('data-description');
            
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
            
            if (!file) {
                alert('Please select a CSV file first.');
                return;
            }
            
            previewBtn.innerHTML = '<span class="btn-icon">⏳</span> Loading Preview...';
            previewBtn.disabled = true;
            
            // Create FormData for preview request
            const formData = new FormData();
            formData.append('file', file);
            formData.append('mapping_id', mappingId);
            formData.append('preview_rows', 5);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Send preview request
            fetch('{{ route("pos-terminals.preview-import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPreview(data);
                } else {
                    alert('Preview failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Preview error:', error);
                alert('Preview failed. Please check your file format.');
            })
            .finally(() => {
                previewBtn.innerHTML = '<span class="btn-icon">👁️</span> Preview Import Data';
                previewBtn.disabled = false;
            });
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
            
            submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Processing...';
            submitBtn.disabled = true;
        });
    }
});

function displayPreview(data) {
    const mappingName = document.getElementById('mappingName');
    const rowCount = document.getElementById('rowCount');
    const columnCount = document.getElementById('columnCount');
    const previewTable = document.getElementById('previewTable');
    const dataPreview = document.getElementById('dataPreview');
    
    // Update stats
    mappingName.textContent = data.mapping_name;
    rowCount.textContent = data.preview_data.length;
    columnCount.textContent = data.headers.length;
    
    // Build table
    let tableHTML = '<thead><tr>';
    
    // Add headers
    const displayHeaders = ['Row', 'Terminal ID', 'Merchant Name', 'City', 'Region', 'Status', 'Mapped Data'];
    displayHeaders.forEach(header => {
        tableHTML += `<th>${header}</th>`;
    });
    tableHTML += '</tr></thead><tbody>';
    
    // Add preview rows
    data.preview_data.forEach(row => {
        tableHTML += '<tr>';
        tableHTML += `<td>${row.row_number}</td>`;
        tableHTML += `<td>${row.mapped_data.terminal_id || 'N/A'}</td>`;
        tableHTML += `<td>${row.mapped_data.merchant_name || 'N/A'}</td>`;
        tableHTML += `<td>${row.mapped_data.city || 'N/A'}</td>`;
        tableHTML += `<td>${row.mapped_data.region || 'N/A'}</td>`;
        tableHTML += `<td><span class="status-badge status-${row.mapped_data.status || 'active'}">${row.mapped_data.status || 'active'}</span></td>`;
        tableHTML += `<td><small>✅ ${Object.keys(row.mapped_data).length} fields mapped</small></td>`;
        tableHTML += '</tr>';
    });
    
    tableHTML += '</tbody>';
    previewTable.innerHTML = tableHTML;
    dataPreview.style.display = 'block';
    
    // Scroll to preview
    dataPreview.scrollIntoView({ behavior: 'smooth' });
}

function closePreview() {
    document.getElementById('dataPreview').style.display = 'none';
}

function resetForm() {
    document.querySelector('.import-form').reset();
    document.getElementById('fileName').textContent = '';
    document.getElementById('dataPreview').style.display = 'none';
    document.getElementById('previewBtn').disabled = true;
    document.getElementById('mappingDescription').style.display = 'none';
}
</script>