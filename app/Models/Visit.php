<?php

namespace App\Models;

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
}

