# Staged Resolution System - Implementation Guide

## Overview

The Staged Resolution System allows tickets to be solved in multiple stages/steps with full audit trail tracking. Each stage can be handled by different employees, with transfers tracked and all work documented.

## Key Features

### 1. Days-Based Estimation (Instead of Minutes)
- Changed from `estimated_resolution_time` (minutes) to `estimated_resolution_days`
- More business-friendly for support tickets
- Better aligns with SLA expectations

### 2. Ticket Steps Workflow
- **Create Step**: Record work done on a ticket
- **Complete Step**: Mark a step as completed with resolution notes
- **Transfer Step**: Transfer ticket to another employee with reason tracking
- **Resolve Ticket**: Mark entire ticket as resolved

### 3. Full Audit Trail
- Track who did what and when
- Record reason for each transfer
- Maintain history of all work done on ticket
- View complete audit trail from ticket details

## Database Schema

### Tickets Table (Modified)
```sql
ALTER TABLE tickets
  DROP COLUMN estimated_resolution_time,
  ADD COLUMN estimated_resolution_days INT NULLABLE;
```

### TicketSteps Table (New)
```sql
CREATE TABLE ticket_steps (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  ticket_id BIGINT NOT NULL,
  employee_id BIGINT NOT NULL,
  step_number INT NOT NULL,
  status ENUM('in_progress', 'completed', 'transferred', 'resolved') NOT NULL,
  description VARCHAR(255) NOT NULL,
  notes TEXT NULLABLE,
  resolution_notes TEXT NULLABLE,
  transferred_reason VARCHAR(255) NULLABLE,
  transferred_to BIGINT NULLABLE,
  completed_at TIMESTAMP NULLABLE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (ticket_id) REFERENCES tickets(id),
  FOREIGN KEY (employee_id) REFERENCES employees(id),
  FOREIGN KEY (transferred_to) REFERENCES employees(id),
  INDEX (ticket_id),
  INDEX (employee_id),
  INDEX (status)
);
```

## Models

### TicketStep Model
`app/Models/TicketStep.php`

**Relationships:**
- `ticket()` - Belongs to Ticket
- `employee()` - Belongs to Employee (original worker)
- `transferredToEmployee()` - Belongs to Employee (if transferred)

**Key Methods:**
- `markAsCompleted()` - Mark step as completed
- `transferTo($employee, $reason)` - Transfer to another employee
- `markAsResolved()` - Mark as resolved
- `getAuditTrail()` - Get formatted audit trail entry

### Ticket Model (Enhanced)
`app/Models/Ticket.php`

**New Relationships:**
- `steps()` - Has many TicketSteps

**New Methods:**
- `currentStep()` - Get the current in-progress step

## Controller Methods

### TicketController
`app/Http/Controllers/TicketController.php`

#### `store()` - Create Ticket
- Creates initial TicketStep automatically
- Validates `estimated_resolution_days` instead of minutes
- Sets initial step description as "Ticket created and opened"

#### `addStep(Request $request, Ticket $ticket)`
- Add work/notes to ticket
- Creates new step with auto-incremented step number
- Parameters:
  - `description` (required) - What work was done
  - `notes` (optional) - Additional notes

#### `completeStep(Request $request, Ticket $ticket, TicketStep $step)`
- Mark current step as completed
- Parameters:
  - `resolution_notes` (optional) - What was fixed/accomplished

#### `transferStep(Request $request, Ticket $ticket, TicketStep $step)`
- Transfer ticket to another employee
- Creates audit trail entry
- Parameters:
  - `transferred_to` (required) - Employee ID to transfer to
  - `transferred_reason` (required) - Why transferring
  - `notes` (optional) - Work completed so far

#### `resolveTicket(Request $request, Ticket $ticket)`
- Mark entire ticket as resolved
- Marks current step as resolved
- Sets ticket status to 'resolved'

#### `getAuditTrail(Ticket $ticket)`
- Returns complete audit trail with all steps

## API Routes

### Web Routes
```php
POST    /tickets/{ticket}/steps                    - Add step
PATCH   /tickets/{ticket}/steps/{step}/complete    - Complete step
POST    /tickets/{ticket}/steps/{step}/transfer    - Transfer step
PATCH   /tickets/{ticket}/resolve                  - Resolve ticket
GET     /tickets/{ticket}/audit-trail              - Get audit trail
```

### API Routes
Same endpoints under `/api/tickets/` namespace:
```php
POST    /api/tickets/{ticket}/steps
PATCH   /api/tickets/{ticket}/steps/{step}/complete
POST    /api/tickets/{ticket}/steps/{step}/transfer
PATCH   /api/tickets/{ticket}/resolve
GET     /api/tickets/{ticket}/audit-trail
```

## Frontend UI

### Enhanced Ticket Form
- Changed "Est. Resolution Time (minutes)" to "Est. Resolution Time (Days)"
- Updated field ID from `ticketEstimatedTime` to `ticketEstimatedDays`
- Updated database field name to `estimated_resolution_days`

### Ticket Details Modal
- Added "View Steps" button to see audit trail
- Added "Edit Ticket" button for basic edits

### Ticket Steps Modal (NEW)
- Displays all ticket steps with:
  - Step number and status
  - Employee who handled it
  - Description of work done
  - Resolution notes if completed
  - Transfer information if transferred
  - Completion timestamp
  
- Add Work Step Form:
  - Description field (required)
  - Notes field (optional)
  - "Add Step" button
  - "Complete & Transfer" button

### Transfer Modal (NEW)
- Transfer ticket to another employee
- Requires:
  - Target employee (dropdown)
  - Reason for transfer (textarea)
  - Optional work notes (textarea)

## Usage Workflow

### Scenario 1: Single Employee Resolution
1. Employee creates ticket
2. Initial step automatically created: "Ticket created and opened"
3. Employee clicks "View Steps"
4. Adds step: "Checked hardware connections" + notes
5. Adds another step: "Replaced faulty cable"
6. Completes with resolution: "Cable replacement fixed the issue"
7. System marks ticket as resolved

### Scenario 2: Multi-Step Resolution with Transfer
1. Employee 1 creates ticket
2. Initial step created
3. Employee 1 adds step: "Diagnosed as hardware issue"
4. Employee 1 clicks "Complete & Transfer"
5. Fills transfer form:
   - Transfer to: Employee 2
   - Reason: "Requires hardware replacement expertise"
   - Work done: "Identified faulty power supply"
6. Employee 2 sees transferred ticket
7. Employee 2 adds step: "Replaced power supply"
8. Employee 2 adds step: "Tested and verified functionality"
9. Employee 2 resolves ticket with notes: "Power supply replacement successful"

### Viewing Audit Trail
1. Open ticket details
2. Click "View Steps"
3. See complete history:
   - All work done
   - Who did it
   - When it was done
   - Transfer reasons if applicable
   - Final resolution notes

## Deployment Steps

### Local Development
```bash
# Navigate to project
cd c:\xampp4\htdocs\dashboard\Revival_Technologies

# Run migration (creates ticket_steps table, modifies tickets table)
php artisan migrate --force

# Cache should auto-clear, but optionally:
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

### Production Deployment
```bash
# SSH into production server
ssh root@server_ip

# Navigate to project
cd /var/www/Revival_Technologies

# Run migration on production
php artisan migrate --force

# Restart PHP/Cache
php-fpm restart
systemctl restart php8.2-fpm
```

## API Examples

### Add Work Step
```json
POST /api/tickets/1/steps
Content-Type: application/json

{
  "description": "Diagnosed network connectivity issue",
  "notes": "Checked IP configuration and DNS settings"
}

Response:
{
  "message": "Work step added successfully",
  "step": {
    "id": 1,
    "ticket_id": 1,
    "step_number": 2,
    "employee_id": 5,
    "status": "in_progress",
    "description": "Diagnosed network connectivity issue",
    "notes": "Checked IP configuration and DNS settings",
    ...
  }
}
```

### Complete Step
```json
PATCH /api/tickets/1/steps/2/complete
Content-Type: application/json

{
  "resolution_notes": "Issue was DNS misconfiguration, reconfigured to use 8.8.8.8"
}

Response:
{
  "message": "Step completed successfully",
  "step": {...}
}
```

### Transfer Step
```json
POST /api/tickets/1/steps/2/transfer
Content-Type: application/json

{
  "transferred_to": 7,
  "transferred_reason": "Requires hardware replacement",
  "notes": "Already troubleshot software issues"
}

Response:
{
  "message": "Ticket transferred successfully",
  "old_step": {...},
  "new_step": {...}
}
```

### Resolve Ticket
```json
PATCH /api/tickets/1/resolve
Content-Type: application/json

{
  "resolution_notes": "Hardware replaced successfully, system fully operational"
}

Response:
{
  "message": "Ticket resolved successfully",
  "ticket": {...}
}
```

### Get Audit Trail
```json
GET /api/tickets/1/audit-trail

Response:
{
  "success": true,
  "ticket_id": "TICK-001",
  "total_steps": 3,
  "steps": [
    {
      "step_number": 1,
      "employee_name": "John Doe",
      "status": "completed",
      "description": "Ticket created and opened",
      "completed_at": "2026-01-21 10:30:00",
      ...
    },
    ...
  ]
}
```

## Testing

### Manual Testing Checklist
- [ ] Create new ticket - should auto-create initial step
- [ ] View ticket steps modal
- [ ] Add new work step to ticket
- [ ] Complete step with resolution notes
- [ ] Transfer ticket to another employee with reason
- [ ] View full audit trail showing all steps
- [ ] Verify estimated_resolution_days displays as "days" not "minutes"
- [ ] Resolve ticket from steps modal
- [ ] Verify timestamps are correct for all steps

### Database Validation
```sql
-- Check ticket_steps table created
SELECT COUNT(*) FROM ticket_steps;

-- Check initial steps created for tickets
SELECT t.id, t.ticket_id, ts.step_number, ts.description 
FROM tickets t 
LEFT JOIN ticket_steps ts ON t.id = ts.ticket_id
ORDER BY t.id DESC;

-- Check estimated_resolution_days column
SELECT id, ticket_id, estimated_resolution_days 
FROM tickets 
WHERE estimated_resolution_days IS NOT NULL
LIMIT 5;
```

## Troubleshooting

### Issue: "estimated_resolution_time" still showing
**Solution**: Database migration not run
```bash
php artisan migrate --force
php artisan cache:clear
```

### Issue: "View Steps" button not working
**Solution**: Check browser console for JavaScript errors, verify routes are cached
```bash
php artisan route:cache
php artisan config:cache
```

### Issue: Steps not saving to database
**Solution**: Check database permissions and connection
```bash
# Verify database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue: Transfer not creating new step
**Solution**: Verify Employee::find() in TicketController works
```bash
php artisan tinker
>>> App\Models\Employee::find(1);
```

## Future Enhancements

1. **Ticket SLA Tracking**: Track if ticket meets estimated_resolution_days SLA
2. **Escalation**: Auto-escalate if ticket exceeds estimated time
3. **Email Notifications**: Notify employees on transfer/completion
4. **Bulk Operations**: Transfer multiple tickets at once
5. **Step Templates**: Pre-defined step descriptions for common issues
6. **Performance Metrics**: Track average steps to resolution by issue type
7. **Archived Steps**: Archive old tickets' step history
8. **Step Dependencies**: Make certain steps required before resolution

## Notes

- All timestamps in UTC
- Employee IDs must exist in employees table
- Transfer creates new step, doesn't modify original
- Cannot transfer if no active/in_progress step exists
- Ticket can have only one "in_progress" step at a time
- Completed steps are immutable (for audit trail integrity)
- Audit trail includes all resolved/closed steps for reference
