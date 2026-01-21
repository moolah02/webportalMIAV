<!-- Dynamic Category Fields Partial -->
<div id="categoryFieldsContainer" style="display:none;">
    <div class="form-group">
        <div id="dynamicFieldsContent"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const stockQuantityInput = document.getElementById('stock_quantity');
    const dynamicFieldsContainer = document.getElementById('categoryFieldsContainer');
    const dynamicFieldsContent = document.getElementById('dynamicFieldsContent');

    // Categories that don't typically need Brand and Model
    const categoriesWithoutBrandModel = ['Furniture', 'Software', 'Office Supplies', 'Licenses'];

    // Load fields when category changes
    if (categorySelect) {
        categorySelect.addEventListener('change', loadCategoryFields);

        // Load on page load if category is already selected (edit mode)
        if (categorySelect.value) {
            loadCategoryFields();
        }
    }

    function loadCategoryFields() {
        const categoryName = categorySelect.value;

        if (!categoryName) {
            dynamicFieldsContainer.style.display = 'none';
            dynamicFieldsContent.innerHTML = '';
            toggleBrandModelFields(true); // Show them by default
            return;
        }

        // Toggle Brand/Model visibility based on category
        const shouldHideBrandModel = categoriesWithoutBrandModel.includes(categoryName);
        toggleBrandModelFields(!shouldHideBrandModel);

        fetch(`/api/asset-categories/${categoryName}/fields`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderDynamicFields(data.category, data.fields);
                    updateStockQuantityBehavior(data.category.requires_individual_entry);
                }
            })
            .catch(error => console.error('Error loading fields:', error));
    }

    function toggleBrandModelFields(show) {
        // Find Brand and Model field containers by ID
        const brandField = document.getElementById('brandField');
        const modelField = document.getElementById('modelField');

        if (brandField) {
            brandField.style.display = show ? 'block' : 'none';
            const brandInput = brandField.querySelector('input[name="brand"]');
            if (brandInput) {
                if (!show) brandInput.removeAttribute('required');
            }
        }

        if (modelField) {
            modelField.style.display = show ? 'block' : 'none';
            const modelInput = modelField.querySelector('input[name="model"]');
            if (modelInput) {
                if (!show) modelInput.removeAttribute('required');
            }
        }
    }
        }

        dynamicFieldsContainer.style.display = 'block';

        fields.forEach(field => {
            const fieldGroup = createFieldElement(field);
            dynamicFieldsContent.appendChild(fieldGroup);
        });
    }

    function createFieldElement(field) {
        const wrapper = document.createElement('div');
        wrapper.className = 'form-group';

        const label = document.createElement('label');
        label.htmlFor = `field_${field.name}`;
        label.textContent = field.label;
        if (field.required) {
            label.innerHTML += '<span class="text-danger">*</span>';
        }

        const inputContainer = document.createElement('div');

        let input;
        const fieldId = `field_${field.name}`;
        const fieldInputName = `specifications[${field.name}]`;
        const existingValue = document.querySelector(`[name="${fieldInputName}"]`)?.value || '';

        switch (field.type) {
            case 'text':
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'email':
                input = document.createElement('input');
                input.type = 'email';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'url':
                input = document.createElement('input');
                input.type = 'url';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'tel':
                input = document.createElement('input');
                input.type = 'tel';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'number':
                input = document.createElement('input');
                input.type = 'number';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'date':
                input = document.createElement('input');
                input.type = 'date';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.rows = '3';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
                break;

            case 'select':
                input = document.createElement('select');
                input.className = 'form-control';
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

                inputContainer.appendChild(input);
                break;

            default:
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldInputName;
                input.placeholder = field.placeholder || '';
                input.value = existingValue;
                if (field.required) input.required = true;
                inputContainer.appendChild(input);
        }

        wrapper.appendChild(label);
        wrapper.appendChild(inputContainer);

        if (field.help) {
            const helpText = document.createElement('small');
            helpText.className = 'form-text text-muted';
            helpText.textContent = field.help;
            wrapper.appendChild(helpText);
        }

        return wrapper;
    }

    function updateStockQuantityBehavior(requiresIndividualEntry) {
        if (requiresIndividualEntry && stockQuantityInput) {
            stockQuantityInput.value = 1;
            stockQuantityInput.readOnly = true;
            stockQuantityInput.disabled = true;

            // Show notice
            let notice = document.getElementById('individualEntryNotice');
            if (!notice) {
                notice = document.createElement('div');
                notice.id = 'individualEntryNotice';
                notice.className = 'alert alert-info';
                notice.innerHTML = '<i class="fas fa-info-circle"></i> This category requires individual entry. Stock quantity is automatically set to 1.';
                stockQuantityInput.parentElement.appendChild(notice);
            }
            notice.style.display = 'block';
        } else if (stockQuantityInput) {
            stockQuantityInput.readOnly = false;
            stockQuantityInput.disabled = false;
            const notice = document.getElementById('individualEntryNotice');
            if (notice) {
                notice.style.display = 'none';
            }
        }
    }
});
</script>
