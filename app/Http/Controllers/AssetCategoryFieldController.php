<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\AssetCategoryField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetCategoryFieldController extends Controller
{
    /**
     * Display all fields for a category
     * GET /settings/asset-categories/{category}/fields
     */
    public function index(AssetCategory $category)
    {
        $this->authorize('update', $category);

        $fields = $category->fields()->ordered()->get();

        return view('settings.asset-category-fields.index', compact('category', 'fields'));
    }

    /**
     * Store a new field
     * POST /settings/asset-categories/{category}/fields
     */
    public function store(Request $request, AssetCategory $category)
    {
        $this->authorize('update', $category);

        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|regex:/^[a-z_]+$/|unique:asset_category_fields,field_name,NULL,id,asset_category_id,' . $category->id,
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,number,date,select,textarea,email,url,tel',
            'is_required' => 'boolean',
            'placeholder_text' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'options' => 'nullable|array',
            'options.*' => 'string|max:100',
            'validation_rules' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $maxOrder = $category->fields()->max('display_order') ?? 0;

        $field = new AssetCategoryField([
            'asset_category_id' => $category->id,
            'field_name' => $request->field_name,
            'field_label' => $request->field_label,
            'field_type' => $request->field_type,
            'is_required' => $request->boolean('is_required'),
            'placeholder_text' => $request->placeholder_text,
            'help_text' => $request->help_text,
            'options' => $request->field_type === 'select' ? $request->options : null,
            'validation_rules' => $request->validation_rules,
            'display_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $field->save();

        return back()->with('success', "Field '{$field->field_label}' created successfully.");
    }

    /**
     * Update a field
     * PUT /settings/asset-categories/{category}/fields/{field}
     */
    public function update(Request $request, AssetCategory $category, AssetCategoryField $field)
    {
        $this->authorize('update', $category);

        if ($field->asset_category_id !== $category->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,number,date,select,textarea,email,url,tel',
            'is_required' => 'boolean',
            'placeholder_text' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'options' => 'nullable|array',
            'options.*' => 'string|max:100',
            'validation_rules' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $field->update([
            'field_label' => $request->field_label,
            'field_type' => $request->field_type,
            'is_required' => $request->boolean('is_required'),
            'placeholder_text' => $request->placeholder_text,
            'help_text' => $request->help_text,
            'options' => $request->field_type === 'select' ? $request->options : null,
            'validation_rules' => $request->validation_rules,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Field '{$field->field_label}' updated successfully.");
    }

    /**
     * Delete a field
     * DELETE /settings/asset-categories/{category}/fields/{field}
     */
    public function destroy(Request $request, AssetCategory $category, AssetCategoryField $field)
    {
        $this->authorize('update', $category);

        if ($field->asset_category_id !== $category->id) {
            abort(404);
        }

        $fieldLabel = $field->field_label;
        $field->delete();

        return back()->with('success', "Field '{$fieldLabel}' deleted successfully.");
    }

    /**
     * Reorder fields via AJAX
     * POST /settings/asset-categories/{category}/fields/reorder
     */
    public function reorder(Request $request, AssetCategory $category)
    {
        $this->authorize('update', $category);

        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->order as $index => $fieldId) {
            AssetCategoryField::where('id', $fieldId)
                ->where('asset_category_id', $category->id)
                ->update(['display_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Fields reordered successfully.']);
    }

    /**
     * Get fields as JSON for AJAX form loading
     * GET /api/asset-categories/{category}/fields
     */
    public function getFieldsJson(AssetCategory $category)
    {
        $fields = $category->activeFields()->get()->map(function ($field) {
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
        });

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'requires_individual_entry' => $category->requires_individual_entry,
            ],
            'fields' => $fields,
        ]);
    }
}
