"""
Second-pass fixes: replace metric-card structures with design system stat-cards,
fix header action divs, and other structural issues.
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

def fix_metric_classes(content):
    """Replace .metric-card/.metric-icon/.metric-number/.metric-label with stat-card equivalents."""
    # metric-card (with possible extra classes)
    content = re.sub(r'class="metric-card[^"]*"', 'class="stat-card"', content)
    # metric-icon
    content = re.sub(
        r'<div class="metric-icon"[^>]*>',
        '<div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">',
        content
    )
    content = re.sub(r'class="metric-icon[^"]*"',
        'class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0"',
        content)
    content = re.sub(r'class="metric-content[^"]*"', 'class="flex-1 min-w-0"', content)
    content = re.sub(r'class="metric-number[^"]*"', 'class="stat-number"', content)
    content = re.sub(r'class="metric-label[^"]*"', 'class="stat-label uppercase tracking-wide"', content)
    return content

def fix_containers(content):
    """Remove .container, .header, .stats-container, .actions, .subtitle wrappers."""
    # Remove <div class="container"> but keep its content
    content = re.sub(r'<div class="container">', '', content)
    # stats-container → Tailwind grid
    content = re.sub(r'class="stats-container"',
        'class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6"', content)
    # .header div → flex justify-between
    content = re.sub(r'class="header"', 'class="flex justify-between items-center mb-6"', content)
    # .actions div → flex gap
    content = re.sub(r'class="actions"', 'class="flex gap-2"', content)
    # .subtitle paragraph → Tailwind text
    content = re.sub(r'class="subtitle"', 'class="text-sm text-gray-500 mt-1"', content)
    return content

def fix_roles_header(content):
    """Move roles buttons to @section('header-actions') and remove the old header div."""
    # Find and restructure the header in roles/create and roles/edit
    old_header_create = r"""<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Manage user roles and permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn-primary">+ Create New Role</a>
    </div>"""
    new_header_create = ""
    if old_header_create in content:
        # Inject @section('header-actions') before @section('content')
        content = content.replace(
            "@section('content')",
            "@section('header-actions')\n<a href=\"{{ route('roles.create') }}\" class=\"btn-primary\">+ Create Role</a>\n@endsection\n\n@section('content')"
        )
        content = content.replace(old_header_create, "")
        print("  Fixed roles/index header")
    return content

def fix_role_form_headers(content, back_route, back_label):
    """Fix roles/create and roles/edit header divs."""
    # Remove header divs with inline styles and absorb buttons into header-actions section
    # Pattern: <div style="display: flex; ..."><div>...<p>...</p></div><div style="display: flex; gap: ...">buttons</div></div>
    pattern = r'<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: \d+px;">\s*<div>\s*<p[^>]*>[^<]*</p>\s*</div>\s*(<div[^>]*>.*?</div>)\s*</div>'
    match = re.search(pattern, content, re.DOTALL)
    if match:
        buttons_div = match.group(1)
        # Extract button links from that div
        buttons = re.findall(r'<a[^>]*>.*?</a>', buttons_div, re.DOTALL)
        btn_str = "\n".join(b.strip() for b in buttons)
        content = content.replace(
            "@section('content')",
            f"@section('header-actions')\n{btn_str}\n@endsection\n\n@section('content')"
        )
        content = re.sub(pattern, '', content, flags=re.DOTALL)
        print("  Fixed form header div")
    # Also fix single-button header divs
    pattern2 = r'<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: \d+px;">\s*<div>\s*<p[^>]*>[^<]*</p>\s*</div>\s*(<a[^>]*>.*?</a>)\s*</div>'
    match2 = re.search(pattern2, content, re.DOTALL)
    if match2:
        btn = match2.group(1).strip()
        content = content.replace(
            "@section('content')",
            f"@section('header-actions')\n{btn}\n@endsection\n\n@section('content')"
        )
        content = re.sub(pattern2, '', content, flags=re.DOTALL)
        print("  Fixed form header with single button")
    return content

def fix_table_containers(content):
    """Replace table-container with overflow-x-auto."""
    content = re.sub(r'class="table-container"', 'class="overflow-x-auto"', content)
    # stats-grid (business-licenses)
    content = re.sub(r'class="stats-grid"', 'class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3"', content)
    content = re.sub(r'class="stat"', 'class="bg-white rounded-xl border border-gray-200 p-4"', content)
    content = re.sub(r'class="stat__value"', 'class="text-2xl font-bold text-gray-900"', content)
    content = re.sub(r'class="stat__label"', 'class="text-xs text-gray-500 mt-1"', content)
    return content

# ── Process each file ──────────────────────────────────────────

# roles/index — fix header + metric cards
rel = "roles/index.blade.php"
c = read(rel)
c = fix_metric_classes(c)
c = fix_roles_header(c)
c = fix_table_containers(c)
write(rel, c)

# roles/create — fix header
rel = "roles/create.blade.php"
c = read(rel)
c = fix_role_form_headers(c, "roles.index", "Back to Roles")
write(rel, c)

# roles/edit — fix header
rel = "roles/edit.blade.php"
c = read(rel)
c = fix_role_form_headers(c, "roles.index", "Back to Roles")
write(rel, c)

# asset-approvals/index
rel = "asset-approvals/index.blade.php"
c = read(rel)
c = fix_metric_classes(c)
c = fix_containers(c)
c = fix_table_containers(c)
write(rel, c)

# client-dashboards/index + show — fix containers
for rel in ["client-dashboards/index.blade.php", "client-dashboards/show.blade.php"]:
    c = read(rel)
    c = fix_metric_classes(c)
    c = fix_containers(c)
    c = fix_table_containers(c)
    write(rel, c)

# deployment, jobs/assignment — fix metric cards if present
for rel in ["deployment/hierarchical.blade.php", "jobs/assignment.blade.php"]:
    c = read(rel)
    c = fix_metric_classes(c)
    c = fix_containers(c)
    c = fix_table_containers(c)
    write(rel, c)

# business-licenses — fix stats divs
for rel in ["business-licenses/index.blade.php", "business-licenses/show.blade.php",
            "business-licenses/create.blade.php", "business-licenses/edit.blade.php",
            "business-licenses/renew.blade.php"]:
    c = read(rel)
    c = fix_metric_classes(c)
    c = fix_containers(c)
    c = fix_table_containers(c)
    write(rel, c)

# asset-requests
for rel in ["asset-requests/index.blade.php", "asset-requests/checkout.blade.php",
            "asset-requests/show.blade.php", "asset-requests/catalog.blade.php",
            "asset-requests/cart.blade.php"]:
    c = read(rel)
    c = fix_metric_classes(c)
    c = fix_containers(c)
    c = fix_table_containers(c)
    write(rel, c)

# reports/system-dashboard
rel = "reports/system-dashboard.blade.php"
c = read(rel)
c = fix_metric_classes(c)
c = fix_containers(c)
c = fix_table_containers(c)
write(rel, c)

print("\nDone.")
