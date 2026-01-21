@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>{{ $category->name }} - Custom Fields</h2>
            <p class="text-muted">Manage dynamic fields for {{ $category->name }} category</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('settings.index') }}" class="btn btn-secondary">Back</a>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFieldModal">
                <i class="fas fa-plus"></i> Add Field
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                @if ($category->requires_individual_entry)
                    <span class="badge badge-info">Individual Entry Required</span>
                @endif
                Category Fields ({{ $fields->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if ($fields->isEmpty())
                <div class="alert alert-info">
                    No custom fields defined for this category yet. Click "Add Field" to create one.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="fieldsTable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Order</th>
                                <th>Field Name</th>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Active</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="fieldsSortable">
                            @foreach ($fields as $field)
                                <tr data-field-id="{{ $field->id }}" class="field-row">
                                    <td class="handle text-center" style="cursor: move;">
                                        <i class="fas fa-bars"></i>
                                    </td>
                                    <td><code>{{ $field->field_name }}</code></td>
                                    <td>{{ $field->field_label }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $field->field_type }}</span>
                                    </td>
                                    <td>
                                        @if ($field->is_required)
                                            <span class="badge badge-danger">Required</span>
                                        @else
                                            <span class="badge badge-light">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($field->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-field" data-field="{{ $field }}" data-toggle="modal" data-target="#editFieldModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('asset-category-fields.destroy', [$category, $field]) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this field?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <small class="text-muted">Drag rows to reorder fields</small>
            @endif
        </div>
    </div>
</div>

<!-- Add Field Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('asset-category-fields.store', $category) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="field_name">Field Name (snake_case)*</label>
                        <input type="text" class="form-control @error('field_name') is-invalid @enderror"
                               id="field_name" name="field_name" required placeholder="e.g., license_plate, vin"
                               value="{{ old('field_name') }}">
                        @error('field_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">Lowercase letters, numbers, and underscores only</small>
                    </div>

                    <div class="form-group">
                        <label for="field_label">Field Label*</label>
                        <input type="text" class="form-control @error('field_label') is-invalid @enderror"
                               id="field_label" name="field_label" required placeholder="e.g., License Plate"
                               value="{{ old('field_label') }}">
                        @error('field_label')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="field_type">Field Type*</label>
                        <select class="form-control @error('field_type') is-invalid @enderror" id="field_type" name="field_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="email">Email</option>
                            <option value="url">URL</option>
                            <option value="tel">Phone</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select Dropdown</option>
                        </select>
                        @error('field_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" id="optionsGroup" style="display:none;">
                        <label for="options">Options (one per line)*</label>
                        <textarea class="form-control" id="options" name="options[]" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                        <small class="form-text text-muted">For select fields only</small>
                    </div>

                    <div class="form-group">
                        <label for="placeholder_text">Placeholder Text</label>
                        <input type="text" class="form-control" id="placeholder_text" name="placeholder_text"
                               placeholder="e.g., Enter license plate" value="{{ old('placeholder_text') }}">
                    </div>

                    <div class="form-group">
                        <label for="help_text">Help Text</label>
                        <textarea class="form-control" id="help_text" name="help_text" rows="2" placeholder="Additional guidance for users">{{ old('help_text') }}</textarea>
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_required" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_required">Required Field</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Field</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div class="modal fade" id="editFieldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFieldForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_field_label">Field Label*</label>
                        <input type="text" class="form-control" id="edit_field_label" name="field_label" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_field_type">Field Type*</label>
                        <select class="form-control" id="edit_field_type" name="field_type" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="email">Email</option>
                            <option value="url">URL</option>
                            <option value="tel">Phone</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select Dropdown</option>
                        </select>
                    </div>

                    <div class="form-group" id="edit_optionsGroup" style="display:none;">
                        <label for="edit_options">Options (one per line)*</label>
                        <textarea class="form-control" id="edit_options" name="options[]" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_placeholder_text">Placeholder Text</label>
                        <input type="text" class="form-control" id="edit_placeholder_text" name="placeholder_text">
                    </div>

                    <div class="form-group">
                        <label for="edit_help_text">Help Text</label>
                        <textarea class="form-control" id="edit_help_text" name="help_text" rows="2"></textarea>
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="edit_is_required" name="is_required" value="1">
                        <label class="custom-control-label" for="edit_is_required">Required Field</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active" value="1" checked>
                        <label class="custom-control-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Field</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Show/hide options textarea based on field type
    $('#field_type').on('change', function() {
        $('#optionsGroup').toggle($(this).val() === 'select');
    });

    $('#edit_field_type').on('change', function() {
        $('#edit_optionsGroup').toggle($(this).val() === 'select');
    });

    // Make table sortable
    $('#fieldsSortable').sortable({
        handle: '.handle',
        placeholder: 'ui-state-highlight',
        stop: function() {
            saveFieldOrder();
        }
    });

    // Edit button click handler
    $(document).on('click', '.edit-field', function() {
        const fieldStr = $(this).data('field');
        const field = typeof fieldStr === 'string' ? JSON.parse(fieldStr) : fieldStr;

        $('#editFieldForm').attr('action', '{{ route("asset-category-fields.update", [$category, ":fieldId"]) }}'.replace(':fieldId', field.id));

        $('#edit_field_label').val(field.field_label);
        $('#edit_field_type').val(field.field_type);
        $('#edit_placeholder_text').val(field.placeholder_text || '');
        $('#edit_help_text').val(field.help_text || '');
        $('#edit_is_required').prop('checked', field.is_required);
        $('#edit_is_active').prop('checked', field.is_active);

        // Handle options for select fields
        if (field.field_type === 'select' && field.options) {
            const optionsText = Array.isArray(field.options) ? field.options.join('\n') : field.options;
            $('#edit_options').val(optionsText);
            $('#edit_optionsGroup').show();
        } else {
            $('#edit_optionsGroup').hide();
        }
    });

    // Save field order
    function saveFieldOrder() {
        const order = [];
        $('#fieldsSortable tr').each(function() {
            order.push($(this).data('field-id'));
        });

        $.ajax({
            url: '{{ route("asset-category-fields.reorder", $category) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order: order
            },
            success: function(response) {
                if (response.success) {
                    // Show toast or notification
                    console.log('Fields reordered successfully');
                }
            },
            error: function(xhr) {
                alert('Error reordering fields');
            }
        });
    }
});
</script>

<style>
    .ui-sortable-handle {
        cursor: move;
    }

    .ui-state-highlight {
        background-color: #f5f5f5;
        border: 2px dashed #ccc;
    }
</style>
@endsection
