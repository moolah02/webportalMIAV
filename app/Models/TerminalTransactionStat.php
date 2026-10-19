<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transaction activity for one device over one period, as supplied by the
 * client/processor and uploaded on the portal. The portal never generates
 * these figures itself.
 */
class TerminalTransactionStat extends Model
{
    protected $fillable = [
        'pos_terminal_id',
        'terminal_code',
        'period_start',
        'period_end',
        'transactions_count',
        'transactions_value',
        'target_count',
        'source_file',
        'imported_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'transactions_count' => 'integer',
        'transactions_value' => 'decimal:2',
        'target_count' => 'integer',
    ];

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'imported_by');
    }
}
