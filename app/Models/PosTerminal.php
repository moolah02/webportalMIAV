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
        'monthly_transaction_target',
        'status',
        'current_status',
        'source',
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

    /**
     * The one definition of whether a terminal is working.
     *
     * Two columns describe it and they disagreed, which is why uptime differed
     * from page to page: `status` is the lifecycle enum set when the record is
     * created, `current_status` is what the last field visit found. The field
     * reading wins where there is one - in this system a terminal's state comes
     * from visits - and the lifecycle value is the fallback for a terminal
     * nobody has been to yet.
     *
     * ContractReportService already read it this way. This is that same rule,
     * written once, so the dashboards stop contradicting each other.
     */
    public const EFFECTIVE_STATUS_SQL = "LOWER(COALESCE(NULLIF(pos_terminals.current_status, ''), pos_terminals.status))";

    public function getEffectiveStatusAttribute(): string
    {
        return strtolower((string) ($this->current_status ?: $this->status));
    }

    /** Counts/filters terminals by the effective status above. */
    public function scopeEffectiveStatus($query, string|array $status)
    {
        $values      = array_map('strtolower', (array) $status);
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        return $query->whereRaw(self::EFFECTIVE_STATUS_SQL." IN ($placeholders)", $values);
    }

    // Existing Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * The assignments this terminal appears in.
     *
     * This is deliberately NOT an Eloquent relation. An assignment carries its
     * terminals in a JSON array column, `job_assignments.pos_terminals`, so
     * there is no `pos_terminal_id` for hasMany to key on - it was guessing a
     * column that has never existed, and every read of it threw "Unknown
     * column job_assignments.pos_terminal_id". On the terminal page that was
     * caught and logged, so the assignment list just looked empty; on
     * GET /api/pos-terminals?my_assignments_only=1 nothing caught it at all.
     *
     * Returns a query builder, so ->with(), ->latest(), ->limit() and ->get()
     * all still read the way they did. It cannot be eager-loaded or used with
     * whereHas - use scopeAssignedTo() for that.
     */
    public function jobAssignments()
    {
        return JobAssignment::query()->whereRaw(self::holdsTerminalSql((int) $this->id));
    }

    /**
     * Terminals that appear in an assignment held by this technician.
     *
     * The whereHas() this replaces could never run: see jobAssignments().
     */
    public function scopeAssignedTo($query, $technicianId, array $statuses = [])
    {
        return $query->whereExists(function ($q) use ($technicianId, $statuses) {
            $q->selectRaw('1')
              ->from('job_assignments')
              ->whereRaw(self::holdsTerminalSql('pos_terminals.id'))
              ->where('job_assignments.technician_id', $technicianId);

            if ($statuses) {
                $q->whereIn('job_assignments.status', $statuses);
            }
        });
    }

    /**
     * SQL that is true when an assignment's JSON terminal list holds this id.
     *
     * The list has been written both ways over the life of the table - [3] and
     * ["3"] - so match both rather than betting on one and silently returning
     * nothing. $idExpr is either an integer cast by the caller or a column
     * name, never user input.
     */
    private static function holdsTerminalSql(string|int $idExpr): string
    {
        return "(JSON_CONTAINS(job_assignments.pos_terminals, CAST($idExpr AS JSON))"
             . " OR JSON_CONTAINS(job_assignments.pos_terminals, JSON_QUOTE(CAST($idExpr AS CHAR))))";
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
            'active' => ['class' => 'status-active', 'text' => 'Active', 'icon' => ''],
            'offline' => ['class' => 'status-offline', 'text' => 'Offline', 'icon' => ''],
            'maintenance' => ['class' => 'status-pending', 'text' => 'Maintenance', 'icon' => ''],
            'faulty' => ['class' => 'status-offline', 'text' => 'Faulty', 'icon' => ''],
            'decommissioned' => ['class' => 'status-offline', 'text' => 'Decommissioned', 'icon' => ''],
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
