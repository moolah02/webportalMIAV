<?php

// ==============================================
// 6. REGION MODEL
// File: app/Models/Region.php
// ==============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function posTerminals()
    {
        return $this->hasMany(PosTerminal::class, 'region', 'name');
    }

    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'technician_regions');
    }
}