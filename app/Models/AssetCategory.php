<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function assets()
    {
        return $this->hasMany(Asset::class, 'category', 'name');
    }

    /**
     * Get custom fields defined for this category
     */
    public function fields()
    {
        return $this->hasMany(AssetCategoryField::class);
    }

    /**
     * Get active fields in display order
     */
    public function activeFields()
    {
        return $this->hasMany(AssetCategoryField::class)->active()->ordered();
    }

    /**
     * Get fields as array for dynamic form generation
     */
    public function getFieldsArray()
    {
        return $this->activeFields()->get()->map(function($field) {
            return [
                'name' => $field->field_name,
                'label' => $field->field_label,
                'type' => $field->field_type,
                'required' => $field->is_required,
                'placeholder' => $field->placeholder_text,
                'help' => $field->help_text,
                'options' => $field->options,
                'validation' => $field->getValidationString(),
            ];
        })->toArray();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}