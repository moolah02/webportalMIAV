import glob, re

BASE = r'resources\views'
SKIP = ['layouts', 'vendor', 'auth\\', 'guest.blade']

for f in sorted(glob.glob(BASE + r'\**\*.blade.php', recursive=True)):
    if any(s in f for s in SKIP):
        continue
    try:
        txt = open(f, encoding='utf-8').read()
    except:
        continue

    # Remove ui-card prefixed classes so they don't false-positive
    txt2 = txt.replace('ui-card-body', 'UICB').replace('ui-card-header', 'UICH')

    issues = []
    if re.search(r'\bcard-body\b', txt2): issues.append('card-body')
    if re.search(r'\bcard-header\b', txt2): issues.append('card-header')
    if 'btn btn-' in txt2: issues.append('btn btn-')
    if re.search(r'\bform-control\b', txt2): issues.append('form-control')
    if re.search(r'\bcol-md-\d', txt2): issues.append('col-md-')
    if re.search(r'\bform-group\b', txt2): issues.append('form-group')
    if re.search(r'\bcontainer-fluid\b', txt2): issues.append('container-fluid')
    if re.search(r'bg-blue-[456789]|bg-gray-800|bg-green-500[^;]|bg-indigo-[456]', txt2): issues.append('raw-colors')
    if '<style' in txt2 and 'body {' in txt2: issues.append('<style>+body{}')
    if issues:
        print(f.replace(BASE + '\\', '') + ' -> ' + ', '.join(issues))
