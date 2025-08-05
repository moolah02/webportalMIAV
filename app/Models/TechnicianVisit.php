<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'technician_id',
        'pos_terminal_id',
        'client_id',
        'visit_date',
        'asset_type',
        'asset_id',
        'terminal_status',
        'technician_feedback',
        'comments',
        'photos',
        'duration_minutes',
        'issues_found',
        'merchant_feedback',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'photos' => 'array',
        'issues_found' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

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

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'visit_id');
    }

    // Generate unique visit ID
    public static function generateVisitId()
    {
        $prefix = 'VIS-' . date('Ymd') . '-';
        $lastVisit = self::where('visit_id', 'like', $prefix . '%')
            ->orderBy('visit_id', 'desc')
            ->first();
        
        if ($lastVisit) {
            $lastNumber = intval(substr($lastVisit->visit_id, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return $prefix . $newNumber;
    }
}