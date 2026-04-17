import os, glob

base = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'
missing = []
for fp in glob.glob(base + r'\**\*.blade.php', recursive=True):
    rel = fp.replace(base + '\\', '')
    skip = ['layouts\\', 'vendor\\', 'auth\\', 'docs\\', 'exports\\', 'pdf\\']
    if any(x in rel for x in skip):
        continue
    with open(fp, encoding='utf-8', errors='ignore') as f:
        content = f.read()
    has_extends = "@extends('layouts.app')" in content
    has_title = "@section('title'" in content
    if has_extends and not has_title:
        missing.append(rel)

for r in sorted(missing):
    print(r)
print(f'\nTotal missing: {len(missing)}')
