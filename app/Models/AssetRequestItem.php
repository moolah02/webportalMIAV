<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_request_id',
        'asset_id',
        'quantity_requested',
        'quantity_approved',
        'quantity_fulfilled',
        'unit_price_at_request',
        'total_price',
        'special_requirements',
        'item_status',
        'notes',
    ];

    protected $casts = [
        'unit_price_at_request' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function assetRequest()
    {
        return $this->belongsTo(AssetRequest::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Accessors & Helpers
    public function getStatusBadgeAttribute()
    {
        return match($this->item_status) {
            'pending' => 'status-pending',
            'approved' => 'status-active',
            'partially_approved' => 'status-pending',
            'rejected' => 'status-offline',
            'fulfilled' => 'status-active',
            default => 'status-pending'
        };
    }

    // Auto-calculate total price
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->quantity_requested * $item->unit_price_at_request;
        });

        static::saved(function ($item) {
            // Recalculate request total when item is saved
            $item->assetRequest->recalculateTotal();
        });
    }
}