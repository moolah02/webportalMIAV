<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTerminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'pos_terminal_id',
        'included_at',
        'inclusion_reason',
        'is_active',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'included_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ==============================================
    // RELATIONSHIPS
    // ==============================================

    /**
     * Project terminal belongs to a project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Project terminal belongs to a POS terminal
     */
    public function posTerminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class);
    }

    /**
     * Project terminal was created by an employee
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // ==============================================
    // SCOPES
    // ==============================================

    /**
     * Scope for active project terminals
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for terminals in a specific project
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope for terminals by inclusion reason
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('inclusion_reason', 'like', "%{$reason}%");
    }

    // ==============================================
    // HELPER METHODS
    // ==============================================

    /**
     * Deactivate this terminal assignment
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Activate this terminal assignment
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Check if terminal has been visited for this project
     */
    public function hasBeenVisited(): bool
    {
        return $this->posTerminal
            ->technicianVisits()
            ->whereHas('jobAssignment', function($query) {
                $query->where('project_id', $this->project_id);
            })
            ->exists();
    }

    /**
     * Get latest visit for this project
     */
    public function getLatestVisit()
    {
        return $this->posTerminal
            ->technicianVisits()
            ->whereHas('jobAssignment', function($query) {
                $query->where('project_id', $this->project_id);
            })
            ->latest('visit_date')
            ->first();
    }
}
