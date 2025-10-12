<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_code',
        'project_name',
        'client_id',
        'project_type',
        'description',
        'start_date',
        'end_date',
        'status',
        'priority',
        'budget',
        'estimated_terminals_count',
        'actual_terminals_count',
        'completion_percentage',
        'project_manager_id',
        'previous_project_id',
        'insights_from_previous',
        'terminal_selection_criteria',
        'notes',
        'created_by',
        'completed_at',
        'completed_by',
        // NEW: Add closure fields
        'closed_at',
        'closed_by',
        'closure_reason',
        'report_generated_at',
        'report_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime', // NEW: Add cast for closed_at
        'report_generated_at' => 'datetime',
        'terminal_selection_criteria' => 'array',
        'budget' => 'decimal:2',
        'completion_percentage' => 'decimal:2',
    ];

    protected $appends = [
        'status_badge_class',
        'priority_badge_class',
        'is_overdue',
        'duration_days',
    ];

    // ==============================================
    // RELATIONSHIPS
    // ==============================================

    /**
     * Project belongs to a client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Project has a project manager (employee)
     */
    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    /**
     * Project was created by an employee
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    /**
     * Project was completed by an employee
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'completed_by');
    }

    // NEW: Add closedBy relationship
    /**
     * Project was closed by an employee
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'closed_by');
    }

    /**
     * Project can reference a previous project for insights
     */
    public function previousProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'previous_project_id');
    }

    /**
     * Projects that reference this project as previous
     */
    public function nextProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'previous_project_id');
    }

    /**
     * Project has many POS terminals through project_terminals pivot
     */
    public function posTerminals(): BelongsToMany
    {
        return $this->belongsToMany(PosTerminal::class, 'project_terminals', 'project_id', 'pos_terminal_id')
            ->withPivot('included_at', 'inclusion_reason', 'is_active', 'created_by')
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    public function completion()
    {
        return $this->hasOne(ProjectCompletion::class);
    }

    /**
     * Project has many visits through job assignments
     */
    public function visits(): HasManyThrough
    {
        return $this->hasManyThrough(
            Visit::class,
            JobAssignment::class,
            'project_id',
            'job_assignment_id',
            'id',
            'id'
        );
    }

    /**
     * Project has many reports
     */
    public function projectReports(): HasMany
    {
        return $this->hasMany(ProjectReport::class);
    }

    /**
     * Project has many report runs
     */
    public function reportRuns(): HasMany
    {
        return $this->hasMany(ReportRun::class);
    }

    // ==============================================
    // SCOPES
    // ==============================================

    /**
     * Scope for active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed projects
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // NEW: Add scope for closed projects
    /**
     * Scope for closed projects (all closure types)
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['completed', 'closed', 'cancelled', 'on_hold']);
    }

    /**
     * Scope for projects by client
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope for projects by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('project_type', $type);
    }

    /**
     * Scope for overdue projects
     */
    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled', 'closed', 'on_hold']);
    }

    /**
     * Scope for projects with progress calculation
     */
    public function scopeWithProgress($query)
    {
        return $query->withCount([
            'projectTerminals as total_terminals',
            'jobAssignments as total_assignments',
            'visits as total_visits'
        ]);
    }

    // ==============================================
    // ACCESSORS & MUTATORS
    // ==============================================

    /**
     * Get the status badge CSS class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'completed' => 'bg-primary',
            'paused' => 'bg-warning',
            'cancelled' => 'bg-secondary',
            'closed' => 'bg-info', // NEW: Add closed status
            'on_hold' => 'bg-warning', // NEW: Add on_hold status
            default => 'bg-secondary',
        };
    }

    /**
     * Get the priority badge CSS class
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'emergency' => 'bg-danger',
            'high' => 'bg-warning',
            'normal' => 'bg-primary',
            'low' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if project is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date &&
            $this->end_date->isPast() &&
            !in_array($this->status, ['completed', 'cancelled', 'closed', 'on_hold']);
    }

    /**
     * Get project duration in days
     */
    public function getDurationDaysAttribute(): ?int
    {
        if (!$this->start_date) {
            return null;
        }

        $endDate = $this->closed_at ?: ($this->completed_at ?: ($this->end_date ?: now()));
        return $this->start_date->diffInDays($endDate);
    }

    /**
     * Get formatted budget
     */
    public function getFormattedBudgetAttribute(): string
    {
        return $this->budget ? '$' . number_format($this->budget, 2) : 'Not set';
    }

    // NEW: Add closure reason accessor
    /**
     * Get formatted closure reason
     */
    public function getFormattedClosureReasonAttribute(): string
    {
        if (!$this->closure_reason) {
            return 'N/A';
        }

        return match ($this->closure_reason) {
            'completed' => 'Completed Successfully',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold',
            'client_request' => 'Client Request',
            default => ucfirst(str_replace('_', ' ', $this->closure_reason)),
        };
    }

    // ==============================================
    // HELPER METHODS
    // ==============================================

    /**
     * Calculate real-time project completion percentage
     */
    public function calculateCompletionPercentage(): float
    {
        $totalTerminals = $this->projectTerminals()->where('is_active', true)->count();

        if ($totalTerminals === 0) {
            return 0;
        }

        $completedTerminals = $this->projectTerminals()
            ->where('is_active', true)
            ->whereHas('posTerminal.visits', function($query) {
                $query->whereIn('terminal_status', ['seen_working', 'seen_issues'])
                    ->whereHas('jobAssignment', function($subQuery) {
                        $subQuery->where('project_id', $this->id);
                    });
            })
            ->distinct('pos_terminal_id')
            ->count();

        return round(($completedTerminals / $totalTerminals) * 100, 2);
    }

    /**
     * Update completion percentage
     */
    public function updateCompletionPercentage(): void
    {
        $this->update(['completion_percentage' => $this->calculateCompletionPercentage()]);
    }

    // UPDATED: Check if project can be closed (more flexible than completion)
    /**
     * Check if project can be closed
     */
    public function canBeClosed(): bool
    {
        return !in_array($this->status, ['completed', 'closed', 'cancelled', 'on_hold']);
    }

    /**
     * Check if project can be completed (strict requirements)
     */
    public function canBeCompleted(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }

        // Check if all terminals have been visited
        $totalTerminals = $this->projectTerminals()->where('is_active', true)->count();
        $visitedTerminals = $this->projectTerminals()
            ->where('is_active', true)
            ->whereHas('posTerminal.visits', function($query) {
                $query->whereHas('jobAssignment', function($subQuery) {
                    $subQuery->where('project_id', $this->id);
                });
            })
            ->distinct('pos_terminal_id')
            ->count();

        return $visitedTerminals >= $totalTerminals;
    }

    /**
     * Get project statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_terminals' => $this->projectTerminals()->where('is_active', true)->count(),
            'total_assignments' => $this->jobAssignments()->count(),
            'completed_assignments' => $this->jobAssignments()->where('status', 'completed')->count(),
            'total_visits' => $this->visits()->count(),
            'unique_technicians' => $this->jobAssignments()->distinct('technician_id')->count(),
            'avg_completion_time' => $this->jobAssignments()
                ->whereNotNull('actual_start_time')
                ->whereNotNull('actual_end_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, actual_start_time, actual_end_time)) as avg_hours')
                ->value('avg_hours'),
        ];
    }

    /**
     * Get insights from previous project
     */
    public function getPreviousProjectInsights(): ?array
    {
        if (!$this->previousProject) {
            return null;
        }

        return [
            'completion_time' => $this->previousProject->duration_days,
            'terminal_count' => $this->previousProject->actual_terminals_count,
            'success_rate' => $this->previousProject->completion_percentage,
            'key_learnings' => $this->insights_from_previous,
        ];
    }

    /**
     * Generate project code if not set
     */
    public static function generateProjectCode(Client $client, string $projectType): string
    {
        $typeCode = strtoupper(substr($projectType, 0, 3));
        $dateCode = date('Ymd');
        $sequence = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);

        return "PROJ-{$typeCode}-{$dateCode}-{$sequence}";
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (!$project->project_code) {
                $client = Client::find($project->client_id);
                $project->project_code = self::generateProjectCode($client, $project->project_type);
            }
        });

        static::updated(function ($project) {
            // Auto-update completion percentage when project is updated
            if ($project->isDirty(['status']) && !in_array($project->status, ['completed', 'closed', 'cancelled'])) {
                $project->updateCompletionPercentage();
            }
        });
    }

    public function projectTerminals()
    {
        return $this->hasMany(ProjectTerminal::class);
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class);
    }
}
