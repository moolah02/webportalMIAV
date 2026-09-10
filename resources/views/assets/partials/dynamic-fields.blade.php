{{-- Dynamic Category Fields Partial (styled by the including page: .as-form in create/edit) --}}
<div id="dynamicFieldsSection" class="ui-card" style="display: none;">
    <div class="ui-card-header">
        <h3 id="dynamicFieldsTitle"></h3>
    </div>
    <div class="ui-card-body">
        <div id="dynamicFieldsContent" class="as-grid"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const stockQuantityInput = document.querySelector('input[name="stock_quantity"]');
    const dynamicFieldsSection = document.getElementById('dynamicFieldsSection');
    const dynamicFieldsTitle = document.getElementById('dynamicFieldsTitle');
    const dynamicFieldsContent = document.getElementById('dynamicFieldsContent');

    // Categories that don't typically need Brand and Model
    const categoriesWithoutBrandModel = ['Furniture', 'Software', 'Office Supplies', 'Licenses'];

    // Store existing specification values (for edit mode)
    const existingSpecifications = @json(old('specifications', $asset->specifications ?? []));

    // Load fields when category changes
    if (categorySelect) {
        categorySelect.addEventListener('change', loadCategoryFields);

        // Load on page load if category is already selected
        if (categorySelect.value) {
            loadCategoryFields();
        }
    }

    function loadCategoryFields() {
        const categoryName = categorySelect.value;

        if (!categoryName) {
            dynamicFieldsSection.style.display = 'none';
            dynamicFieldsContent.innerHTML = '';
            toggleBrandModelFields(true);
            return;
        }

        // Toggle Brand/Model visibility based on category
        const shouldHideBrandModel = categoriesWithoutBrandModel.includes(categoryName);
        toggleBrandModelFields(!shouldHideBrandModel);

        // Fetch category fields from API
        fetch(`/api/asset-categories/${encodeURIComponent(categoryName)}/fields`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.fields.length > 0) {
                    renderDynamicFields(data.category, data.fields);
                    updateStockQuantityBehavior(data.category.requires_individual_entry);
                } else {
                    dynamicFieldsSection.style.display = 'none';
                    dynamicFieldsContent.innerHTML = '';
                    updateStockQuantityBehavior(false);
                }
            })
            .catch(error => {
                console.error('Error loading category fields:', error);
                dynamicFieldsSection.style.display = 'none';
            });
    }

    function toggleBrandModelFields(show) {
        const brandField = document.getElementById('brandField');
        const modelField = document.getElementById('modelField');

        if (brandField) {
            brandField.style.display = show ? 'block' : 'none';
        }

        if (modelField) {
            modelField.style.display = show ? 'block' : 'none';
        }
    }

    function renderDynamicFields(category, fields) {
        dynamicFieldsContent.innerHTML = '';
        dynamicFieldsTitle.innerHTML = `${getCategoryIcon(category.name)}${category.name} Details`;
        dynamicFieldsSection.style.display = 'block';
        dynamicFieldsSection.classList.add('category-fields');

        fields.forEach(field => {
            const fieldGroup = createFieldElement(field);
            dynamicFieldsContent.appendChild(fieldGroup);
        });
    }

    function getCategoryIcon(categoryName) {
        const icons = {
            'Vehicles': '',
            'Computer and IT Equipment': '',
            'IT Equipment': '',
            'POS Terminals': '',
            'Licenses': '',
            'Software': '',
            'Furniture': '',
            'Office Supplies': '',
            'Electronics': ''
        };
        return icons[categoryName] || '';
    }

    function createFieldElement(field) {
        const wrapper = document.createElement('div');

        const label = document.createElement('label');
        label.className = 'ui-label';
        label.htmlFor = `spec_${field.name}`;
        label.textContent = field.label;
        if (field.required) {
            label.innerHTML += ' <span class="as-req">*</span>';
        }

        // Get existing value from specifications
        const existingValue = existingSpecifications && existingSpecifications[field.name]
            ? existingSpecifications[field.name]
            : '';

        let input;
        const fieldId = `spec_${field.name}`;
        const fieldInputName = `specifications[${field.name}]`;

        switch (field.type) {
            case 'text':
            case 'email':
            case 'url':
            case 'tel':
                input = document.createElement('input');
                input.type = field.type;
                input.className = 'ui-input w-full';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                break;

            case 'number':
                input = document.createElement('input');
                input.type = 'number';
                input.className = 'ui-input w-full';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                break;

            case 'date':
                input = document.createElement('input');
                input.type = 'date';
                input.className = 'ui-input w-full';
                input.id = fieldId;
                input.name = fieldInputName;
                input.value = existingValue;
                if (field.required) input.required = true;
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'ui-textarea w-full';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.rows = 3;
                input.value = existingValue;
                if (field.required) input.required = true;
                break;

            case 'select':
                input = document.createElement('select');
                input.className = 'ui-select w-full';
                input.id = fieldId;
                input.name = fieldInputName;
                if (field.required) input.required = true;

                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = '-- Select --';
                input.appendChild(emptyOption);

                if (field.options && Array.isArray(field.options)) {
                    field.options.forEach(option => {
                        const optionEl = document.createElement('option');
                        optionEl.value = option;
                        optionEl.textContent = option;
                        if (option === existingValue) {
                            optionEl.selected = true;
                        }
                        input.appendChild(optionEl);
                    });
                }
                break;

            default:
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'ui-input w-full';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
        }

        wrapper.appendChild(label);
        wrapper.appendChild(input);

        if (field.help) {
            const helpText = document.createElement('div');
            helpText.className = 'as-hint';
            helpText.textContent = field.help;
            wrapper.appendChild(helpText);
        }

        return wrapper;
    }

    function updateStockQuantityBehavior(requiresIndividualEntry) {
        if (!stockQuantityInput) return;

        const existingNotice = document.getElementById('individualEntryNotice');

        if (requiresIndividualEntry) {
            stockQuantityInput.value = 1;
            stockQuantityInput.readOnly = true;
            stockQuantityInput.style.backgroundColor = 'var(--mv-surface-2)';

            if (!existingNotice) {
                const notice = document.createElement('div');
                notice.id = 'individualEntryNotice';
                notice.className = 'as-note';
                notice.innerHTML = '<strong>Note:</strong> This category requires individual entry. Each item must be added separately with unique identifiers.';
                stockQuantityInput.parentElement.appendChild(notice);
            }
        } else {
            stockQuantityInput.readOnly = false;
            stockQuantityInput.style.backgroundColor = '';

            if (existingNotice) {
                existingNotice.remove();
            }
        }
    }
});
</script>
