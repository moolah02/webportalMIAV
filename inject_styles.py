import re

# Read recovered CSS
css_raw = open('deployment_styles_recover.css', encoding='utf-8').read()
css_raw = css_raw.replace('\r\n', '\n').strip()

# Replace wrong brand colors in recovered CSS
css_raw = css_raw.replace('#2196f3', '#1a3a5c').replace('#1976d2', '#152e4a')

# Wrap in @push('styles')
push_block = "@push('styles')\n" + css_raw + "\n@endpush\n\n"

# Read current file
path = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\deployment\hierarchical.blade.php'
txt = open(path, encoding='utf-8').read()

MARKER = "@section('content')"
if "@push('styles')" in txt:
    print('Already has @push styles - nothing to do')
elif MARKER in txt:
    txt = txt.replace(MARKER, push_block + MARKER, 1)
    open(path, 'w', encoding='utf-8').write(txt)
    print('SUCCESS: Injected styles block before @section content')
else:
    print('ERROR: Could not find insertion point')
