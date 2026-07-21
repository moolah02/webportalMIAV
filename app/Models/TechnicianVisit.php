<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianVisit extends Model
{
    use HasFactory;

    protected $table = 'technician_visits';

    /**
     * Fillable columns (old + new).
     * Kept legacy fields for backward compatibility (e.g., photos JSON),
     * while adding the new columns we altered in SQL.
     */
    protected $fillable = [
        // identifiers & links
        'visit_id',
        'technician_id',
        'pos_terminal_id',
        'client_id',
        'job_assignment_id',

        // snapshots
        'merchant_id_snapshot',
        'device_type_snapshot',
        'serial_snapshot',
        'team_name',
        'address_snapshot',

        // lifecycle
        'status',   // open | in_progress | closed
        'outcome',  // completed | could_not_access_site | parts_required | reschedule | terminal_not_found | terminal_relocated

        // timing
        'visit_date',     // legacy
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',

        // classification / terminal state
        'asset_type',
        'asset_id',
        'terminal_status',               // legacy
        'terminal_status_during_visit',  // active | inactive | not_found | relocated | replaced
        'terminal_condition',            // good | fair | poor | damaged

        // notes & content
        'technician_feedback',
        'comments',
        'issues_found',            // json (legacy)
        'condition_notes',         // new (text)
        'corrective_action',
        'visit_summary',
        'recommended_next_action',
        'merchant_feedback',

        // location
        'latitude',
        'longitude',
        'gps_accuracy_m',

        // signature & media
        'merchant_sign_off_name',
        'merchant_signature_path',
        'photos',         // legacy JSON – kept for backward compatibility
        'photos_count',

        // misc
        'other_terminals_found',   // json
        'client_guid',
    ];

    /**
     * Attribute casting.
     * - Use json for arrays
     * - datetime for timestamps
     * - decimal for coordinates
     */
    protected $casts = [
        'visit_date'              => 'datetime',
        'scheduled_at'            => 'datetime',
        'started_at'              => 'datetime',
        'ended_at'                => 'datetime',
        // issues_found stored as plain string (single dropdown value)
        'photos'                  => 'array',   // legacy support
        'other_terminals_found'   => 'array',
        'latitude'                => 'decimal:8',
        'longitude'               => 'decimal:8',
        'gps_accuracy_m'          => 'integer',
        'photos_count'            => 'integer',
    ];

    /* issues_found is a MySQL JSON column — must always store valid JSON.
     * Arrays are flattened to a comma-separated string first, then JSON-encoded. */
    public function setIssuesFoundAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['issues_found'] = null;
        } elseif (is_array($value)) {
            $str = implode(', ', array_filter($value)) ?: null;
            $this->attributes['issues_found'] = is_null($str) ? null : json_encode($str);
        } else {
            $this->attributes['issues_found'] = json_encode($value);
        }
    }

    /* Return the human-readable string (not the JSON wrapper) for display/forms. */
    public function getIssuesFoundAttribute($value): mixed
    {
        if (is_null($value)) {
            return null;
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // JSON string → plain string; JSON array → comma-joined string
            return is_array($decoded) ? implode(', ', $decoded) : $decoded;
        }
        return $value;
    }

    /* =========================
     * Relationships
     * ========================= */

    public function technician()
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    public function posTerminal()
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function jobAssignment()
    {
        return $this->belongsTo(JobAssignment::class, 'job_assignment_id');
    }

    public function attachments()
    {
        return $this->hasMany(TechnicianVisitAttachment::class, 'technician_visit_id');
    }

    public function audits()
    {
        return $this->hasMany(TechnicianVisitAudit::class, 'technician_visit_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'visit_id');
    }

    /* =========================
     * Scopes
     * ========================= */

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOutcome($query, string $outcome)
    {
        return $query->where('outcome', $outcome);
    }

    public function scopeByTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    public function scopeBetweenDates($query, $start, $end, string $column = 'started_at')
    {
        return $query->whereBetween($column, [$start, $end]);
    }

    /* =========================
     * Accessors / Helpers
     * ========================= */

    /**
     * Calculate duration_minutes if not explicitly set but we have started_at & ended_at.
     */
    public function getDurationMinutesAttribute($value)
    {
        if (!is_null($value)) {
            return (int) $value;
        }

        if ($this->started_at && $this->ended_at) {
            return (int) $this->started_at->diffInMinutes($this->ended_at);
        }

        return null;
    }

    /**
     * Ensure a human-friendly visit_id exists on create (e.g., VIS-20250826-001).
     */
    protected static function booted()
    {
        static::creating(function (self $model) {
            if (empty($model->visit_id)) {
                $model->visit_id = self::generateVisitId();
            }
        });
    }

    // Generate unique visit ID (kept from your original model)
    public static function generateVisitId()
    {
        $prefix = 'VIS-' . date('Ymd') . '-';

        try {
            $lastVisit = self::where('visit_id', 'like', $prefix . '%')
                ->orderBy('visit_id', 'desc')
                ->first();

            if ($lastVisit) {
                $lastNumber = intval(substr($lastVisit->visit_id, -3));
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }
        } catch (\Throwable $e) {
            // visit_id column may not exist on this DB instance yet
            $newNumber = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        return $prefix . $newNumber;
    }
    public function assignment()
{
    return $this->belongsTo(JobAssignment::class, 'job_assignment_id');
}

}
