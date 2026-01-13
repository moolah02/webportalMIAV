<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetRelationship extends Model
{
    protected $fillable = [
        'parent_asset_id',
        'related_asset_id',
        'relationship_type',
        'metadata',
        'starts_at',
        'expires_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent asset (e.g., Vehicle)
     */
    public function parentAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'parent_asset_id');
    }

    /**
     * Get the related asset (e.g., Insurance License)
     */
    public function relatedAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'related_asset_id');
    }

    /**
     * Scope to get only active relationships
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get expiring relationships (within X days)
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    /**
     * Scope to get expired relationships
     */
    public function scopeExpired($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /**
     * Scope to filter by relationship type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('relationship_type', $type);
    }

    /**
     * Check if relationship is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Check if relationship is expiring soon
     */
    public function isExpiringSoon($days = 30): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        $daysUntil = $this->daysUntilExpiry();
        return $daysUntil !== null && $daysUntil >= 0 && $daysUntil <= $days;
    }

    /**
     * Get human-readable relationship type
     */
    public function getRelationshipTypeLabel(): string
    {
        return match($this->relationship_type) {
            'has_insurance' => 'Has Insurance',
            'has_license' => 'Has License',
            'has_permit' => 'Has Permit',
            'requires' => 'Requires',
            'depends_on' => 'Depends On',
            'linked_to' => 'Linked To',
            'attached_to' => 'Attached To',
            default => ucfirst(str_replace('_', ' ', $this->relationship_type)),
        };
    }
}
