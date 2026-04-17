import re, glob

base = "resources/views"
changed = []

for path in glob.glob(base + "/**/*.blade.php", recursive=True):
    if "layouts" in path or "vendor" in path:
        continue
    with open(path, "r", encoding="utf-8") as f:
        content = f.read()
    original = content

    # Pattern: header div with justify-between that now contains NO inner <div> title wrapper
    # i.e. the div immediately contains buttons/links, not a <div> child for the title
    # Replace: justify-between items-center mb-6 -> justify-end items-center mb-6
    # Only when the comment {{-- Header --}} is directly before it
    content = content.replace(
        '{{-- Header --}}\n<div class="flex justify-between items-center mb-6">',
        '{{-- Actions --}}\n<div class="flex justify-end items-center mb-6">'
    )
    content = content.replace(
        '{{-- Header --}}\n<div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">',
        '{{-- Actions --}}\n<div class="flex justify-end items-center mb-6 pb-4 border-b border-gray-200">'
    )

    if content != original:
        with open(path, "w", encoding="utf-8", newline="") as f:
            f.write(content)
        rel = path.replace(base + "/", "")
        changed.append(rel)

print("Fixed " + str(len(changed)) + " files:")
for fn in changed:
    print("  " + fn)