<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region_code',
        'description',
        'province',
        'country',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    
public function posTerminals()
{
    return $this->hasMany(PosTerminal::class, 'region_id');
}

    // Relationship with Cities (if you have a cities table)
    public function cities()
    {
        return $this->hasMany(City::class, 'region_id');
    }

    // Scope for active regions
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get creator
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // Get updater
    public function updater()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
    
}