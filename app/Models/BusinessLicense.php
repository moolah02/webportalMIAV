<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_name',
        'license_number',
        'license_type',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'renewal_date',
        'status',
        'cost',
        'renewal_cost',
        'location',
        'department_id',
        'responsible_employee_id',
        'description',
        'compliance_notes',
        'document_path',
        'renewal_reminder_days',
        'auto_renewal',
        'priority_level',
        'business_impact',
        'regulatory_body',
        'license_conditions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'cost' => 'decimal:2',
        'renewal_cost' => 'decimal:2',
        'auto_renewal' => 'boolean',
        'renewal_reminder_days' => 'integer',
    ];

    // License Types
    const LICENSE_TYPES = [
        'business_operation' => 'Business Operation License',
        'professional' => 'Professional License',
        'trade' => 'Trade License',
        'health_safety' => 'Health & Safety License',
        'environmental' => 'Environmental License',
        'industry_specific' => 'Industry Specific License',
        'import_export' => 'Import/Export License',
        'software' => 'Software License',
        'broadcasting' => 'Broadcasting License',
        'financial' => 'Financial License',
        'other' => 'Other'
    ];

    // Status Options
    const STATUSES = [
        'active' => 'Active',
        'expired' => 'Expired',
        'pending_renewal' => 'Pending Renewal',
        'suspended' => 'Suspended',
        'cancelled' => 'Cancelled',
        'under_review' => 'Under Review'
    ];

    // Priority Levels
    const PRIORITY_LEVELS = [
        'critical' => 'Critical',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low'
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function responsibleEmployee()
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    // Accessors & Mutators
    public function getLicenseTypeNameAttribute()
    {
        return self::LICENSE_TYPES[$this->license_type] ?? ucfirst(str_replace('_', ' ', $this->license_type));
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPriorityLevelNameAttribute()
    {
        return self::PRIORITY_LEVELS[$this->priority_level] ?? ucfirst(str_replace('_', ' ', $this->priority_level));
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }
        $reminderDays = $this->renewal_reminder_days ?: 30;
        return $this->expiry_date->diffInDays(now()) <= $reminderDays;
    }

    public function getComplianceStatusAttribute()
    {
        if ($this->is_expired) {
            return 'non_compliant';
        }
        if ($this->is_expiring_soon) {
            return 'warning';
        }
        return 'compliant';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    public function scopeCritical($query)
    {
        return $query->where('priority_level', 'critical');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // Helper Methods
    public function canRenew()
    {
        return in_array($this->status, ['active', 'expired', 'pending_renewal']);
    }

    public function getRenewalUrl()
    {
        return route('business-licenses.renew', $this);
    }

    public function getViewUrl()
    {
        return route('business-licenses.show', $this);
    }

    public function getEditUrl()
    {
        return route('business-licenses.edit', $this);
    }

    public function markAsRenewed($newExpiryDate, $cost = null)
    {
        $this->update([
            'status' => 'active',
            'renewal_date' => now(),
            'expiry_date' => $newExpiryDate,
            'renewal_cost' => $cost ?: $this->renewal_cost,
        ]);
    }

    public function getStatusColorClass()
    {
        return match($this->status) {
            'active' => 'text-green-600 bg-green-100',
            'expired' => 'text-red-600 bg-red-100',
            'pending_renewal' => 'text-yellow-600 bg-yellow-100',
            'suspended' => 'text-orange-600 bg-orange-100',
            'cancelled' => 'text-gray-600 bg-gray-100',
            'under_review' => 'text-blue-600 bg-blue-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    public function getPriorityColorClass()
    {
        return match($this->priority_level) {
            'critical' => 'text-red-600 bg-red-100',
            'high' => 'text-orange-600 bg-orange-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'low' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}