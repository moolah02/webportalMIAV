<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A contractual service report for one period: the narrative the client reads
 * (summary, recommendations, actions required from them) plus a snapshot of
 * the figures taken when it was issued, so a sent report can be reproduced.
 */
class ContractReport extends Model
{
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_MONTHLY = 'monthly';

    protected $fillable = [
        'type',
        'period_start',
        'period_end',
        'client_id',
        'status',
        'executive_summary',
        'recommendations',
        'client_actions',
        'payload',
        'prepared_by',
        'approved_by_name',
        'approved_at',
        'issued_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payload' => 'array',
        'approved_at' => 'datetime',
        'issued_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }
}
