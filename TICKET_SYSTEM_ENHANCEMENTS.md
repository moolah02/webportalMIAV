# Support Ticket System - Enhanced Features Implementation

## Overview
I've successfully implemented the three requested features for your support ticket system:

1. **Ticket Type Division**: POS Terminal vs Internal Tickets
2. **Assignment Type**: Public (any employee can attend) vs Direct (assigned to specific employee)
3. **Status Filtering**: View pending tickets and other filtered views

---

## Database Changes

### Migration Created
**File**: `database/migrations/2026_01_21_add_ticket_type_and_assignment_type.php`

**New Columns Added to `tickets` Table**:
```sql
- ticket_type ENUM('pos_terminal', 'internal') DEFAULT 'pos_terminal'
- assignment_type ENUM('public', 'direct') DEFAULT 'public'
```

**Indexes**: Both columns are indexed for fast filtering

---

## Model Updates

### File: `app/Models/Ticket.php`

**New Fillable Fields**:
```php
'ticket_type'       // pos_terminal or internal
'assignment_type'   // public or direct
```

**New Scope Methods** (for easy filtering):
```php
scopeByTicketType($ticketType)              // Filter by ticket type
scopeByAssignmentType($assignmentType)      // Filter by assignment type
scopePending()                              // Get open, in_progress, pending tickets
scopePosTerminalTickets()                   // Get all POS terminal tickets
scopeInternalTickets()                      // Get all internal tickets
scopePublicTickets()                        // Get all public tickets
scopeDirectTickets()                        // Get all direct tickets
```

---

## Controller Updates

### 1. TicketController (Web Interface)

**Updated Methods**:

#### `index()` - Enhanced Filtering
- Added filters for `ticket_type` parameter
- Added filters for `assignment_type` parameter  
- Added filters for `pending` parameter (shows open/in_progress/pending)

**Query Parameters**:
```
?ticket_type=pos_terminal    // Show only POS terminal tickets
?ticket_type=internal         // Show only internal tickets
?assignment_type=public       // Show only public tickets
?assignment_type=direct       // Show only direct tickets
?pending=true                 // Show only pending tickets
```

**Enhanced Statistics**:
```php
$stats = [
    'open'           // Count of open tickets
    'in_progress'    // Count of in-progress tickets
    'pending'        // Count of pending (open/in_progress/pending)
    'resolved'       // Count of resolved tickets
    'critical'       // Count of critical priority tickets
    'pos_terminal'   // Count of POS terminal tickets
    'internal'       // Count of internal tickets
    'public'         // Count of public tickets
    'direct'         // Count of direct tickets
]
```

#### `store()` - Create Ticket Validation
```php
'ticket_type' => 'required|in:pos_terminal,internal'
'assignment_type' => 'required|in:public,direct'
```

**Conditional Validation**:
- If `ticket_type = 'pos_terminal'`: `pos_terminal_id` is required
- If `assignment_type = 'direct'`: `assigned_to` (employee) is required

#### `update()` - Edit Ticket
Same validation rules as store, ensuring data integrity

---

### 2. API TicketController (`app/Http/Controllers/Api/TicketController.php`)

**Updated Methods**:

#### `index()` - GET /api/tickets
New filter parameters added:
```
?ticket_type=pos_terminal
?ticket_type=internal
?assignment_type=public
?assignment_type=direct
?pending=true
```

#### `store()` - POST /api/tickets
New required fields:
```json
{
    "title": "Ticket title",
    "description": "Description",
    "issue_type": "hardware_malfunction",
    "priority": "high",
    "ticket_type": "pos_terminal",        // NEW: pos_terminal or internal
    "assignment_type": "direct",          // NEW: public or direct
    "pos_terminal_id": 5,                 // Required if ticket_type = pos_terminal
    "assigned_to": 3                      // Required if assignment_type = direct
}
```

#### `mapTicketRow()` & `mapTicketDetail()`
API responses now include:
```json
{
    "ticket_type": "pos_terminal",
    "assignment_type": "public",
    ...
}
```

---

## API Changes Summary

### Existing APIs That Changed

| Endpoint | Method | Changes |
|----------|--------|---------|
| `/api/tickets` | GET | Added query filters: `ticket_type`, `assignment_type`, `pending` |
| `/api/tickets` | POST | Added required fields: `ticket_type`, `assignment_type`; Conditional requirements |
| `/api/tickets/{id}` | GET | Response now includes `ticket_type`, `assignment_type` |
| `/api/tickets/{id}` | PUT | Added validation for `ticket_type`, `assignment_type` |

### New Filters Available

**Query Parameters**:
```
GET /api/tickets?ticket_type=pos_terminal
GET /api/tickets?assignment_type=direct
GET /api/tickets?pending=true
GET /api/tickets?status=open&ticket_type=internal
GET /api/tickets?assignment_type=public&priority=high
```

### Example API Requests

**Create POS Terminal Ticket (Public)**:
```json
POST /api/tickets
{
    "title": "Terminal Not Responding",
    "description": "POS terminal 045 not responding",
    "issue_type": "hardware_malfunction",
    "priority": "urgent",
    "ticket_type": "pos_terminal",
    "assignment_type": "public",
    "pos_terminal_id": 45
}
```

**Create Internal Ticket (Direct Assignment)**:
```json
POST /api/tickets
{
    "title": "Internal System Issue",
    "description": "Database sync issue",
    "issue_type": "software_issue",
    "priority": "high",
    "ticket_type": "internal",
    "assignment_type": "direct",
    "assigned_to": 12
}
```

**Get All Pending POS Terminal Tickets**:
```
GET /api/tickets?ticket_type=pos_terminal&pending=true
```

**Get All Public Internal Tickets**:
```
GET /api/tickets?ticket_type=internal&assignment_type=public
```

---

## Feature Details

### 1. Ticket Type Division

**POS Terminal Tickets**:
- Related to physical POS terminals
- Requires `pos_terminal_id`
- Good for hardware/software issues with specific terminals
- Merchant-specific issues

**Internal Tickets**:
- General internal issues
- Not tied to specific terminals
- For system-wide, office, or personnel issues
- Optional terminal assignment

---

### 2. Assignment Type

**Public Tickets** (`assignment_type = 'public'`):
- Any employee (with permissions) can view and claim
- No specific assignment
- Ideal for general support queues
- Employees can self-assign when starting work

**Direct Tickets** (`assignment_type = 'direct'`):
- Assigned to a specific employee (`assigned_to`)
- Only that employee + managers see as primary responsibility
- Requires `assigned_to` field at creation
- Better for sensitive or specialized issues

---

### 3. Pending Tickets View

**Pending Status** includes:
- `status = 'open'` (newly created, not started)
- `status = 'in_progress'` (actively being worked on)
- `status = 'pending'` (waiting for information/parts/other)

**Query**: `GET /api/tickets?pending=true` or filter in UI by pending

---

## Before Deploying

### Run Migration
```bash
php artisan migrate
```

This will add the two new columns to your existing tickets table with defaults.

### Update Views (If Needed)
If you have custom ticket views/forms, they should include:
```html
<select name="ticket_type" required>
    <option value="pos_terminal">POS Terminal</option>
    <option value="internal">Internal</option>
</select>

<select name="assignment_type" required>
    <option value="public">Public (Any Employee)</option>
    <option value="direct">Direct (Specific Employee)</option>
</select>
```

---

## Testing Checklist

- [ ] Create a POS Terminal ticket (public) - should work
- [ ] Create a POS Terminal ticket (direct) - should work with assigned employee
- [ ] Try creating POS Terminal ticket without `pos_terminal_id` - should error
- [ ] Try creating direct ticket without `assigned_to` - should error
- [ ] Create an Internal ticket (public) - should work
- [ ] Create an Internal ticket (direct) - should work with assigned employee
- [ ] Filter by `?ticket_type=internal` - should show only internal
- [ ] Filter by `?assignment_type=public` - should show only public
- [ ] Filter by `?pending=true` - should show open/in_progress/pending
- [ ] Verify API responses include `ticket_type` and `assignment_type`
- [ ] Check stats show counts for each type

---

## Files Modified/Created

**Created**:
- `database/migrations/2026_01_21_add_ticket_type_and_assignment_type.php`

**Modified**:
- `app/Models/Ticket.php` - Added fillable, casts, and scope methods
- `app/Http/Controllers/TicketController.php` - Updated index, store, update methods
- `app/Http/Controllers/Api/TicketController.php` - Updated index, store, mapTicketRow, mapTicketDetail

---

## Next Steps

1. **Run migration**: `php artisan migrate`
2. **Test locally** or on dev server
3. **Commit changes**: Include migration in git
4. **Deploy to production** following your deployment process
5. **Update frontend** (if you have custom UI) to support new fields

Would you like me to help with the deployment or make any adjustments to these features?
