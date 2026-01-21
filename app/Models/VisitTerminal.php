<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitTerminal extends Model
{
    protected $fillable = [
        'visit_id',
        'terminal_id',      // can be int or string; DB column should be string/varchar if you’ll ever send non-numeric IDs
        'status',
        'condition',
        'serial_number',
        'terminal_model',
        'device_type',
        'comments',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}

