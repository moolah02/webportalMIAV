import re, glob

base = "resources\\views"
changed = []

for path in glob.glob(base + "\\**\\*.blade.php", recursive=True):
    if "layouts" in path or "vendor" in path:
        continue
    with open(path, "r", encoding="utf-8") as f:
        lines = f.readlines()
    original = "".join(lines)
    new_lines = []
    for l in lines:
        if re.search(r'<h[12][^>]*class="[^"]*page-title', l):
            continue
        if re.search(r'<p[^>]*class="[^"]*page-subtitle', l):
            continue
        new_lines.append(l)
    content = "".join(new_lines)
    content = re.sub(r"[ \t]*<div>\s*\n[ \t]*</div>\s*\n", "", content)
    content = re.sub(r"\n{3,}", "\n\n", content)
    if content != original:
        with open(path, "w", encoding="utf-8", newline="") as f:
            f.write(content)
        rel = path.replace(base + "\\", "")
        changed.append(rel)

print("Fixed " + str(len(changed)) + " files:")
for fn in changed:
    print("  " + fn)