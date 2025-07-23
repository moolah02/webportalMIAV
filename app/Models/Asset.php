<?php

// ==============================================
// 1. ASSET MODEL
// File: app/Models/Asset.php
// ==============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'brand',
        'model',
        'unit_price',
        'currency',
        'stock_quantity',
        'min_stock_level',
        'sku',
        'barcode',
        'specifications',
        'image_url',
        'status',
        'is_requestable',
        'requires_approval',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'specifications' => 'array',
        'is_requestable' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    // Relationships
    public function requestItems()
    {
        return $this->hasMany(AssetRequestItem::class);
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRequestable($query)
    {
        return $query->where('is_requestable', true)->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    // Accessors & Helpers
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->unit_price, 2);
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusBadgeAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'status-offline',
            'low_stock' => 'status-pending',
            'in_stock' => 'status-active',
            default => 'status-active'
        };
    }

    public function canBeRequested($quantity = 1)
    {
        return $this->is_requestable && 
               $this->status === 'active' && 
               $this->stock_quantity >= $quantity;
    }
}
