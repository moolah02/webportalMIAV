<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One spell of a device standing at one site.
 *
 * The open row (ended_at null) is where the device is now; the closed ones are
 * where it has been. The site is snapshotted rather than joined, so a client
 * renamed or a merchant removed later does not rewrite history.
 */
class TerminalSiteAssignment extends Model
{
    public const ENDED_COLLECTED = 'collected';
    public const ENDED_MIGRATED = 'migrated';
    public const ENDED_RETIRED = 'retired';

    /** How a spell at a site ended, for reading. */
    public const ENDED_REASONS = [
        self::ENDED_COLLECTED => 'Collected into stock',
        self::ENDED_MIGRATED => 'Replaced by another device',
        self::ENDED_RETIRED => 'Taken out of service',
    ];

    protected $fillable = [
        'pos_terminal_id',
        'client_id',
        'merchant_id',
        'merchant_name',
        'physical_address',
        'city',
        'region',
        'province',
        'area',
        'started_at',
        'ended_at',
        'ended_reason',
        'replaced_by_terminal_id',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'replaced_by_terminal_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    /** Is the device still at this site? */
    public function isOpen(): bool
    {
        return $this->ended_at === null;
    }

    public function endedReasonLabel(): ?string
    {
        return $this->ended_reason
            ? (self::ENDED_REASONS[$this->ended_reason] ?? $this->ended_reason)
            : null;
    }

    /** The site in one line, for a history row. */
    public function siteLabel(): string
    {
        $parts = array_filter([
            $this->merchant_name,
            $this->city,
            $this->region,
        ], fn ($p) => trim((string) $p) !== '');

        return $parts === [] ? 'Site not recorded' : implode(' · ', $parts);
    }
}
