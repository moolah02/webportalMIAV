{{--
==============================================
POS TERMINAL IMPORT FORM
File: resources/views/pos-terminals/import.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Import POS Terminals</h3>
            <p style="color: #666; margin: 5px 0 0 0;">Bulk import terminal data from bank/client CSV or Excel files</p>
        </div>
        <a href="{{ route('pos-terminals.index') }}" class="btn">← Back to Terminals</a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Import Form -->
        <div class="content-card">
            <h4 style="margin-bottom: 20px; color: #333;">📤 Upload Terminal Data</h4>
            
            <form action="{{ route('pos-terminals.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Client Selection -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Select Client/Bank *</label>
                    <select name="client_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        <option value="">Choose the client for these terminals...</option>
                        @foreach($clients ?? [] as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }} ({{ $client->client_code }})</option>
                        @endforeach
                    </select>
                    <small style="color: #666;">All terminals in the file will be assigned to this client</small>
                </div>

                <!-- File Upload Area -->
                <div style="border: 3px dashed #ddd; border-radius: 8px; padding: 40px; text-align: center; background: #fafafa; margin-bottom: 25px; transition: all 0.2s;" 
                     onDragOver="this.style.borderColor='#2196f3'; this.style.background='#f5f9ff'" 
                     onDragLeave="this.style.borderColor='#ddd'; this.style.background='#fafafa'">
                    <div style="font-size: 48px; margin-bottom: 15px;">📁</div>
                    <h4 style="margin-bottom: 10px;">Drag & Drop Your File Here</h4>
                    <p style="color: #666; margin-bottom: 15px;">or click to browse files</p>
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" required 
                           style="margin-bottom: 10px; padding: 8px;">
                    <div style="font-size: 12px; color: #666;">
                        Supported formats: CSV, Excel (.xlsx, .xls) • Max size: 10MB
                    </div>
                </div>

                <!-- Import Options -->
                <div style="margin-bottom: 25px;">
                    <h5 style="margin-bottom: 10px;">Import Options</h5>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="options[]" value="skip_duplicates" checked>
                            Skip duplicate terminal IDs
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="options[]" value="update_existing">
                            Update existing terminals with new data
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="options[]" value="send_notifications">
                            Send import summary email
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="{{ route('pos-terminals.index') }}" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-primary">Process Import</button>
                </div>
            </form>
        </div>

        <!-- Import Guide -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Expected Format -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">📋 Expected Format</h4>
                <div style="font-size: 14px;">
                    <div style="margin-bottom: 15px;">
                        <strong>Required Columns:</strong>
                        <ul style="margin-top: 8px; padding-left: 20px; color: #666;">
                            <li>terminal_id</li>
                            <li>merchant_name</li>
                            <li>merchant_contact_person</li>
                            <li>merchant_phone</li>
                            <li>physical_address</li>
                            <li>region</li>
                        </ul>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <strong>Optional Columns:</strong>
                        <ul style="margin-top: 8px; padding-left: 20px; color: #666;">
                            <li>merchant_email</li>
                            <li>area</li>
                            <li>business_type</li>
                            <li>terminal_model</li>
                            <li>serial_number</li>
                            <li>installation_date</li>
                        </ul>
                    </div>
                </div>
                
                <a href="#" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 10px;">
                    📥 Download Template
                </a>
            </div>

            <!-- Import History -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">📊 Recent Imports</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #4caf50;">
                        <div style="font-weight: 500; font-size: 14px;">ABC Bank - Q3 Terminals</div>
                        <div style="font-size: 12px; color: #666; margin-top: 2px;">
                            45 terminals • July 20, 2024 • Success
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #4caf50;">
                        <div style="font-weight: 500; font-size: 14px;">First National - New Branches</div>
                        <div style="font-size: 12px; color: #666; margin-top: 2px;">
                            12 terminals • July 18, 2024 • Success
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #ff9800;">
                        <div style="font-weight: 500; font-size: 14px;">City Bank - Update</div>
                        <div style="font-size: 12px; color: #666; margin-top: 2px;">
                            8 terminals • July 15, 2024 • 2 errors
                        </div>
                    </div>
                </div>
                
                <a href="#" style="font-size: 12px; color: #2196f3; text-decoration: none; margin-top: 10px; display: block;">
                    View all import history →
                </a>
            </div>

            <!-- Help & Support -->
            <div class="content-card">
                <h4 style="margin-bottom: 15px; color: #333;">❓ Need Help?</h4>
                <div style="font-size: 14px; color: #666; line-height: 1.5;">
                    <p style="margin-bottom: 10px;">
                        Having trouble with your import? Check our common issues:
                    </p>
                    <ul style="padding-left: 20px; margin-bottom: 15px;">
                        <li>Ensure column headers match exactly</li>
                        <li>Check for duplicate terminal IDs</li>
                        <li>Verify date formats (YYYY-MM-DD)</li>
                        <li>Remove empty rows</li>
                    </ul>
                    <a href="#" style="color: #2196f3; text-decoration: none; font-size: 12px;">
                        📖 View full import guide
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-card ul {
    margin: 0;
    padding: 0;
}

.content-card li {
    margin-bottom: 4px;
}
</style>
@endsection