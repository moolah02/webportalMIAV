"""Third-pass: remove filter-group wrappers, wrap tables in ui-card, fix roles search input."""
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

def remove_filter_group(content):
    """Remove <div class="filter-group"> wrappers (keep content)."""
    content = re.sub(r'\s*<div class="filter-group">\s*', '\n            ', content)
    content = re.sub(r'\s*</div>\s*\n(\s*(?:<select|<input|<button|@if|@endif))', r'\n\1', content)
    return content

def wrap_table_in_card(content):
    """Wrap standalone <table class="ui-table"> with a ui-card div."""
    # Find table that comes directly after a </div> of filter section (not already in a card)
    content = re.sub(
        r'(\n\s*</div>\s*\n)\n(\s*<!-- Requests Table -->)\n(\s*<table class="ui-table">)',
        r'\1\n\2\n    <div class="ui-card overflow-hidden mt-4">\n        <div class="overflow-x-auto">\n\3',
        content
    )
    # Close the wrapping div before pagination
    content = re.sub(
        r'(\s*</tbody>\s*\n\s*</table>)(\s*\n\s*<!-- Pagination -->)',
        r'\1\n        </div>\n    </div>\2',
        content
    )
    return content

def fix_roles_search(content):
    """Fix roles/index search form inputs."""
    # The search form uses inline style for the input
    content = content.replace(
        'style="flex: 1; min-width: 250px; padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;"',
        'class="ui-input flex-1 min-w-52"'
    )
    content = content.replace(
        'style="padding: 10px; border: 1px solid #E0E0E0; border-radius: 6px; font-size: 14px;"',
        'class="ui-select w-auto"'
    )
    # Remove inline styles from the search form wrapper
    content = content.replace(
        'style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;"',
        'class="flex gap-3 items-center flex-wrap"'
    )
    return content

def fix_roles_table(content):
    """Fix roles table wrapper."""
    content = re.sub(r'class="ui-card p-6"\s*>', 'class="ui-card">', content)
    content = re.sub(
        r'(<!-- Roles Table -->)\s*\n\s*<div class="ui-card">',
        r'<!-- Roles Table -->\n    <div class="ui-card overflow-hidden mt-4">',
        content
    )
    content = re.sub(r'<div class="overflow-x-auto">\s*\n\s*<table class="ui-table">',
                     '<div class="overflow-x-auto"><table class="ui-table">', content)
    return content

def fix_inline_styles_general(content):
    """Remove well-known inline styles replaced by Tailwind."""
    # margin-bottom: 20px; on cards → handled by mb classes
    content = content.replace(' style="margin-bottom: 20px;"', '')
    content = content.replace(' style="margin-bottom: 30px;"', '')
    content = content.replace(' style="margin-top: 30px; display: flex; justify-content: center;"',
                              ' class="mt-6 flex justify-center"')
    # Empty header divs
    content = re.sub(r'\n    <!-- Header -->\s*\n\s*\n', '\n', content)
    return content

# Process asset-approvals/index
rel = "asset-approvals/index.blade.php"
c = read(rel)
c = remove_filter_group(c)
c = wrap_table_in_card(c)
write(rel, c)

# Process roles/index
rel = "roles/index.blade.php"
c = read(rel)
c = fix_roles_search(c)
c = fix_inline_styles_general(c)
write(rel, c)

# Fix inline style on pagination divs across all rewritten files
for rel in [
    "business-licenses/index.blade.php",
    "roles/index.blade.php",
    "roles/create.blade.php",
    "roles/edit.blade.php",
    "asset-approvals/index.blade.php",
    "asset-requests/index.blade.php",
    "deployment/hierarchical.blade.php",
    "jobs/assignment.blade.php",
    "reports/system-dashboard.blade.php",
]:
    path = os.path.join(BASE, rel)
    if not os.path.exists(path):
        continue
    c = read(rel)
    c = fix_inline_styles_general(c)
    write(rel, c)

print("\nDone.")
