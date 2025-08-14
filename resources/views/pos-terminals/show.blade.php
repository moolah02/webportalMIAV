@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-block-end: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px;">Terminal {{ $posTerminal->terminal_id }}</h2>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">{{ $posTerminal->merchant_name }} • {{ $posTerminal->client->company_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('pos-terminals.edit', $posTerminal) }}" class="btn btn-primary">Edit Terminal</a>
            <button onclick="confirmDelete()" class="btn btn-danger">Delete Terminal</button>
            <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to List</a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        
        <!-- Left Column - Main Information -->
        <div>
            <!-- Terminal Information -->
            <div class="info-card">
                <h3>🖥️ Terminal Information</h3>
                <div class="info-grid">
                    <div>
                        <label>Terminal ID</label>
                        <div class="info-value">{{ $posTerminal->terminal_id }}</div>
                    </div>
                    <div>
                        <label>Status</label>
                        <div>
                            <span class="status-badge status-{{ $posTerminal->status }}">
                                {{ ucfirst($posTerminal->status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label>Model</label>
                        <div class="info-value">{{ $posTerminal->terminal_model ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Serial Number</label>
                        <div class="info-value">{{ $posTerminal->serial_number ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Installation Date</label>
                        <div class="info-value">{{ $posTerminal->installation_date ? $posTerminal->installation_date->format('M d, Y') : 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Last Service</label>
                        <div class="info-value">{{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}</div>
                    </div>
                </div>
            </div>

            <!-- Merchant Information -->
            <div class="info-card">
                <h3>🏪 Merchant Information</h3>
                <div class="info-grid">
                    <div>
                        <label>Business Name</label>
                        <div class="info-value">{{ $posTerminal->merchant_name }}</div>
                    </div>
                    <div>
                        <label>Contact Person</label>
                        <div class="info-value">{{ $posTerminal->merchant_contact_person ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Phone Number</label>
                        <div class="info-value">
                            @if($posTerminal->merchant_phone)
                                <a href="tel:{{ $posTerminal->merchant_phone }}" style="color: #007bff; text-decoration: none;">
                                    {{ $posTerminal->merchant_phone }}
                                </a>
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div>
                        <label>Email Address</label>
                        <div class="info-value">
                            @if($posTerminal->merchant_email)
                                <a href="mailto:{{ $posTerminal->merchant_email }}" style="color: #007bff; text-decoration: none;">
                                    {{ $posTerminal->merchant_email }}
                                </a>
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div>
                        <label>Business Type</label>
                        <div class="info-value">{{ $posTerminal->business_type ?: 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Region</label>
                        <div class="info-value">{{ $posTerminal->region ?: 'Not specified' }}</div>
                    </div>
                </div>
                
                @if($posTerminal->physical_address)
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <label>Physical Address</label>
                    <div class="info-value" style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-top: 5px;">
                        {{ $posTerminal->physical_address }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Client Information -->
            <div class="info-card">
                <h3>🏢 Client Information</h3>
                <div class="info-grid">
                    <div>
                        <label>Bank/Client</label>
                        <div class="info-value">
                            <a href="{{ route('clients.show', $posTerminal->client) }}" style="color: #007bff; text-decoration: none; font-weight: 500;">
                                {{ $posTerminal->client->company_name }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <label>Client Code</label>
                        <div class="info-value">{{ $posTerminal->client->client_code ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <label>Contact Person</label>
                        <div class="info-value">{{ $posTerminal->client->contact_person ?? 'Not specified' }}</div>
                    </div>
                    <div>
                        <label>Client Status</label>
                        <div>
                            <span class="status-badge status-{{ $posTerminal->client->status ?? 'active' }}">
                                {{ ucfirst($posTerminal->client->status ?? 'active') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($posTerminal->contract_details)
            <!-- Contract Details -->
            <div class="info-card">
                <h3>📄 Contract Details</h3>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 3px solid #007bff;">
                    {{ $posTerminal->contract_details }}
                </div>
            </div>
            @endif

            <!-- Service History -->
            <div class="info-card">
                <h3>📋 Service History</h3>
                <div id="service-history-list">
                    <div style="padding: 20px; text-align: center; color: #666;">
                        <p>No service records found</p>
                        <button onclick="openServiceModal()" class="btn btn-primary" style="margin-top: 10px;">Record First Service</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div class="info-card">
                <h3>⚡ Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <button onclick="updateStatus('active')" class="action-btn active-btn">
                        ✅ Mark as Active
                    </button>
                    <button onclick="updateStatus('maintenance')" class="action-btn maintenance-btn">
                        🔧 Mark for Maintenance
                    </button>
                    <button onclick="updateStatus('offline')" class="action-btn offline-btn">
                        ⚫ Mark as Offline
                    </button>
                    <button onclick="updateStatus('faulty')" class="action-btn faulty-btn">
                        ⚠️ Mark as Faulty
                    </button>
                    
                    <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">
                    
                    <button onclick="openTicketModal()" class="action-btn">🎫 Create Ticket</button>
                    <button onclick="openServiceModal()" class="action-btn">📅 Schedule Service</button>
                    <button onclick="generateReport()" class="action-btn">📊 Generate Report</button>
                    <button onclick="openNotesModal()" class="action-btn">📝 Add Notes</button>
                </div>
            </div>

            <!-- Service Information -->
            <div class="info-card">
                <h3>🛠️ Service Information</h3>
                <div style="margin-block-end: 15px;">
                    <label>Last Service</label>
                    <div class="info-value">
                        {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->format('M d, Y') : 'Never serviced' }}
                    </div>
                    @if($posTerminal->last_service_date)
                    <div style="font-size: 12px; color: #666; margin-top: 2px;">
                        {{ $posTerminal->last_service_date->diffForHumans() }}
                    </div>
                    @endif
                </div>
                
                <div style="margin-block-end: 15px;">
                    <label>Next Service Due</label>
                    <div class="info-value">
                        @if($posTerminal->next_service_due)
                            <span style="color: {{ $posTerminal->next_service_due <= now() ? '#dc3545' : ($posTerminal->next_service_due <= now()->addDays(7) ? '#ffc107' : '#333') }};">
                                {{ $posTerminal->next_service_due->format('M d, Y') }}
                            </span>
                            @if($posTerminal->next_service_due <= now())
                                <div style="font-size: 12px; color: #dc3545; font-weight: 600; margin-top: 2px;">⚠️ Overdue</div>
                            @elseif($posTerminal->next_service_due <= now()->addDays(7))
                                <div style="font-size: 12px; color: #ffc107; font-weight: 600; margin-top: 2px;">⏰ Due soon</div>
                            @endif
                        @else
                            Not scheduled
                        @endif
                    </div>
                </div>
                
                <button onclick="openServiceModal()" class="btn btn-primary" style="width: 100%;">
                    Schedule Service
                </button>
            </div>

            <!-- Statistics -->
            <div class="info-card">
                <h3>📊 Statistics</h3>
                <div class="stats-list">
                    <div class="stat-item">
                        <span>Total Jobs</span>
                        <span id="total-jobs">0</span>
                    </div>
                    <div class="stat-item">
                        <span>Service Reports</span>
                        <span id="service-reports">0</span>
                    </div>
                    <div class="stat-item">
                        <span>Open Tickets</span>
                        <span id="open-tickets">0</span>
                    </div>
                    <div class="stat-item">
                        <span>Days Since Last Service</span>
                        <span id="days-since-service">
                            {{ $posTerminal->last_service_date ? $posTerminal->last_service_date->diffInDays(now()) : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->

<!-- Create Ticket Modal -->
<div id="ticketModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🎫 Create Support Ticket</h3>
            <button onclick="closeModal('ticketModal')" class="close-btn">&times;</button>
        </div>
        <form id="ticketForm" onsubmit="submitTicket(event)">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" required class="form-control">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Issue Type</label>
                    <select name="issue_type" required class="form-control">
                        <option value="">Select Issue Type</option>
                        <option value="hardware">Hardware Issue</option>
                        <option value="software">Software Issue</option>
                        <option value="network">Network/Connectivity</option>
                        <option value="paper">Paper/Receipt Issues</option>
                        <option value="card_reader">Card Reader Problem</option>
                        <option value="display">Display Issues</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Issue Description</label>
                    <textarea name="description" rows="4" required class="form-control" placeholder="Describe the issue in detail..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Reported By</label>
                    <input type="text" name="reported_by" class="form-control" placeholder="Name of person reporting" value="{{ $posTerminal->merchant_contact_person }}">
                </div>
                
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" name="contact_number" class="form-control" placeholder="Contact number" value="{{ $posTerminal->merchant_phone }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('ticketModal')" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Ticket</button>
            </div>
        </form>
    </div>
</div>

<!-- Schedule Service Modal -->
<div id="serviceModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📅 Schedule Service</h3>
            <button onclick="closeModal('serviceModal')" class="close-btn">&times;</button>
        </div>
        <form id="serviceForm" onsubmit="submitService(event)">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Service Type</label>
                    <select name="service_type" required class="form-control">
                        <option value="">Select Service Type</option>
                        <option value="preventive">Preventive Maintenance</option>
                        <option value="corrective">Corrective Maintenance</option>
                        <option value="installation">Installation</option>
                        <option value="repair">Repair</option>
                        <option value="inspection">Inspection</option>
                        <option value="replacement">Replacement</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Scheduled Date</label>
                    <input type="date" name="scheduled_date" required class="form-control" min="{{ date('Y-m-d') }}">
                </div>
                
                <div class="form-group">
                    <label>Scheduled Time</label>
                    <input type="time" name="scheduled_time" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Assigned Technician</label>
                    <select name="technician_id" class="form-control">
                        <option value="">Select Technician</option>
                        <option value="1">John Doe</option>
                        <option value="2">Jane Smith</option>
                        <option value="3">Mike Johnson</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Service Notes</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Any special instructions or notes..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="update_next_service" checked>
                        Update next service due date
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('serviceModal')" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Schedule Service</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Notes Modal -->
<div id="notesModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📝 Add Notes</h3>
            <button onclick="closeModal('notesModal')" class="close-btn">&times;</button>
        </div>
        <form id="notesForm" onsubmit="submitNotes(event)">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Note Type</label>
                    <select name="note_type" required class="form-control">
                        <option value="general">General Note</option>
                        <option value="technical">Technical Note</option>
                        <option value="customer">Customer Feedback</option>
                        <option value="issue">Issue Report</option>
                        <option value="resolution">Resolution Note</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="5" required class="form-control" placeholder="Enter your notes here..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_important">
                        Mark as Important
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('notesModal')" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Notes</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Generation Modal -->
<div id="reportModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📊 Generate Report</h3>
            <button onclick="closeModal('reportModal')" class="close-btn">&times;</button>
        </div>
        <form id="reportForm" onsubmit="submitReport(event)">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Report Type</label>
                    <select name="report_type" required class="form-control" onchange="updateReportOptions(this.value)">
                        <option value="">Select Report Type</option>
                        <option value="service_history">Service History Report</option>
                        <option value="status_log">Status Change Log</option>
                        <option value="performance">Performance Report</option>
                        <option value="maintenance">Maintenance Schedule</option>
                        <option value="summary">Terminal Summary</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Date Range</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <input type="date" name="start_date" class="form-control" placeholder="Start Date">
                        <input type="date" name="end_date" class="form-control" placeholder="End Date">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Format</label>
                    <select name="format" required class="form-control">
                        <option value="pdf">PDF Document</option>
                        <option value="excel">Excel Spreadsheet</option>
                        <option value="csv">CSV File</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Include Options</label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label><input type="checkbox" name="include[]" value="charts" checked> Include Charts</label>
                        <label><input type="checkbox" name="include[]" value="details" checked> Include Detailed Information</label>
                        <label><input type="checkbox" name="include[]" value="notes"> Include Notes</label>
                        <label><input type="checkbox" name="include[]" value="tickets"> Include Ticket History</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('reportModal')" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden Forms -->
<form id="statusUpdateForm" action="{{ route('pos-terminals.update-status', $posTerminal) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

<form id="deleteForm" action="{{ route('pos-terminals.destroy', $posTerminal) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
/* Professional, clean styling */
.info-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 20px;
    margin-block-end: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.info-card h3 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.info-grid label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
    margin-block-end: 3px;
    display: block;
}

.info-value {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

.btn {
    display: inline-block;
    padding: 8px 16px;
    background: #f8f9fa;
    color: #333;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn:hover {
    background: #e9ecef;
    border-color: #007bff;
    text-decoration: none;
}

.btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
    border-color: #bd2130;
}

.action-btn {
    display: block;
    padding: 10px;
    background: white;
    color: #333;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-align: center;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    text-decoration: none;
}

.active-btn {
    background: #d4edda !important;
    color: #155724 !important;
    border-color: #c3e6cb !important;
}

.maintenance-btn {
    background: #d1ecf1 !important;
    color: #0c5460 !important;
    border-color: #bee5eb !important;
}

.offline-btn {
    background: #fff3cd !important;
    color: #856404 !important;
    border-color: #ffeaa7 !important;
}

.faulty-btn {
    background: #f8d7da !important;
    color: #721c24 !important;
    border-color: #f5c6cb !important;
}

.status-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-offline {
    background: #fff3cd;
    color: #856404;
}

.status-maintenance {
    background: #d1ecf1;
    color: #0c5460;
}

.status-faulty {
    background: #f8d7da;
    color: #721c24;
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 13px;
}

.stat-item span:last-child {
    font-weight: 600;
    color: #007bff;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
}

.close-btn:hover {
    color: #333;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-size: 14px;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
}

/* Toast Notification */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 6px;
    padding: 15px 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 300px;
    z-index: 2000;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.success {
    border-left: 4px solid #28a745;
}

.toast.error {
    border-left: 4px solid #dc3545;
}

.toast.info {
    border-left: 4px solid #17a2b8;
}

/* Responsive */
@media (max-width: 768px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    .info-grid {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="display: flex; justify-content: space-between"] {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .modal-content {
        width: 95%;
        margin: 10px;
    }
}
</style>

<script>
// Status Update
function updateStatus(status) {
    if (confirm(`Are you sure you want to update the terminal status to ${status}?`)) {
        document.getElementById('statusInput').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}

// Delete Confirmation
function confirmDelete() {
    if (confirm('Are you sure you want to delete this terminal? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Modal Functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openTicketModal() {
    openModal('ticketModal');
}

function openServiceModal() {
    openModal('serviceModal');
}

function openNotesModal() {
    openModal('notesModal');
}

function generateReport() {
    openModal('reportModal');
}

// Form Submissions
function submitTicket(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Simulate API call
    showToast('Ticket created successfully!', 'success');
    closeModal('ticketModal');
    
    // Update statistics
    const ticketCount = document.getElementById('open-tickets');
    ticketCount.textContent = parseInt(ticketCount.textContent) + 1;
    
    // Reset form
    event.target.reset();
}

function submitService(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Simulate API call
    showToast('Service scheduled successfully!', 'success');
    closeModal('serviceModal');
    
    // Update statistics
    const jobCount = document.getElementById('total-jobs');
    jobCount.textContent = parseInt(jobCount.textContent) + 1;
    
    // Reset form
    event.target.reset();
}

function submitNotes(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Simulate API call
    showToast('Notes added successfully!', 'success');
    closeModal('notesModal');
    
    // Reset form
    event.target.reset();
}

function submitReport(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const reportType = formData.get('report_type');
    const format = formData.get('format');
    
    // Simulate report generation
    showToast(`Generating ${reportType} report in ${format} format...`, 'info');
    
    setTimeout(() => {
        showToast('Report generated successfully! Download will start shortly.', 'success');
        closeModal('reportModal');
        
        // Update statistics
        const reportCount = document.getElementById('service-reports');
        reportCount.textContent = parseInt(reportCount.textContent) + 1;
        
        // Simulate download
        const link = document.createElement('a');
        link.href = '#';
        link.download = `terminal_${reportType}_{{ date('Y-m-d') }}.${format}`;
        link.click();
    }, 2000);
    
    // Reset form
    event.target.reset();
}

// Toast Notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = {
        success: '✅',
        error: '❌',
        info: 'ℹ️'
    }[type] || 'ℹ️';
    
    toast.innerHTML = `
        <span style="font-size: 20px;">${icon}</span>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Update report options based on type
function updateReportOptions(reportType) {
    const includeOptions = document.querySelectorAll('input[name="include[]"]');
    
    // Enable/disable options based on report type
    if (reportType === 'summary') {
        includeOptions.forEach(option => option.checked = true);
    } else if (reportType === 'status_log') {
        includeOptions.forEach(option => {
            option.checked = option.value === 'details';
        });
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// Close modal on outside click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});

// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    // Simulate loading statistics
    // In production, this would be an API call
    setTimeout(() => {
        // These would come from your backend
        document.getElementById('total-jobs').textContent = '12';
        document.getElementById('service-reports').textContent = '5';
        document.getElementById('open-tickets').textContent = '3';
    }, 500);
});
</script>
@endsection