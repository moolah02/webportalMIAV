<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\AssetCategoryField;
use Illuminate\Http\Request;

class AssetCategoryFieldController extends Controller
{
    /**
     * Display fields for a category
     */
    public function index(AssetCategory $category)
    {
        $fields = $category->fields()->ordered()->get();
        $categories = AssetCategory::active()->ordered()->get();

        return view('settings.asset-category-fields.index', compact('category', 'fields', 'categories'));
    }

    /**
     * Store a new field for a category
     */
    public function store(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'field_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                'unique:asset_category_fields,field_name,NULL,id,asset_category_id,' . $category->id,
            ],
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,number,date,select,textarea,email,url,tel',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'placeholder_text' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'validation_rules' => 'nullable|array',
            'validation_rules.min' => 'nullable|numeric',
            'validation_rules.max' => 'nullable|numeric',
        ]);

        $maxOrder = $category->fields()->max('display_order') ?? 0;

        $category->fields()->create([
            'field_name' => $validated['field_name'],
            'field_label' => $validated['field_label'],
            'field_type' => $validated['field_type'],
            'is_required' => $validated['is_required'] ?? false,
            'options' => $validated['field_type'] === 'select' ? ($validated['options'] ?? null) : null,
            'placeholder_text' => $validated['placeholder_text'] ?? null,
            'help_text' => $validated['help_text'] ?? null,
            'validation_rules' => $validated['validation_rules'] ?? null,
            'display_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Field added successfully.');
    }

    /**
     * Update a field
     */
    public function update(Request $request, AssetCategoryField $field)
    {
        $validated = $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,number,date,select,textarea,email,url,tel',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'placeholder_text' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'validation_rules' => 'nullable|array',
            'validation_rules.min' => 'nullable|numeric',
            'validation_rules.max' => 'nullable|numeric',
        ]);

        $field->update([
            'field_label' => $validated['field_label'],
            'field_type' => $validated['field_type'],
            'is_required' => $validated['is_required'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'options' => $validated['field_type'] === 'select' ? ($validated['options'] ?? null) : null,
            'placeholder_text' => $validated['placeholder_text'] ?? null,
            'help_text' => $validated['help_text'] ?? null,
            'validation_rules' => $validated['validation_rules'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Field updated successfully.');
    }

    /**
     * Delete a field
     */
    public function destroy(AssetCategoryField $field)
    {
        $field->delete();
        return redirect()->back()->with('success', 'Field deleted successfully.');
    }

    /**
     * Reorder fields via AJAX
     */
    public function reorder(Request $request, AssetCategory $category)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*' => 'exists:asset_category_fields,id'
        ]);

        foreach ($request->fields as $index => $fieldId) {
            AssetCategoryField::where('id', $fieldId)
                ->where('asset_category_id', $category->id)
                ->update(['display_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get fields for AJAX (dynamic form loading)
     */
    public function getFieldsJson(AssetCategory $category)
    {
        return response()->json([
            'fields' => $category->getFieldsArray(),
            'requires_individual_entry' => $category->requires_individual_entry,
        ]);
    }

    /**
     * Update category settings (like requires_individual_entry)
     */
    public function updateCategory(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'requires_individual_entry' => 'boolean',
        ]);

        $category->update([
            'requires_individual_entry' => $validated['requires_individual_entry'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Category settings updated.');
    }
}
