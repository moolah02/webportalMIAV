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
        'requires_individual_entry',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_individual_entry' => 'boolean',
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

    /**
     * Get validation rules array for this category's fields
     * Returns Laravel validation rules keyed by field name
     */
    public function getValidationRulesArray(): array
    {
        $rules = [];
        foreach ($this->activeFields as $field) {
            $rules['specifications.' . $field->field_name] = $field->getValidationString();
        }
        return $rules;
    }

    /**
     * Get validation attributes for error messages
     * Maps field names to human-readable labels
     */
    public function getValidationAttributes(): array
    {
        $attributes = [];
        foreach ($this->activeFields as $field) {
            $attributes['specifications.' . $field->field_name] = $field->field_label;
        }
        return $attributes;
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
