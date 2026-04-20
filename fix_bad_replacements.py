"""Fix bad replacements from batch_fix_ui.py and improve patterns."""
import os, re, glob

BASE = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

# Step 1: Find all files with bad replacements
print("=== Finding corrupted class names ===")
bad_files = {}
for f in sorted(glob.glob(BASE + r'\**\*.blade.php', recursive=True)):
    try:
        txt = open(f, encoding='utf-8').read()
    except:
        continue
    bad = re.findall(r'[\w]+-ui-card-(?:body|header)(?:-[\w]+)*', txt)
    if bad:
        rel = f.replace(BASE + os.sep, '')
        bad_files[f] = (rel, list(set(bad)))
        print(rel + ': ' + ', '.join(set(bad)))

print(f"\n=== Fixing {len(bad_files)} files ===")

for fpath, (rel, bad_classes) in bad_files.items():
    txt = open(fpath, encoding='utf-8').read()
    original = txt
    # Undo: remove the 'ui-' that was wrongly inserted in the middle
    # e.g., stat-ui-card-body → stat-card-body
    #        rh-ui-card-header → rh-card-header
    txt = re.sub(r'([\w]+-)(ui-)(card-(?:body|header))', r'\1\3', txt)
    if txt != original:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(txt)
        print(f'Fixed: {rel}')
    else:
        print(f'No change: {rel}')

print("\nDone.")
