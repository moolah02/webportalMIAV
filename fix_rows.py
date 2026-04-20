"""
Fix <div class="row"> containers in wizard/report pages.
Convert to Tailwind CSS grid.
"""
import os, re

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

TARGET_FILES = [
    r'projects\closure-wizard.blade.php',
    r'projects\completion-wizard.blade.php',
    r'projects\closure-reports.blade.php',
    r'projects\completion-reports.blade.php',
    r'projects\completion-success.blade.php',
    r'projects\create-improved.blade.php',
    r'projects\partials\active-projects-tab.blade.php',
    r'projects\partials\analytics-tab.blade.php',
    r'projects\partials\manual-report-generator.blade.php',
    r'projects\partials\report-generation-tab.blade.php',
    r'projects\partials\terminal-preview-modal.blade.php',
    r'projects\partials\terminal-upload-section.blade.php',
    r'pos-terminals\import.blade.php',
    r'reports\technician-visits.blade.php',
    r'deployment\site-visit.blade.php',
    r'profile\profile.blade.php',
]

def fix_rows(txt):
    # 1. Convert <div class="row"> to grid
    #    Check if it wraps children with class="" → those are former col-md-X
    
    # Replace <div class="row"> with grid
    txt = re.sub(
        r'<div class="row">',
        '<div class="grid grid-cols-1 md:grid-cols-2 gap-5">',
        txt
    )
    txt = re.sub(
        r'<div class="row mb-(\d+)">',
        r'<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-\1">',
        txt
    )
    txt = re.sub(
        r'<div class="row mt-(\d+)">',
        r'<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-\1">',
        txt
    )
    
    # 2. container-fluid → empty
    txt = re.sub(r'<div class="container-fluid[^"]*">', '<div>', txt)
    txt = re.sub(r'<div class="container[^"]*">', '<div>', txt)
    
    # 3. Remove empty class attributes
    txt = re.sub(r' class=""', '', txt)
    txt = re.sub(r" class=''", '', txt)
    
    return txt

changed = 0
for rel in TARGET_FILES:
    path = os.path.join(BASE, rel)
    if not os.path.exists(path):
        print(f'SKIP: {rel}')
        continue
    try:
        original = open(path, encoding='utf-8').read()
        patched = fix_rows(original)
        if patched != original:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(patched)
            changed += 1
            print(f'Fixed rows: {rel}')
        else:
            print(f'No change: {rel}')
    except Exception as e:
        print(f'ERROR {rel}: {e}')

print(f'\nDone. {changed} file(s) updated.')
