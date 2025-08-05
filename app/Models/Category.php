<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    // Category types constants
    const TYPE_ASSET_CATEGORY = 'asset_category';
    const TYPE_ASSET_STATUS = 'asset_status';
    const TYPE_TERMINAL_STATUS = 'terminal_status';
    const TYPE_SERVICE_TYPE = 'service_type';

    public static function getTypes()
    {
        return [
            self::TYPE_ASSET_CATEGORY => 'Asset Categories',
            self::TYPE_ASSET_STATUS => 'Asset Status',
            self::TYPE_TERMINAL_STATUS => 'POS Terminal Status',
            self::TYPE_SERVICE_TYPE => 'Service Types',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods to get categories for dropdowns
    public static function getAssetCategories()
    {
        return self::ofType(self::TYPE_ASSET_CATEGORY)->active()->ordered()->pluck('name', 'slug');
    }

    public static function getAssetStatuses()
    {
        return self::ofType(self::TYPE_ASSET_STATUS)->active()->ordered()->pluck('name', 'slug');
    }

    public static function getTerminalStatuses()
    {
        return self::ofType(self::TYPE_TERMINAL_STATUS)->active()->ordered()->pluck('name', 'slug');
    }

    public static function getServiceTypes()
    {
        return self::ofType(self::TYPE_SERVICE_TYPE)->active()->ordered()->pluck('name', 'slug');
    }

    // NEW: Get categories with full details (not just name/slug)
    public static function getTerminalStatusesWithDetails()
    {
        return self::ofType(self::TYPE_TERMINAL_STATUS)->active()->ordered()->get();
    }

    public static function getServiceTypesWithDetails()
    {
        return self::ofType(self::TYPE_SERVICE_TYPE)->active()->ordered()->get();
    }

    // NEW: Get formatted options for select dropdowns
    public static function getSelectOptions($type)
    {
        return self::where('type', $type)
                   ->where('is_active', true)
                   ->orderBy('sort_order')
                   ->pluck('name', 'slug');
    }

    // NEW: Get category by slug and type
    public static function findBySlugAndType($slug, $type)
    {
        return self::where('slug', $slug)
                   ->where('type', $type)
                   ->where('is_active', true)
                   ->first();
    }

    // NEW: Get formatted badge data for display
    public function getBadgeData()
    {
        return [
            'class' => 'status-badge',
            'style' => "background-color: {$this->color}15; color: {$this->color}; border: 1px solid {$this->color}40; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;",
            'icon' => $this->icon,
            'text' => $this->name,
            'color' => $this->color
        ];
    }

    // NEW: Relationship for POS Terminals
    public function posTerminals()
    {
        return $this->hasMany(PosTerminal::class, 'status', 'slug');
    }

    // NEW: Relationship for Assets (if you use categories for assets too)
    public function assets()
    {
        return $this->hasMany(Asset::class, 'category', 'name');
    }
}