<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetCategoryField extends Model
{
    protected $fillable = [
        'asset_category_id',
        'field_name',
        'field_label',
        'field_type',
        'is_required',
        'validation_rules',
        'options',
        'placeholder_text',
        'help_text',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'validation_rules' => 'array',
        'options' => 'array',
        'display_order' => 'integer',
    ];

    /**
     * Get the category this field belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    /**
     * Scope to get only active fields
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get fields in display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('field_label');
    }

    /**
     * Get validation rules as Laravel validation string
     */
    public function getValidationString(): string
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add field type validation
        switch ($this->field_type) {
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'tel':
                $rules[] = 'string';
                break;
            default:
                $rules[] = 'string';
        }

        // Add custom validation rules
        if ($this->validation_rules) {
            if (isset($this->validation_rules['min'])) {
                $rules[] = 'min:' . $this->validation_rules['min'];
            }
            if (isset($this->validation_rules['max'])) {
                $rules[] = 'max:' . $this->validation_rules['max'];
            }
            if (isset($this->validation_rules['regex'])) {
                $rules[] = 'regex:' . $this->validation_rules['regex'];
            }
        }

        // For select fields, validate against options
        if ($this->field_type === 'select' && $this->options) {
            $rules[] = 'in:' . implode(',', $this->options);
        }

        return implode('|', $rules);
    }

    /**
     * Generate HTML input element for this field
     * Used for dynamic form rendering
     */
    public function renderInputHtml(string $value = '', string $namePrefix = 'specifications'): string
    {
        $fieldName = "{$namePrefix}[{$this->field_name}]";
        $fieldId = "field_{$this->field_name}";
        $required = $this->is_required ? 'required' : '';
        $placeholder = $this->placeholder_text ? "placeholder=\"{$this->placeholder_text}\"" : '';
        $help = $this->help_text ? "<small class='form-text text-muted'>{$this->help_text}</small>" : '';

        $input = '';

        switch ($this->field_type) {
            case 'text':
                $input = "<input type='text' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$placeholder} {$required}>";
                break;
            case 'email':
                $input = "<input type='email' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$placeholder} {$required}>";
                break;
            case 'url':
                $input = "<input type='url' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$placeholder} {$required}>";
                break;
            case 'tel':
                $input = "<input type='tel' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$placeholder} {$required}>";
                break;
            case 'number':
                $input = "<input type='number' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$placeholder} {$required}>";
                break;
            case 'date':
                $input = "<input type='date' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$required}>";
                break;
            case 'textarea':
                $input = "<textarea class='form-control' id='{$fieldId}' name='{$fieldName}' {$placeholder} {$required} rows='3'>{$value}</textarea>";
                break;
            case 'select':
                $options = $this->options ? $this->options : [];
                $optionsHtml = implode('', array_map(function($opt) use ($value) {
                    $selected = $opt === $value ? 'selected' : '';
                    return "<option value='{$opt}' {$selected}>{$opt}</option>";
                }, $options));
                $input = "<select class='form-control' id='{$fieldId}' name='{$fieldName}' {$required}><option value=''>-- Select --</option>{$optionsHtml}</select>";
                break;
            default:
                $input = "<input type='text' class='form-control' id='{$fieldId}' name='{$fieldName}' value='{$value}' {$placeholder} {$required}>";
        }

        return $input . $help;
    }
