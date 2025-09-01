<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'tags',
        'is_global',
        'created_by',
        'payload'
    ];

    protected $casts = [
        'tags' => 'array',
        'payload' => 'array',
        'is_global' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ReportRun::class, 'template_id');
    }
}
