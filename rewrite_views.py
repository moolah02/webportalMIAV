"""
Rewrite blade views to use the shared design system classes.
Removes local <style> blocks and replaces old class names with design system equivalents.
"""
import re, os

BASE = r"c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views"

def read(rel):
    path = os.path.join(BASE, rel)
    with open(path, 'r', encoding='utf-8') as f:
        return f.read()

def write(rel, content):
    path = os.path.join(BASE, rel)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"WROTE: {rel}")

def remove_style_block(content):
    """Remove <style>...</style> blocks."""
    return re.sub(r'\n?<style>[^<]*(?:<(?!/style>)[^<]*)*</style>', '', content, flags=re.DOTALL)

def fix_classes(content):
    """Replace old class names with design system equivalents."""
    replacements = [
        # Buttons (order matters — most specific first)
        (r'class="btn btn-primary ([^"]+)"', r'class="btn-primary \1"'),
        (r'class="btn btn-danger ([^"]+)"', r'class="btn-danger \1"'),
        (r'class="btn btn-success ([^"]+)"', r'class="btn-success \1"'),
        (r'class="btn btn-primary"', 'class="btn-primary"'),
        (r'class="btn btn-danger"', 'class="btn-danger"'),
        (r'class="btn btn-success"', 'class="btn-success"'),
        (r'class="btn btn-outline"', 'class="btn-secondary"'),
        (r'class="btn btn-ghost"', 'class="btn-secondary"'),
        (r'class="btn btn-small"', 'class="btn-secondary btn-sm"'),
        (r'class="btn btn-small btn-outline"', 'class="btn-secondary btn-sm"'),
        (r'class="btn btn-small btn-primary"', 'class="btn-primary btn-sm"'),
        # Simple btn — make secondary
        (r'class="btn"', 'class="btn-secondary"'),
        # Cards
        (r'class="content-card"', 'class="ui-card p-6"'),
        (r'class="card"', 'class="ui-card"'),
        # Tables
        (r'class="roles-table"', 'class="ui-table"'),
        (r'class="table"', 'class="ui-table"'),
        # Form inputs (be careful — only standalone class attrs)
        (r'class="input"', 'class="ui-input"'),
        (r'class="filter-select"', 'class="ui-select"'),
        (r'class="filter-input"', 'class="ui-input"'),
        (r'class="modal-textarea"', 'class="ui-input"'),
        # Badges — keep badge class, add gray as default color
        (r'class="badge"(?! badge)', 'class="badge badge-gray"'),
        # Status badges → design system
        (r'class="status-badge status-active"', 'class="badge badge-green"'),
        (r'class="status-badge status-approved"', 'class="badge badge-green"'),
        (r'class="status-badge status-pending"', 'class="badge badge-yellow"'),
        (r'class="status-badge status-rejected"', 'class="badge badge-red"'),
        (r'class="status-badge status-fulfilled"', 'class="badge badge-blue"'),
        (r'class="status-badge status-([^"]+)"', 'class="badge badge-gray"'),
        # Priority badges
        (r'class="priority-badge priority-urgent"', 'class="badge badge-red"'),
        (r'class="priority-badge priority-high"', 'class="badge badge-orange"'),
        (r'class="priority-badge priority-normal"', 'class="badge badge-blue"'),
        (r'class="priority-badge priority-low"', 'class="badge badge-gray"'),
        (r'class="priority-badge priority-([^"]+)"', 'class="badge badge-gray"'),
        # Permission badges
        (r'class="permission-badge super-admin"', 'class="badge badge-red"'),
        (r'class="permission-badge manager"', 'class="badge badge-purple"'),
        (r'class="permission-badge user"', 'class="badge badge-blue"'),
        (r'class="permission-badge limited"', 'class="badge badge-gray"'),
        (r'class="permission-badge ([^"]+)"', 'class="badge badge-gray"'),
        (r'class="permission-tag"', 'class="badge badge-gray"'),
    ]
    for pattern, replacement in replacements:
        content = re.sub(pattern, replacement, content)
    return content

def fix_header_divs(content):
    """Replace full-width inline-style header action divs."""
    # Remove empty header paragraphs with only subtitle text
    content = re.sub(
        r'\s*<div[^>]*>\s*<p style="[^"]*">[^<]*</p>\s*</div>\s*\n\s*(<[a-z])',
        r'\n    \1',
        content
    )
    return content

def process(rel):
    content = read(rel)
    content = remove_style_block(content)
    content = fix_classes(content)
    write(rel, content)

# Files to process
FILES = [
    "business-licenses/index.blade.php",
    "business-licenses/show.blade.php",
    "business-licenses/create.blade.php",
    "business-licenses/edit.blade.php",
    "business-licenses/renew.blade.php",
    "roles/index.blade.php",
    "roles/create.blade.php",
    "roles/edit.blade.php",
    "asset-approvals/index.blade.php",
    "asset-approvals/show.blade.php",
    "asset-requests/index.blade.php",
    "asset-requests/checkout.blade.php",
    "asset-requests/show.blade.php",
    "asset-requests/catalog.blade.php",
    "asset-requests/cart.blade.php",
    "deployment/hierarchical.blade.php",
    "jobs/assignment.blade.php",
    "client-dashboards/index.blade.php",
    "client-dashboards/show.blade.php",
    "reports/system-dashboard.blade.php",
    "settings/index.blade.php",
    "settings/manage-department.blade.php",
    "settings/manage-role.blade.php",
    "pos-terminals/show.blade.php",
]

for f in FILES:
    path = os.path.join(BASE, f)
    if os.path.exists(path):
        process(f)
    else:
        print(f"SKIP (not found): {f}")

print("\nDone.")
