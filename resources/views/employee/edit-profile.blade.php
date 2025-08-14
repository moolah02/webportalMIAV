@extends('layouts.app')

@section('content')
<div class="profile-edit">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div>
                <h4>✏️ Edit Profile</h4>
                <p>Update your personal information and system preferences</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('employee.profile') }}" class="btn btn-secondary">
                    ← Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="error-notification">
            <h4>⚠️ Please fix the following errors:</h4>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-card">
            <div class="card-header">
                <h3>👤 Profile Information</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('employee.update-profile') }}">
                    @csrf
                    @method('PATCH')

                    <!-- Current Profile Summary -->
                    <div class="current-profile-summary">
                        <div class="profile-avatar">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="profile-details">
                            <h3>{{ $employee->full_name }}</h3>
                            <p>{{ $employee->employee_number }} • {{ $employee->department->name ?? 'No Department' }}</p>
                            <div class="profile-badges">
                                <span class="badge badge-{{ $employee->status === 'active' ? 'success' : 'inactive' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                                @if($employee->role)
                                    <span class="badge badge-blue">{{ $employee->role->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Editable Fields Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4>📝 Personal Information</h4>
                            <p>Update your basic personal details</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name" class="form-label required">First Name</label>
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name" 
                                       class="form-input @error('first_name') error @enderror" 
                                       value="{{ old('first_name', $employee->first_name) }}" 
                                       required>
                                @error('first_name')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="last_name" class="form-label required">Last Name</label>
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name" 
                                       class="form-input @error('last_name') error @enderror" 
                                       value="{{ old('last_name', $employee->last_name) }}" 
                                       required>
                                @error('last_name')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       id="email" 
                                       class="form-input" 
                                       value="{{ $employee->email }}" 
                                       disabled>
                                <small class="form-help">Contact IT to change your email address</small>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       class="form-input @error('phone') error @enderror" 
                                       value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- System Preferences Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4>⚙️ System Preferences</h4>
                            <p>Configure your system settings and preferences</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="time_zone" class="form-label required">Time Zone</label>
                                <select name="time_zone" 
                                        id="time_zone" 
                                        class="form-select @error('time_zone') error @enderror" 
                                        required>
                                    <option value="">Select Time Zone</option>
                                    @php
                                        $timezones = [
                                            'UTC' => 'UTC (Coordinated Universal Time)',
                                            'America/New_York' => 'Eastern Time (US & Canada)',
                                            'America/Chicago' => 'Central Time (US & Canada)', 
                                            'America/Denver' => 'Mountain Time (US & Canada)',
                                            'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                            'Europe/London' => 'London, Edinburgh, Dublin',
                                            'Europe/Paris' => 'Paris, Berlin, Madrid',
                                            'Africa/Harare' => 'Harare, Zimbabwe',
                                            'Africa/Johannesburg' => 'Johannesburg, South Africa',
                                            'Africa/Cairo' => 'Cairo, Egypt',
                                            'Asia/Tokyo' => 'Tokyo, Osaka, Sapporo',
                                            'Asia/Shanghai' => 'Beijing, Shanghai',
                                            'Australia/Sydney' => 'Sydney, Melbourne',
                                        ];
                                    @endphp
                                    @foreach($timezones as $value => $label)
                                        <option value="{{ $value }}" 
                                                {{ old('time_zone', $employee->time_zone) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('time_zone')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="language" class="form-label required">Language</label>
                                <select name="language" 
                                        id="language" 
                                        class="form-select @error('language') error @enderror" 
                                        required>
                                    @php
                                        $languages = [
                                            'en' => 'English',
                                            'es' => 'Spanish',
                                            'fr' => 'French',
                                            'de' => 'German',
                                            'it' => 'Italian',
                                            'pt' => 'Portuguese',
                                            'zh' => 'Chinese',
                                            'ja' => 'Japanese',
                                            'ko' => 'Korean',
                                            'ar' => 'Arabic',
                                        ];
                                    @endphp
                                    @foreach($languages as $value => $label)
                                        <option value="{{ $value }}" 
                                                {{ old('language', $employee->language) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('language')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Read-Only Information Section -->
                    <div class="form-section readonly-section">
                        <div class="section-header">
                            <h4>📋 System Information</h4>
                            <p>These details are managed by your administrator</p>
                        </div>
                        
                        <div class="readonly-grid">
                            <div class="readonly-item">
                                <div class="readonly-label">Employee Number</div>
                                <div class="readonly-value">{{ $employee->employee_number }}</div>
                            </div>

                            <div class="readonly-item">
                                <div class="readonly-label">Hire Date</div>
                                <div class="readonly-value">
                                    {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not specified' }}
                                </div>
                            </div>

                            <div class="readonly-item">
                                <div class="readonly-label">Department</div>
                                <div class="readonly-value">
                                    {{ $employee->department->name ?? 'Not assigned' }}
                                    <small>Contact HR to change</small>
                                </div>
                            </div>

                            <div class="readonly-item">
                                <div class="readonly-label">Role</div>
                                <div class="readonly-value">
                                    {{ $employee->role->name ?? 'Not assigned' }}
                                    <small>Contact your manager to change</small>
                                </div>
                            </div>

                            <div class="readonly-item">
                                <div class="readonly-label">Status</div>
                                <div class="readonly-value">
                                    <span class="badge badge-{{ $employee->status === 'active' ? 'success' : 'inactive' }}">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="readonly-item">
                                <div class="readonly-label">Last Login</div>
                                <div class="readonly-value">
                                    {{ $employee->last_login_at ? $employee->last_login_at->diffForHumans() : 'Never' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            💾 Save Changes
                        </button>
                        <a href="{{ route('employee.profile') }}" class="btn btn-secondary">
                            ❌ Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Profile Edit Styles - Matching Deployment Planning */
.profile-edit {
    padding: 0;
    max-width: 100%;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Error Notification */
.error-notification {
    background: #fee2e2;
    color: #991b1b;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border: 1px solid #fecaca;
}

.error-notification h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
}

.error-notification ul {
    margin: 0;
    padding-left: 1.5rem;
}

.error-notification li {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

/* Page Header - Same as Profile */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-content h4 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.header-content p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

/* Main Content */
.main-content {
    max-width: 800px;
    margin: 0 auto;
}

/* Cards - Same as Deployment */
.content-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow: hidden;
}

.card-header {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.card-body {
    padding: 1.5rem;
}

/* Current Profile Summary */
.current-profile-summary {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.profile-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
}

.profile-details h3 {
    margin: 0 0 0.25rem 0;
    color: #111827;
    font-size: 1.25rem;
    font-weight: 700;
}

.profile-details p {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.profile-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Form Sections */
.form-section {
    margin-bottom: 2rem;
}

.section-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.section-header h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.section-header p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

/* Form Elements - Same as Deployment */
.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.form-label.required::after {
    content: ' *';
    color: #ef4444;
}

.form-input, .form-select {
    width: 100%;
    padding: 0.625rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
    background: white;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:disabled {
    background-color: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

.form-input.error, .form-select.error {
    border-color: #ef4444;
}

.form-error {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

.form-help {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
    display: block;
}

/* Read-Only Section */
.readonly-section {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.readonly-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.readonly-item {
    background: white;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.readonly-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.readonly-value {
    color: #111827;
    font-size: 0.875rem;
    font-weight: 500;
}

.readonly-value small {
    display: block;
    color: #6b7280;
    font-size: 0.75rem;
    font-weight: 400;
    margin-top: 0.25rem;
}

/* Badges - Same as Profile */
.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.625rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-blue {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-inactive {
    background: #f3f4f6;
    color: #6b7280;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    justify-content: flex-end;
}

/* Buttons - Same as Deployment */
.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-large {
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .current-profile-summary {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .readonly-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .main-content {
        margin: 0 1rem;
    }
}

@media (max-width: 480px) {
    .card-body {
        padding: 1rem;
    }
    
    .current-profile-summary {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1rem;
    }
}
</style>

<script>
// Auto-hide error notification
document.addEventListener('DOMContentLoaded', function() {
    const errorNotification = document.querySelector('.error-notification');
    if (errorNotification) {
        setTimeout(() => {
            errorNotification.style.opacity = '0';
            setTimeout(() => errorNotification.remove(), 300);
        }, 8000); // Hide after 8 seconds for errors (longer than success)
    }
});

// Form validation feedback
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('.form-input, .form-select');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error') && this.value.trim()) {
                this.classList.remove('error');
            }
        });
    });
});
</script>
@endsection