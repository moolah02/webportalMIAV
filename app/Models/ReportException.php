<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Why a device was not visited, or not activated, during a reporting period.
 * Recorded by the office; the contractual reports show it against the device
 * together with the person the issue sits with.
 */
class ReportException extends Model
{
    public const TYPE_NON_VISIT = 'non_visit';
    public const TYPE_NON_ACTIVATION = 'non_activation';

    /** Reason codes offered on the report screen, in display order. */
    public const REASON_CODES = [
        'shop_closed' => 'Shop closed on arrival',
        'merchant_relocated' => 'Merchant relocated',
        'merchant_not_trading' => 'Merchant no longer trading',
        'access_denied' => 'Access denied on site',
        'device_removed' => 'Device removed from site',
        'device_not_found' => 'Device not found on site',
        'device_faulty' => 'Device faulty — awaiting repair or swap',
        'no_connectivity' => 'No network connectivity at site',
        'no_stock' => 'No stock / consumables unavailable',
        'security_risk' => 'Site unsafe to visit',
        'weather_access' => 'Weather or road access',
        'rescheduled' => 'Visit rescheduled to next period',
        'not_scheduled' => 'No visit scheduled in period',
        'awaiting_client' => 'Awaiting instruction from the client',
        'other' => 'Other (see note)',
    ];

    protected $fillable = [
        'pos_terminal_id',
        'period_start',
        'period_end',
        'type',
        'reason_code',
        'reason_note',
        'escalation_owner_id',
        'status',
        'resolved_at',
        'recorded_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'resolved_at' => 'datetime',
    ];

    public static function reasonLabel(?string $code): ?string
    {
        return $code ? (self::REASON_CODES[$code] ?? $code) : null;
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'escalation_owner_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }
}
