<?php

// ==============================================
// 7. POS TERMINAL MODEL
// File: app/Models/PosTerminal.php
// ==============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTerminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'client_id',
        'merchant_name',
        'merchant_contact_person',
        'merchant_phone',
        'merchant_email',
        'physical_address',
        'region',
        'area',
        'business_type',
        'installation_date',
        'terminal_model',
        'serial_number',
        'contract_details',
        'status',
        'last_service_date',
        'next_service_due',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'last_service_date' => 'date',
        'next_service_due' => 'date',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function serviceReports()
    {
        return $this->hasManyThrough(ServiceReport::class, JobAssignment::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    public function scopeFaulty($query)
    {
        return $query->whereIn('status', ['faulty', 'maintenance']);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isFaulty()
    {
        return in_array($this->status, ['faulty', 'maintenance']);
    }

    public function needsService()
    {
        return $this->next_service_due && $this->next_service_due <= now()->addDays(7);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'status-active',
            'offline' => 'status-offline',
            'maintenance' => 'status-pending',
            'faulty' => 'status-offline',
            'decommissioned' => 'status-offline',
        ];

        return $badges[$this->status] ?? 'status-offline';
    }

    public function getLastServiceInfoAttribute()
    {
        if (!$this->last_service_date) {
            return 'Never serviced';
        }

        $daysSince = $this->last_service_date->diffInDays(now());
        return "{$this->last_service_date->format('M d, Y')} ({$daysSince} days ago)";
    }
}