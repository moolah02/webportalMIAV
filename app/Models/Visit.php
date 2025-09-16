<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'visits';

    protected $fillable = [
        'merchant_id',
        'merchant_name',
        'employee_id',
        'assignment_id',
        'completed_at',
        'contact_person',
        'phone_number',
        'visit_summary',
        'action_points',
        'evidence',
        'signature',
        'other_terminals_found',
        'terminal', // singular JSON snapshot
    ];

    protected $casts = [
        'completed_at'          => 'datetime',
        'terminal'              => 'array',   // <-- singular
        'evidence'              => 'array',
        'other_terminals_found' => 'array',


    ];

    public function visitTerminals()
    {
        return $this->hasMany(VisitTerminal::class);
    }


    public function hasTerminal(): bool
{
    return !empty($this->terminal) && !empty($this->terminal['terminal_id']);
}

public function getTerminalId(): ?string
{
    return $this->terminal['terminal_id'] ?? null;
}
    // Convenience when storing only one terminal per visit
    public function visitTerminal()
    {
        return $this->hasOne(VisitTerminal::class);
    }

    public function getCompletedAtLocalAttribute()
    {
        return optional($this->completed_at)?->timezone(config('app.timezone'));
    }

    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('completed_at', [$from, $to]);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('merchant_name', 'like', "%{$term}%")
              ->orWhere('visit_summary', 'like', "%{$term}%")
              ->orWhere('action_points', 'like', "%{$term}%");
        });
    }

    // --- JSON accessors (singular) ---
    public function getTerminalAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getEvidenceAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getOtherTerminalsFoundAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    // If you still need this helper, keep it; it expects "terminals" JSON (plural).
    // Given you now store SINGLE terminal, you can update or remove this.
    public function relatedClients()
    {
        $terminalIds = collect([$this->terminal['terminal_id'] ?? null])->filter();

        return \App\Models\Client::whereIn('id', function ($q) use ($terminalIds) {
            $q->select('client_id')
              ->from('pos_terminals')
              ->whereIn('terminal_id', $terminalIds);
        })->get();
    }
    public function getCompleteTerminalInfo(): ?array
{
    if (empty($this->terminal) || !is_array($this->terminal)) {
        return null; // No data to display
    }

    $data = $this->terminal; // JSON column already cast to array

    // Try to fetch extra info from pos_terminals table if terminal_id exists
    if (!empty($data['terminal_id'])) {
        $posTerminal = DB::table('pos_terminals')
            ->where('id', $data['terminal_id'])  // use 'id' if that's the real column
    ->first();;

        if ($posTerminal) {
            $data = array_merge($data, [
                'found_in_pos_terminals' => true,
                'terminal_model' => $posTerminal->model ?? null,
                'condition_status' => $posTerminal->condition ?? null,
                'serial_number' => $posTerminal->serial_number ?? null,
                'last_service_date' => $posTerminal->last_service_date ?? null,
                'next_service_due' => $posTerminal->next_service_due ?? null,
            ]);
        } else {
            $data['found_in_pos_terminals'] = false;
        }
    }

    return $data;
}

}

