<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_code',
        'project_name', 
        'client_id',
        'project_type', // discovery, servicing, support, maintenance, installation
        'description',
        'start_date',
        'end_date',
        'status', // active, completed, paused, cancelled
        'priority', // low, normal, high, emergency
        'budget',
        'estimated_terminals_count',
        'actual_terminals_count',
        'project_manager_id',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2'
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManager()
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // Project has many assignments
    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class);
    }

    // Project can have many terminals (through assignments)
    public function terminals()
    {
        return $this->hasManyThrough(
            PosTerminal::class,
            JobAssignment::class,
            'project_id', // Foreign key on job_assignments table
            'id', // Foreign key on pos_terminals table  
            'id', // Local key on projects table
            'pos_terminals' // Local key on job_assignments table (JSON field)
        );
    }

    // Get unique terminals across all assignments for this project
    public function getUniqueTerminals()
    {
        $terminalIds = $this->jobAssignments()
            ->get()
            ->pluck('pos_terminals')
            ->flatten()
            ->unique()
            ->values();

        return PosTerminal::whereIn('id', $terminalIds)->get();
    }

    // Project progress tracking
    public function getProgressAttribute()
    {
        $totalAssignments = $this->jobAssignments()->count();
        if ($totalAssignments === 0) return 0;
        
        $completedAssignments = $this->jobAssignments()->where('status', 'completed')->count();
        return round(($completedAssignments / $totalAssignments) * 100, 2);
    }

    // Display name: "Client A - Discovery Project" 
    public function getDisplayNameAttribute()
    {
        return "{$this->client->company_name} - {$this->project_name}";
    }

    // Project type badge
    public function getProjectTypeBadgeAttribute()
    {
        $badges = [
            'discovery' => ['class' => 'bg-info', 'icon' => '🔍', 'text' => 'Discovery'],
            'servicing' => ['class' => 'bg-success', 'icon' => '🔧', 'text' => 'Servicing'],
            'support' => ['class' => 'bg-warning', 'icon' => '💬', 'text' => 'Support'],
            'maintenance' => ['class' => 'bg-secondary', 'icon' => '⚙️', 'text' => 'Maintenance'],
            'installation' => ['class' => 'bg-primary', 'icon' => '📦', 'text' => 'Installation'],
            'upgrade' => ['class' => 'bg-purple', 'icon' => '⬆️', 'text' => 'Upgrade'],
            'decommission' => ['class' => 'bg-danger', 'icon' => '🗑️', 'text' => 'Decommission']
        ];

        return $badges[$this->project_type] ?? ['class' => 'bg-secondary', 'icon' => '📋', 'text' => ucfirst($this->project_type)];
    }

    // Status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'bg-success',
            'completed' => 'bg-primary',
            'paused' => 'bg-warning',
            'cancelled' => 'bg-danger'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    // Generate unique project code
    public static function generateProjectCode($clientId, $projectType)
    {
        $client = Client::find($clientId);
        $clientCode = $client ? strtoupper(substr($client->company_name, 0, 3)) : 'GEN';
        $typeCode = strtoupper(substr($projectType, 0, 3));
        $yearMonth = date('Ym');
        
        $lastProject = self::where('project_code', 'like', "{$clientCode}-{$typeCode}-{$yearMonth}-%")
            ->orderBy('project_code', 'desc')
            ->first();
        
        if ($lastProject) {
            $lastNumber = intval(substr($lastProject->project_code, -2));
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }
        
        return "{$clientCode}-{$typeCode}-{$yearMonth}-{$newNumber}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('project_type', $type);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['active', 'paused']);
    }

    // Validation helpers
    public static function getValidProjectTypes()
    {
        return [
            'discovery' => 'Terminal Discovery',
            'servicing' => 'Terminal Servicing', 
            'support' => 'Terminal Support',
            'maintenance' => 'Maintenance',
            'installation' => 'Installation',
            'upgrade' => 'System Upgrade',
            'decommission' => 'Decommission'
        ];
    }
}