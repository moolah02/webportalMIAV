<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class PosTerminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'region',
        'region_id',
        'city',
        'province',
        'terminal_id',
        'client_id',
        'merchant_name',
        'merchant_contact_person',
        'merchant_phone',
        'merchant_email',
        'physical_address',
        'area',
        'business_type',
        'installation_date',
        'terminal_model',
        'serial_number',
        'contract_details',
        'status',
        'current_status',
        'last_service_date',
        'next_service_due',
        'status_info',
        'coordinates',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'last_service_date' => 'date',
        'next_service_due' => 'date',
        'status_info' => 'array',
        'coordinates' => 'array',
    ];

    // Existing Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function serviceReports()
    {
        return $this->hasManyThrough(ServiceReport::class, JobAssignment::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function regionModel()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function visits()
    {
        return $this->hasMany(TechnicianVisit::class, 'pos_terminal_id');
    }

    public function latestVisit()
    {
        return $this->hasOne(TechnicianVisit::class, 'pos_terminal_id')->latest('visit_date');
    }

    public function technicianTickets()
    {
        return $this->hasMany(Ticket::class, 'pos_terminal_id');
    }

    // NEW: Link to status category
    public function statusCategory()
    {
        return $this->belongsTo(Category::class, 'status', 'slug')
                    ->where('type', Category::TYPE_TERMINAL_STATUS);
    }

    // Existing Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    public function scopeFaulty($query)
    {
        return $query->whereIn('status', ['faulty', 'maintenance']);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByRegionId($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeByCurrentStatus($query, $status)
    {
        return $query->where('current_status', $status);
    }

    public function scopeActiveTerminals($query)
    {
        return $query->where('current_status', 'active');
    }

    public function scopeOfflineTerminals($query)
    {
        return $query->where('current_status', 'offline');
    }

    // Updated Helper methods to use categories
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isFaulty()
    {
        return in_array($this->status, ['faulty', 'maintenance']);
    }

    public function needsService()
    {
        return $this->next_service_due && $this->next_service_due <= now()->addDays(7);
    }

    // NEW: Get status with category styling
    public function getStatusBadgeAttribute()
    {
        $statusCategory = $this->statusCategory;

        if ($statusCategory) {
            return [
                'class' => 'status-badge',
                'style' => "background-color: {$statusCategory->color}15; color: {$statusCategory->color}; border: 1px solid {$statusCategory->color}40;",
                'icon' => $statusCategory->icon,
                'text' => $statusCategory->name
            ];
        }

        // Fallback to old system - always return array format for consistency
        $badges = [
            'active' => ['class' => 'status-active', 'text' => 'Active', 'icon' => '✅'],
            'offline' => ['class' => 'status-offline', 'text' => 'Offline', 'icon' => '📶'],
            'maintenance' => ['class' => 'status-pending', 'text' => 'Maintenance', 'icon' => '🔧'],
            'faulty' => ['class' => 'status-offline', 'text' => 'Faulty', 'icon' => '⚠️'],
            'decommissioned' => ['class' => 'status-offline', 'text' => 'Decommissioned', 'icon' => '🗑️'],
        ];

        return $badges[$this->status] ?? ['class' => 'status-offline', 'text' => ucfirst($this->status), 'icon' => ''];
    }

    public function getLastServiceInfoAttribute()
    {
        if (!$this->last_service_date) {
            return 'Never serviced';
        }

        $daysSince = $this->last_service_date->diffInDays(now());
        return "{$this->last_service_date->format('M d, Y')} ({$daysSince} days ago)";
    }

    public function getCurrentStatusBadgeAttribute()
    {
        // Similar to getStatusBadgeAttribute but for current_status
        $statusCategory = Category::where('type', Category::TYPE_TERMINAL_STATUS)
                                ->where('slug', $this->current_status)
                                ->first();

        if ($statusCategory) {
            return [
                'class' => 'bg-custom',
                'style' => "background-color: {$statusCategory->color};",
                'icon' => $statusCategory->icon,
                'text' => $statusCategory->name
            ];
        }

        // Fallback
        $badges = [
            'active' => 'bg-success',
            'offline' => 'bg-danger',
            'maintenance' => 'bg-warning',
            'faulty' => 'bg-secondary',
            'decommissioned' => 'bg-dark',
        ];

        return $badges[$this->current_status] ?? 'bg-secondary';
    }

    public function isCurrentlyActive()
    {
        return $this->current_status === 'active';
    }

    public function isCurrentlyOffline()
    {
        return in_array($this->current_status, ['offline', 'faulty', 'decommissioned']);
    }

    public function getRegionNameAttribute()
    {
        return $this->regionModel?->name ?? $this->region;
    }

    // NEW: Get available status options from categories
    public static function getStatusOptions()
    {
        return Category::where('type', Category::TYPE_TERMINAL_STATUS)
                      ->where('is_active', true)
                      ->orderBy('sort_order')
                      ->get()
                      ->pluck('name', 'slug');
    }

    // NEW: Get service type options from categories
    public static function getServiceTypeOptions()
    {
        return Category::where('type', Category::TYPE_SERVICE_TYPE)
                      ->where('is_active', true)
                      ->orderBy('sort_order')
                      ->get()
                      ->pluck('name', 'slug');
    }

    public function getLastVisitInfoAttribute()
    {
        $latestVisit = $this->latestVisit;

        if (!$latestVisit) {
            return 'Never visited';
        }

        $daysSince = $latestVisit->visit_date->diffInDays(now());
        return "{$latestVisit->technician->name} - {$daysSince} days ago";
    }
    // app/Models/PosTerminal.php
public function region()
{
    return $this->belongsTo(\App\Models\Region::class, 'region_id');
}

}
