"""
Additional targeted fixes:
1. Remove standalone 'btn' class that remains after failed btn-* replacement
2. Fix remaining form-group in column-mapping
3. Fix any remaining col-md-* layout classes (convert Bootstrap grid to Tailwind)
"""
import os, re, glob

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

TARGET_FILES = [
    r'pos-terminals\import.blade.php',
    r'pos-terminals\column-mapping.blade.php',
    r'pos-terminals\show.blade.php',
    r'projects\closure-wizard.blade.php',
    r'projects\closure-reports.blade.php',
    r'projects\completion-wizard.blade.php',
    r'projects\completion-reports.blade.php',
    r'projects\completion-success.blade.php',
    r'projects\create-improved.blade.php',
    r'projects\partials\active-projects-tab.blade.php',
    r'projects\partials\analytics-tab.blade.php',
    r'projects\partials\manual-report-generator.blade.php',
    r'projects\partials\report-generation-tab.blade.php',
    r'projects\partials\terminal-preview-modal.blade.php',
    r'projects\partials\terminal-upload-section.blade.php',
    r'reports\index.blade.php',
    r'reports\system-dashboard.blade.php',
    r'asset-requests\index.blade.php',
    r'settings\asset-category-fields\index.blade.php',
    r'settings\manage-category.blade.php',
]

def fix_class_val(val):
    # Remove standalone 'btn' class (leaves btn-* modifier classes intact)
    # e.g. "btn btn-sm btn-danger mt-2" → "btn-sm btn-danger mt-2"
    #      "btn btn-primary" → "btn-primary" (already handled, but safe to repeat)
    val = re.sub(r'(?<![a-zA-Z0-9-])btn(?![a-zA-Z0-9-])\s*', '', val)
    # Fix remaining form-group → mb-4
    val = re.sub(r'(?<![a-zA-Z0-9-])form-group(?![a-zA-Z0-9-])', 'mb-4', val)
    # Convert Bootstrap grid col-md-* to Tailwind (just remove the class; structure is handled by parent grid)
    val = re.sub(r'(?<![a-zA-Z0-9-])col-md-\d+(?![a-zA-Z0-9-])', '', val)
    val = re.sub(r'(?<![a-zA-Z0-9-])col-lg-\d+(?![a-zA-Z0-9-])', '', val)
    val = re.sub(r'(?<![a-zA-Z0-9-])col-sm-\d+(?![a-zA-Z0-9-])', '', val)
    val = re.sub(r'(?<![a-zA-Z0-9-])col-xl-\d+(?![a-zA-Z0-9-])', '', val)
    val = re.sub(r'(?<![a-zA-Z0-9-])col-\d+(?![a-zA-Z0-9-])', '', val)
    # Clean double spaces
    val = re.sub(r'  +', ' ', val).strip()
    return val

def patch_file(fpath):
    txt = open(fpath, encoding='utf-8').read()
    original = txt

    def patch_dq(m):
        return 'class="' + fix_class_val(m.group(1)) + '"'
    def patch_sq(m):
        return "class='" + fix_class_val(m.group(1)) + "'"

    txt = re.sub(r'class="([^"]*)"', patch_dq, txt)
    txt = re.sub(r"class='([^']*)'", patch_sq, txt)
    return txt, txt != original

changed = 0
for rel in TARGET_FILES:
    path = os.path.join(BASE, rel)
    if not os.path.exists(path):
        print(f'SKIP: {rel}')
        continue
    try:
        new_txt, was_changed = patch_file(path)
        if was_changed:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(new_txt)
            changed += 1
            print(f'Patched: {rel}')
        else:
            print(f'No change: {rel}')
    except Exception as e:
        print(f'ERROR {rel}: {e}')

print(f'\nDone. {changed} file(s) updated.')
