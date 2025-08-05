<?php
// app/Models/DeploymentTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'region_id',
        'description',
        'pos_terminals',
        'estimated_duration_hours',
        'service_type',
        'priority',
        'notes',
        'is_active',
        'created_by',
        'tags',
        'group_by'
        
    ];

    protected $casts = [
        'pos_terminals' => 'array',
        'estimated_duration_hours' => 'decimal:2',
        'is_active' => 'boolean',
        'tags' => 'array'
    ];

    // Relationships
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class, 'template_id');
    }

    // Get terminal objects from IDs
    public function getTerminalsAttribute()
    {
        if (!$this->pos_terminals) return collect();
        
        return PosTerminal::whereIn('id', $this->pos_terminals)
            ->with(['client', 'regionModel'])
            ->get();
    }

    // Get terminals count
    public function getTerminalsCountAttribute()
    {
        return is_array($this->pos_terminals) ? count($this->pos_terminals) : 0;
    }

    // Get service type display name
    public function getServiceTypeDisplayAttribute()
    {
        $serviceTypeCategory = Category::findBySlugAndType($this->service_type, Category::TYPE_SERVICE_TYPE);
        return $serviceTypeCategory ? $serviceTypeCategory->name : ucfirst(str_replace('_', ' ', $this->service_type));
    }

    // Get priority badge
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'emergency' => ['class' => 'bg-danger', 'icon' => '🔴', 'text' => 'Emergency'],
            'high' => ['class' => 'bg-warning', 'icon' => '🟡', 'text' => 'High'],
            'normal' => ['class' => 'bg-primary', 'icon' => '🔵', 'text' => 'Normal'],
            'low' => ['class' => 'bg-secondary', 'icon' => '⚪', 'text' => 'Low']
        ];

        return $badges[$this->priority] ?? $badges['normal'];
    }

    // Generate unique template ID
    public static function generateTemplateId()
    {
        $prefix = 'TPL-' . date('Ymd') . '-';
        $lastTemplate = self::where('template_name', 'like', $prefix . '%')
            ->orderBy('template_name', 'desc')
            ->first();
        
        if ($lastTemplate) {
            preg_match('/TPL-\d{8}-(\d{3})/', $lastTemplate->template_name, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return $prefix . $newNumber;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Helper methods
    public function canBeDeployed()
    {
        return $this->is_active && $this->terminals_count > 0;
    }

    public function getEstimatedTotalDuration()
    {
        return $this->estimated_duration_hours * $this->terminals_count;
    }

    public function getCitiesCovered()
    {
        return $this->terminals->pluck('city')->unique()->values();
    }

    public function getClientsCovered()
    {
        return $this->terminals->pluck('client.company_name')->unique()->filter()->values();
    }

    // Deploy template as job assignment
    public function deployAsAssignment($technicianId, $scheduledDate, $additionalNotes = null)
    {
        $assignmentId = JobAssignment::generateAssignmentId();
        
        $notes = $this->notes;
        if ($additionalNotes) {
            $notes .= "\n\nDeployment Notes: " . $additionalNotes;
        }
        
        return JobAssignment::create([
            'assignment_id' => $assignmentId,
            'template_id' => $this->id,
            'technician_id' => $technicianId,
            'region_id' => $this->region_id,
            'pos_terminals' => $this->pos_terminals,
            'scheduled_date' => $scheduledDate,
            'service_type' => $this->service_type,
            'priority' => $this->priority,
            'status' => 'assigned',
            'estimated_duration_hours' => $this->estimated_duration_hours,
            'notes' => $notes,
            'created_by' => auth()->id() ?? 1
        ]);
    }
}