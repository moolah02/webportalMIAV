"""Remove duplicate page-level h1/h2 headings from pages that extend layouts.app."""
import re, os

BASE = r"c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views"

# Files with page-level headings near the top of @section('content') to remove
# (file relative to BASE, heading pattern to remove)
TARGETS = [
    "asset-approvals/index.blade.php",
    "asset-approvals/show.blade.php",
    "asset-requests/checkout.blade.php",
    "asset-requests/index.blade.php",
    "asset-requests/show.blade.php",
    "business-licenses/create.blade.php",
    "business-licenses/edit.blade.php",
    "business-licenses/renew.blade.php",
    "business-licenses/index.blade.php",
    "business-licenses/show.blade.php",
    "client-dashboards/index.blade.php",
    "client-dashboards/show.blade.php",
    "deployment/hierarchical.blade.php",
    "jobs/assignment.blade.php",
    "pos-terminals/show.blade.php",
    "reports/system-dashboard.blade.php",
    "roles/create.blade.php",
    "roles/edit.blade.php",
    "roles/index.blade.php",
    "visits/create.blade.php",
    "settings/manage-department.blade.php",
    "settings/manage-role.blade.php",
    "settings/index.blade.php",
]

# Pattern: standalone h1 or h2 elements (possibly with emoji, title text)
HEADING_RE = re.compile(r'^\s*<h[12][^>]*>.*?</h[12]>\s*$', re.IGNORECASE)

def process(rel_path):
    path = os.path.join(BASE, rel_path)
    if not os.path.exists(path):
        print(f"SKIP (not found): {rel_path}")
        return

    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()

    new_lines = []
    inside_section = False
    section_line_count = 0
    removed = 0

    for i, line in enumerate(lines):
        # Track when we enter @section('content')
        if not inside_section and re.search(r"@section\(['\"](content)['\"]", line):
            inside_section = True
            section_line_count = 0
            new_lines.append(line)
            continue

        if inside_section:
            section_line_count += 1
            # Only look at first 20 lines of the section
            if section_line_count <= 20:
                stripped = line.strip()
                # Skip blank lines for counting purposes
                if not stripped:
                    new_lines.append(line)
                    continue
                # Remove h1/h2 that are page-level titles (not inside other elements)
                if HEADING_RE.match(line):
                    # Don't remove h2 that look like section headings (contain "Details", "Information", etc.)
                    # and are NOT the very first heading after @section
                    print(f"  REMOVE line {i+1}: {line.rstrip()[:80]}")
                    removed += 1
                    continue

        new_lines.append(line)

    if removed > 0:
        with open(path, 'w', encoding='utf-8') as f:
            f.writelines(new_lines)
        print(f"FIXED ({removed} removed): {rel_path}")
    else:
        print(f"CLEAN: {rel_path}")

for t in TARGETS:
    process(t)

print("\nDone.")
