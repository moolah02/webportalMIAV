<?php
// app/Models/JobAssignment.php - UPDATED with Project relationship

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'technician_id', 
        'region_id',
        'pos_terminals',
        'client_id',
        'project_id', // NEW: Link to project
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

    // Existing relationships
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
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // NEW: Project relationship
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // NEW: Get display name including project context
    public function getDisplayNameAttribute()
    {
        $parts = [];
        
        if ($this->project) {
            $parts[] = $this->project->display_name;
        } elseif ($this->client) {
            $parts[] = $this->client->company_name;
        }
        
        if ($this->region) {
            $parts[] = $this->region->name;
        }
        
        $parts[] = $this->assignment_id;
        
        return implode(' - ', $parts);
    }

    // NEW: Scope by project
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    // NEW: Scope by client and project
    public function scopeByClientProject($query, $clientId, $projectId = null)
    {
        $query->where('client_id', $clientId);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        return $query;
    }

public static function generateAssignmentId()
{
    $prefix = 'ASN';
    $date = date('Ymd');
    
    $lastAssignment = self::where('assignment_id', 'like', "{$prefix}-{$date}-%")
        ->orderBy('assignment_id', 'desc')
        ->first();
    
    if ($lastAssignment) {
        $lastNumber = intval(substr($lastAssignment->assignment_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '001';
    }
    
    return "{$prefix}-{$date}-{$newNumber}";
}


public function getTerminalModels()
{
    if (!$this->pos_terminals || !is_array($this->pos_terminals)) {
        return collect();
    }
    
    return \App\Models\PosTerminal::whereIn('id', $this->pos_terminals)->get();
}

/**
 * Get assignment priority badge class
 */
public function getPriorityBadgeAttribute()
{
    $badges = [
        'low' => 'bg-secondary',
        'normal' => 'bg-primary', 
        'high' => 'bg-warning',
        'emergency' => 'bg-danger'
    ];

    return $badges[$this->priority] ?? 'bg-secondary';
}

/**
 * Get assignment status badge class
 */
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


public function getIsOverdueAttribute()
{
    return $this->scheduled_date < now()->toDateString() && 
           in_array($this->status, ['assigned', 'in_progress']);
}


public function getTerminalCountAttribute()
{
    return is_array($this->pos_terminals) ? count($this->pos_terminals) : 0;
}
}