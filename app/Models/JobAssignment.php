<?php
// app/Models/JobAssignment.php - UPDATED with Project relationship

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        'assignment_history' => 'array', // Add this line
        'scheduled_date' => 'date',
        'completed_date' => 'date',
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

    public static function generateAssignmentId(): string
    {
        $prefix = 'ASN';
        $date   = now()->format('Ymd');

        // Lock rows for "today" so two requests can't generate the same suffix.
        // IMPORTANT: call this method INSIDE an open DB transaction.
        $maxSeq = static::where('assignment_id', 'like', "{$prefix}-{$date}-%")
            ->lockForUpdate()
            ->max(
                DB::raw(
                    "CAST(SUBSTRING_INDEX(assignment_id, '-', -1) AS UNSIGNED)"
                )
            );

        $next = (int) $maxSeq + 1;

        return sprintf('%s-%s-%03d', $prefix, $date, $next);
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

    public function getListTitleAttribute()
    {
        $parts = [];

        if ($this->project)   { $parts[] = $this->project->project_name; }
        if ($this->client)    { $parts[] = $this->client->company_name; }
        if ($this->region)    { $parts[] = $this->region->name; }

        // e.g. "Johnson PLC Servicing • XYZ Bank • Harare Region"
        return implode(' • ', array_filter($parts));
    }

    // app/Models/JobAssignment.php
    public function getTerminalMerchantPreviewAttribute()
    {
        $names = $this->getTerminalModels()
            ->pluck('merchant_name')
            ->filter()
            ->take(3);

        return $names->implode(', ');
    }

    public function getTerminalCountAttribute()
    {
        return is_array($this->pos_terminals) ? count($this->pos_terminals) : 0;
    }

    // NEW: Assignment history helper methods
    public function hasAssignmentHistory()
    {
        return !empty($this->assignment_history);
    }

    public function getAssignmentHistoryCount()
    {
        return count($this->assignment_history ?? []);
    }
    public function technicianVisits()
{
    return $this->hasMany(TechnicianVisit::class, 'job_assignment_id')->latest();
}

}
