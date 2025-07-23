<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_code',
        'company_name', 
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'region',
        'status',
        'contract_start_date',
        'contract_end_date',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    // Accessors for status badges (matching your existing status values)
    public function getStatusBadgeAttribute()
    {
        return match(strtolower($this->status)) {
            'active' => 'status-active',
            'inactive' => 'status-inactive', 
            'prospect' => 'status-prospect',
            'lost' => 'status-lost',
            default => 'status-prospect'
        };
    }

    // Check if contract is active
    public function getIsContractActiveAttribute()
    {
        if (!$this->contract_start_date || !$this->contract_end_date) {
            return false;
        }
        
        $now = now();
        return $now->between($this->contract_start_date, $this->contract_end_date);
    }

    // Check if contract is expiring soon (within 30 days)
    public function getIsContractExpiringSoonAttribute()
    {
        if (!$this->contract_end_date) {
            return false;
        }
        
        return $this->contract_end_date->diffInDays(now()) <= 30 && $this->contract_end_date->isFuture();
    }

    // Get full address
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->region
        ]);
        
        return implode(', ', $parts);
    }

    // Relationships (add these when you create related models)
    public function projects()
    {
        return $this->hasMany(Project::class);
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

    public function scopeProspect($query)
    {
        return $query->where('status', 'prospect');
    }

    public function scopeWithActiveContract($query)
    {
        return $query->whereNotNull('contract_start_date')
            ->whereNotNull('contract_end_date')
            ->where('contract_start_date', '<=', now())
            ->where('contract_end_date', '>=', now());
    }
public function posTerminals()
    {
        return $this->hasMany(PosTerminal::class);
    }

    
    public function scopeContractExpiring($query, $days = 30)
    {
        return $query->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [
                now(),
                now()->addDays($days)
            ]);
    }
}