<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        // Existing fields
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

        // New fields for direction and customer licenses
        'license_direction',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_company',
        'customer_phone',
        'customer_address',
        'revenue_amount',
        'billing_cycle',
        'license_terms',
        'usage_limit',
        'support_level',
        'customer_reference',
        'service_start_date',
        'license_quantity',
        'auto_renewal_customer'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'service_start_date' => 'date',
        'cost' => 'decimal:2',
        'renewal_cost' => 'decimal:2',
        'revenue_amount' => 'decimal:2',
        'auto_renewal' => 'boolean',
        'auto_renewal_customer' => 'boolean',
        'renewal_reminder_days' => 'integer',
        'license_quantity' => 'integer',
    ];

    // License Direction Constants
    const LICENSE_DIRECTIONS = [
        'company_held' => 'Company Held License',
        'customer_issued' => 'Customer Issued License'
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
        'service_permit' => 'Service Permit',
        'product_license' => 'Product License',
        'api_access' => 'API Access License',
        'data_license' => 'Data License',
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

    // New constants for customer licenses
    const BILLING_CYCLES = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'annually' => 'Annually',
        'one_time' => 'One Time'
    ];

    const SUPPORT_LEVELS = [
        'basic' => 'Basic Support',
        'standard' => 'Standard Support',
        'premium' => 'Premium Support',
        'enterprise' => 'Enterprise Support'
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

    // License Direction Scopes
    public function scopeCompanyHeld($query)
    {
        return $query->where('license_direction', 'company_held');
    }

    public function scopeCustomerIssued($query)
    {
        return $query->where('license_direction', 'customer_issued');
    }

    // Existing Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 15)
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

    // Direction Helper Methods
    public function isCompanyHeld()
    {
        return $this->license_direction === 'company_held';
    }

    public function isCustomerIssued()
    {
        return $this->license_direction === 'customer_issued';
    }

    public function getLicenseDirectionNameAttribute()
    {
        return self::LICENSE_DIRECTIONS[$this->license_direction] ?? 'Unknown';
    }

    // Customer License Helper Methods
    public function getBillingCycleNameAttribute()
    {
        return self::BILLING_CYCLES[$this->billing_cycle] ?? 'N/A';
    }

    public function getSupportLevelNameAttribute()
    {
        return self::SUPPORT_LEVELS[$this->support_level] ?? 'N/A';
    }

    public function getCustomerDisplayNameAttribute()
    {
        if ($this->customer_company) {
            return $this->customer_company . ($this->customer_name ? " ({$this->customer_name})" : '');
        }
        return $this->customer_name ?? 'N/A';
    }

    public function getAnnualRevenueAttribute()
    {
        if (!$this->revenue_amount || !$this->billing_cycle) {
            return 0;
        }

        switch ($this->billing_cycle) {
            case 'monthly':
                return $this->revenue_amount * 12;
            case 'quarterly':
                return $this->revenue_amount * 4;
            case 'annually':
                return $this->revenue_amount;
            case 'one_time':
                return $this->revenue_amount;
            default:
                return 0;
        }
    }

    // Existing Accessors & Mutators
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

    public function getDaysUntilExpiryAttribute(): ?int
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

   public function getIsExpiringSoonAttribute(): bool
{
    if (!$this->expiry_date) return false;
    $threshold = 15; // fixed threshold since renewal_reminder_days was removed
    return now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false) <= $threshold
        && !$this->is_expired;
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
            'updated_by' => auth()->id()
        ]);
    }

    public function getStatusColorClass()
    {
        $colors = [
            'active' => 'background: #e8f5e8; color: #4caf50;',
            'expired' => 'background: #ffebee; color: #f44336;',
            'pending_renewal' => 'background: #fff3e0; color: #ff9800;',
            'suspended' => 'background: #fce4ec; color: #e91e63;',
            'cancelled' => 'background: #f3e5f5; color: #9c27b0;',
            'under_review' => 'background: #e3f2fd; color: #2196f3;'
        ];

        return $colors[$this->status] ?? 'background: #f5f5f5; color: #666;';
    }

    public function getPriorityColorClass()
    {
        $colors = [
            'critical' => 'background: #ffebee; color: #f44336;',
            'high' => 'background: #fff3e0; color: #ff9800;',
            'medium' => 'background: #e3f2fd; color: #2196f3;',
            'low' => 'background: #f3e5f5; color: #9c27b0;'
        ];

        return $colors[$this->priority_level] ?? 'background: #f5f5f5; color: #666;';
    }
}
