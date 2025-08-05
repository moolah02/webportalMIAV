<?php
// app/Models/JobAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    use HasFactory;

   // In your JobAssignment model
protected $fillable = [
    'assignment_id',
    'technician_id', 
    'region_id',
    'pos_terminals',
    'client_id',
    'scheduled_date',
    'service_type',
    'priority',
    'status',
    'notes',
    'estimated_duration_hours',
    'actual_start_time',
    'actual_end_time', 
    'completion_notes',
    'created_by'
];

protected $casts = [
    'pos_terminals' => 'array',
    'scheduled_date' => 'date',
    'estimated_duration_hours' => 'decimal:2',
    'actual_start_time' => 'datetime',
    'actual_end_time' => 'datetime'
];

    public function technician()
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // NEW: Link to service type category
    public function serviceTypeCategory()
    {
        return $this->belongsTo(Category::class, 'service_type', 'slug')
                    ->where('type', Category::TYPE_SERVICE_TYPE);
    }

    // Generate unique assignment ID
    public static function generateAssignmentId()
    {
        $prefix = 'ASG-' . date('Ymd') . '-';
        $lastAssignment = self::where('assignment_id', 'like', $prefix . '%')
            ->orderBy('assignment_id', 'desc')
            ->first();
        
        if ($lastAssignment) {
            $lastNumber = intval(substr($lastAssignment->assignment_id, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return $prefix . $newNumber;
    }

    // Get terminal objects from IDs
    public function getTerminalsAttribute()
    {
        if (!$this->pos_terminals) return collect();
        
        return PosTerminal::whereIn('id', $this->pos_terminals)->get();
    }

    // Check if assignment is overdue
    public function isOverdue()
    {
        return $this->scheduled_date < now()->toDateString() && $this->status === 'assigned';
    }

    // NEW: Get service type with category styling
    public function getServiceTypeBadgeAttribute()
    {
        $serviceTypeCategory = $this->serviceTypeCategory;
        
        if ($serviceTypeCategory) {
            return [
                'class' => 'service-badge',
                'style' => "background-color: {$serviceTypeCategory->color}15; color: {$serviceTypeCategory->color}; border: 1px solid {$serviceTypeCategory->color}40;",
                'icon' => $serviceTypeCategory->icon,
                'text' => $serviceTypeCategory->name
            ];
        }

        // Fallback to old system
        $badges = [
            'routine_maintenance' => ['text' => 'Routine Maintenance', 'icon' => '🔄'],
            'emergency_repair' => ['text' => 'Emergency Repair', 'icon' => '🚨'],
            'software_update' => ['text' => 'Software Update', 'icon' => '⬇️'],
            'hardware_replacement' => ['text' => 'Hardware Replacement', 'icon' => '🔄'],
            'network_configuration' => ['text' => 'Network Configuration', 'icon' => '🌐'],
            'installation' => ['text' => 'Installation', 'icon' => '📦'],
            'decommission' => ['text' => 'Decommission', 'icon' => '🗑️'],
        ];

        $badge = $badges[$this->service_type] ?? ['text' => ucfirst(str_replace('_', ' ', $this->service_type)), 'icon' => ''];
        return [
            'class' => 'service-badge-default',
            'style' => 'background-color: #f5f5f5; color: #666; padding: 2px 6px; border-radius: 8px; font-size: 11px;',
            'icon' => $badge['icon'],
            'text' => $badge['text']
        ];
    }

    // Get status badge class
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'assigned' => 'bg-info',
            'in_progress' => 'bg-warning',
            'completed' => 'bg-success',
            'cancelled' => 'bg-secondary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    // Get priority badge class
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'emergency' => 'bg-danger',
            'high' => 'bg-warning',
            'normal' => 'bg-primary',
            'low' => 'bg-secondary'
        ];

        return $badges[$this->priority] ?? 'bg-secondary';
    }

    // Calculate actual duration
    public function getActualDurationAttribute()
    {
        if (!$this->actual_start_time || !$this->actual_end_time) {
            return null;
        }

        return $this->actual_start_time->diffInHours($this->actual_end_time, true);
    }

    // NEW: Get service type display name from category
    public function getServiceTypeDisplayAttribute()
    {
        $serviceTypeCategory = $this->serviceTypeCategory;
        return $serviceTypeCategory ? $serviceTypeCategory->name : ucfirst(str_replace('_', ' ', $this->service_type));
    }

    // NEW: Get valid service types for validation
    public static function getValidServiceTypes()
    {
        return Category::getSelectOptions(Category::TYPE_SERVICE_TYPE)->keys()->toArray();
    }

    // Scopes
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    public function scopeForRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeScheduledFor($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', now()->toDateString())
                    ->where('status', 'assigned');
    }

    // NEW: Scope for service type
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }
    
}