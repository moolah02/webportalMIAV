import os, glob, re

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'
targets = glob.glob(BASE + r'\**\*.blade.php', recursive=True)

SKIP = ['pdf', 'partials', 'exports', 'layouts', 'vendor', 'components', 'docs\\layout', 'guest.blade', 'auth\\']

results = []
for f in targets:
    rel = f.replace(BASE + os.sep, '')
    if any(s in rel for s in SKIP):
        continue
    try:
        txt = open(f, encoding='utf-8').read()
    except:
        continue

    issues = []
    style_attrs = re.findall(r'style=[\"\']([^\"\']*)[\"\']', txt)
    non_trivial = [s for s in style_attrs if len(s) > 10 and 'display:none' not in s and 'display: none' not in s]
    if len(non_trivial) > 5:
        issues.append(str(len(non_trivial)) + ' inline styles')

    bs = ['btn btn-', 'card-body', 'card-header', 'form-control', 'form-group', 'col-md-', 'container-fluid', 'alert alert-']
    found_bs = [b for b in bs if b in txt]
    if found_bs:
        issues.append('Bootstrap: ' + ','.join(found_bs[:4]))

    if 'bg-blue-6' in txt or 'bg-gray-800' in txt or 'bg-green-500' in txt or 'bg-indigo' in txt:
        issues.append('raw Tailwind btn colors')

    style_blocks = len(re.findall(r'<style[^>]*>', txt))
    if style_blocks > 0:
        issues.append(str(style_blocks) + ' style block(s)')

    if issues:
        results.append((rel, issues))

results.sort(key=lambda x: len(x[1]), reverse=True)
for r, issues in results:
    print(r)
    for i in issues:
        print('   - ' + i)
