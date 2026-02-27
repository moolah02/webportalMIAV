# Revival Technologies — MIAV Dashboard System
## Comprehensive Live Document

**Document Version:** 2.0  
**Last Updated:** February 27, 2026  
**Prepared by:** Revival Technologies Development Team  
**Status:** LIVE — Actively Maintained

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Environments & Access](#2-environments--access)
3. [Server Architecture & Deployment](#3-server-architecture--deployment)
4. [SSH Access & Remote Management](#4-ssh-access--remote-management)
5. [Development Workflow](#5-development-workflow)
6. [Production Deployment Procedure](#6-production-deployment-procedure)
7. [User Manual — Getting Started](#7-user-manual--getting-started)
8. [User Manual — Modules Reference](#8-user-manual--modules-reference)
9. [Ticket System — Full Technical Reference](#9-ticket-system--full-technical-reference)
10. [Staged Resolution System](#10-staged-resolution-system)
11. [API Reference](#11-api-reference)
12. [Role-Based Access Control](#12-role-based-access-control)
13. [System Administration](#13-system-administration)
14. [Testing Guide — Full Suite](#14-testing-guide--full-suite)
15. [CSV Templates & Data Formats](#15-csv-templates--data-formats)
16. [Troubleshooting Reference](#16-troubleshooting-reference)
17. [Database Reference](#17-database-reference)
18. [Security Considerations](#18-security-considerations)

---

## 1. System Overview

The **MIAV Dashboard System** is a full-stack web application built on Laravel (PHP) for Revival Technologies. It provides an end-to-end operations management platform covering:

- POS Terminal asset lifecycle management
- Technician field operations and job assignment
- Multi-step support ticket management with full audit trails
- Project management from creation through closure
- Client relationship management
- Employee, role, and permission management
- Business license and compliance tracking
- Custom report builder with templating
- Internal asset management with approval workflows

**Technology Stack**

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 11.x (PHP 8.2+) |
| Database | MySQL 8.x |
| Frontend | Blade Templates, AdminLTE 3, Tailwind CSS, Vite |
| Authentication | Laravel Sanctum (session + token) |
| Permissions | Spatie Laravel Permission |
| Exports | CSV, PDF (DomPDF) |
| Queue/Cache | File driver (upgradeable to Redis) |

---

## 2. Environments & Access

### 2.1 Production Environment

| Item | Value |
|---|---|
| **URL** | http://51.21.252.67 |
| **Database** | `miav_system` |
| **Web Root** | `/var/www/html/revival_production` |
| **Branch** | `main` |
| **Status** | Live — treat with care |

> **IMPORTANT:** Never run destructive commands, untested migrations, or direct database edits against production without a prior backup and peer review. Always verify on development first.

**Production Admin Credentials**

| Field | Value |
|---|---|
| Email | `admin@miav.com` |
| Role | `super_admin` |
| Default Password | `bcrypt('password')` — change immediately on first login |

---

### 2.2 Development / Testing Environment

| Item | Value |
|---|---|
| **URL** | http://51.21.252.67:8080 *(port must be open — confirm with server admin)* |
| **Database** | `miav_system_dev` — **fully isolated from production** |
| **Web Root** | `/var/www/html/revival_dev` |
| **Branch** | `fix/sessions-fallback` (current working branch) |
| **Purpose** | All feature development, migration testing, and QA |

> Development uses a completely separate database (`miav_system_dev`). No production data is at risk during development testing.

---

### 2.3 Local Development (XAMPP)

| Item | Value |
|---|---|
| **Path** | `c:\xampp4\htdocs\dashboard\Revival_Technologies` |
| **URL** | `http://localhost/dashboard/Revival_Technologies/public` |
| **Database** | Local MySQL via XAMPP |

---

## 3. Server Architecture & Deployment

### 3.1 Server Details

| Component | Detail |
|---|---|
| Cloud Provider | AWS (EC2) |
| OS | Ubuntu 22.04 LTS |
| IP | 51.21.252.67 |
| PHP Version | 8.2 |
| Web Server | Nginx / Apache |
| Process Manager | PHP-FPM (`php8.2-fpm`) |

### 3.2 Directory Structure on Server

```
/var/www/html/
├── revival_production/       ← Production app (branch: main)
│   ├── .env                  ← Production environment file
│   ├── storage/              ← Writable by www-data
│   └── ...
└── revival_dev/              ← Development app (branch: fix/sessions-fallback)
    ├── .env                  ← Dev environment file (DB: miav_system_dev)
    ├── storage/
    └── ...
```

---

## 4. SSH Access & Remote Management

### 4.1 Connecting to the Server

```bash
# Use the .pem key file for authentication (no password required)
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67
```

> **Key file location:** `C:\Users\hp\Downloads\RTLDEV (1).pem`  
> Ensure the key file permissions are restricted. On Windows this is handled automatically; on Linux/Mac run `chmod 400 "RTLDEV (1).pem"` first.

### 4.2 Navigating Between Environments on Server

```bash
# Switch to development environment
cd /var/www/html/revival_dev

# Switch to production environment
cd /var/www/html/revival_production
```

### 4.3 Running Artisan Commands on Server

Always prefix artisan commands with `sudo -u www-data` to run as the web server user and avoid permission issues:

```bash
sudo -u www-data php artisan migrate
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan optimize:clear
```

---

## 5. Development Workflow

### 5.1 Updating the Development Server

Use this workflow when you want to pull new code changes into the development environment for testing:

```bash
# Step 1: SSH into the server
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67

# Step 2: Navigate to the development app directory
cd /var/www/html/revival_dev

# Step 3: Pull the latest changes from the working branch
sudo -u www-data git pull origin fix/sessions-fallback

# Step 4: Run any new database migrations
sudo -u www-data php artisan migrate

# Step 5: Clear and rebuild caches
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
```

### 5.2 Development Testing Checklist

Before promoting any change to production:

- [ ] Feature tested end-to-end on dev environment (http://51.21.252.67:8080)
- [ ] All existing functionality tested — no regressions
- [ ] Migrations run cleanly on `miav_system_dev`
- [ ] Browser console shows no JavaScript errors
- [ ] All relevant test cases from [Section 14](#14-testing-guide--full-suite) pass
- [ ] Code reviewed and approved
- [ ] Changes merged to `main` branch on GitHub

---

## 6. Production Deployment Procedure

> **Pre-requisite:** All changes must be merged to the `main` branch on GitHub before deploying to production.

### 6.1 Standard Deployment Steps

```bash
# Step 1: SSH into the server
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67

# Step 2: Navigate to the production app directory
cd /var/www/html/revival_production

# Step 3: Pull the latest main branch
sudo -u www-data git pull origin main

# Step 4: Run any new database migrations against production database
sudo -u www-data php artisan migrate

# Step 5: Clear all caches and rebuild
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:clear
```

### 6.2 Pre-Deployment Safety Checklist

- [ ] Create a database backup before deploying (see [Section 17.2](#172-database-backup))
- [ ] Confirm changes work on dev environment
- [ ] Notify team of upcoming deployment
- [ ] Perform deployment during low-traffic period if possible
- [ ] Have rollback plan ready (previous commit hash noted)

### 6.3 Rollback Procedure

If a deployment causes issues, rollback immediately:

```bash
cd /var/www/html/revival_production

# Check git log to find the previous stable commit
git log --oneline -10

# Roll back to previous commit
sudo -u www-data git checkout <previous_commit_hash>

# Clear caches after rollback
sudo -u www-data php artisan optimize:clear
```

---

## 7. User Manual — Getting Started

### 7.1 Logging In

1. Navigate to the system URL in your browser.
2. Enter your **email address** and **password**.
3. Click **Login**.

If you have forgotten your password, click the **Forgot Password** link and follow the email reset instructions.

### 7.2 Navigation

The system uses a **left sidebar** for navigation. Each section can be expanded or collapsed. The sidebar contains:

| Section | Purpose |
|---|---|
| Company Dashboard | System-wide overview and KPIs |
| Employee Dashboard | Personal workspace for the logged-in user |
| Assets Management | Asset inventory, terminals, licenses, requests |
| Field Operations | Deployments, job assignments, visits, tickets |
| Project Management | Full project lifecycle |
| Client Management | Client records and individual client dashboards |
| Employee Management | Staff, roles, and permissions |
| Technician Portal | Simplified view for technicians |
| Administration | System settings and configuration |
| Reports & Analytics | Reporting tools and custom report builder |
| My Account | Profile management and sign out |

### 7.3 System Conventions

- **Required fields** are marked with a red asterisk (*).
- **Status badges** are color-coded:
  - Green = Active / Complete
  - Yellow = Pending / In Progress
  - Red = Urgent / Overdue / Error
  - Grey = Inactive / Cancelled
- **Search and filter** controls appear at the top of every list page.
- **Export options** (CSV, PDF) are available on most list pages.
- **Bulk operations** — select multiple records with checkboxes for batch actions.

---

## 8. User Manual — Modules Reference

### 8.1 Company Dashboard

**Path:** Dashboard → Company Dashboard

The Company Dashboard is the default landing page for administrators and managers, providing a high-level system overview:

- **Terminal Metrics** — Total terminals, active count, deployment status
- **Client Count** — Number of active clients
- **Job Statistics** — Pending, in-progress, and completed jobs
- **License Tracking** — Upcoming license expirations
- **System KPIs** — Overall health and performance metrics

---

### 8.2 Employee Dashboard

**Path:** Dashboard → Employee Dashboard

The Employee Dashboard shows work information relevant to the currently logged-in user:

- **My Job Assignments** — Jobs assigned to you with current status
- **Completed Work** — Summary of recently completed tasks
- **Territory Information** — Your assigned regions and areas
- **Recent Activity** — Latest actions performed by you in the system

---

### 8.3 Assets Management

#### 8.3.1 Internal Assets

**Path:** Assets Management → Internal Assets

Manage the company's internal asset inventory including equipment, tools, and supplies.

**Viewing Assets**
- Asset list shows name, category, stock level, and status.
- Use the search bar to find assets by name.
- Use filters to narrow by category or status.

**Adding an Asset**
1. Click **Add New Asset**.
2. Fill in the required fields: name, category, quantity.
3. Fill in any custom fields for the category (e.g., serial number, model).
4. Click **Save**.

**Managing Stock**
- Click on an asset to view its details.
- Use **Update Stock** to adjust quantities (add or remove).
- Use **Bulk Update Stock** from the list page to update multiple assets at once.

**Assigning Assets to Employees**
1. Open an asset's detail page.
2. Click **Assign to Employee**.
3. Select the employee and quantity.
4. The assignment is tracked with dates for return monitoring.

**Low Stock Alerts**
The system automatically flags assets with stock below the minimum threshold. View these under **Alerts → Low Stock**.

---

#### 8.3.2 POS Terminals

**Path:** Assets Management → POS Terminals

Manage point-of-sale terminal records for all clients.

**Adding a Terminal**
1. Click **Add New Terminal**.
2. Enter the terminal ID, merchant name, client, location details, and status.
3. Click **Save**.

**Bulk Import Terminals**
1. Click **Import** from the terminals list.
2. Download the **CSV template** to see the required format.
3. Upload your prepared CSV file.
4. Review the **preview** — map columns to fields if needed.
5. Click **Import** to process. The system reports created, updated, or skipped records.

**Terminal Actions**
From a terminal's detail page:
- **Create a Ticket** — Report an issue with this terminal.
- **Schedule Service** — Set up a maintenance or service visit.
- **Add Notes** — Record information against the terminal.

---

#### 8.3.3 Asset Requests (Shopping Cart)

**Path:** Assets Management → Request Assets / My Requests

**Submitting a Request**
1. Browse available assets in **Request Assets**.
2. Click **Add to Cart** on desired items.
3. Go to **Cart**, review and adjust quantities.
4. Click **Checkout** then **Submit Request**.
5. Your request enters the approval queue.

**Tracking Requests**
- Go to **My Requests** to see request statuses.
- Statuses: Pending, Approved, Rejected, Cancelled.
- Pending requests can be cancelled if no longer needed.

---

#### 8.3.4 Asset Approvals

**Path:** Assets Management → Asset Approvals

**Reviewing and Approving**
1. Open a pending request to see full details.
2. Review items, quantities, and requester justification.
3. Click **Approve** or **Reject** (rejections require a comment).

**Bulk Actions**
- Select multiple requests using checkboxes.
- Click **Bulk Approve** or **Bulk Reject** to process in batch.

---

#### 8.3.5 Business Licenses

**Path:** Assets Management → Business Licenses

Track company licenses, certifications, and compliance documents.

**Adding a License**
1. Click **Add New License**.
2. Enter the license name, type, issuing body, issue and expiration dates.
3. Upload the license document.
4. Click **Save**.

**Renewal Workflow**
1. Open an expiring license.
2. Click **Renew**.
3. Enter the new expiration date and upload the updated document.
4. Click **Process Renewal**.

---

### 8.4 Field Operations

#### 8.4.1 Terminal Deployment

**Path:** Field Operations → Terminal Deployment

The deployment page provides a hierarchical view for planning terminal work assignments.

**Using the Deployment Page**
1. **Select a Client** from the dropdown.
2. The system loads the client's projects and terminals in a tree view.
3. Expand projects to see terminals grouped by region/city.
4. Check terminals to select them for assignment.

**Assigning Terminals to Technicians**
1. Select the terminals to deploy.
2. Choose a technician from the assignment panel (shows current workload).
3. Click **Assign** to create the job assignment.
4. Click **Generate Work Order** to produce a printable/downloadable PDF.

**Tracking Progress**
- The deployment page shows progress bars per project.
- View completion percentages and remaining terminal counts.

---

#### 8.4.2 Job Assignments

**Path:** Field Operations → All Job Assignments

**Creating an Assignment**
1. Click **Create Assignment**.
2. Select client, project, and terminal.
3. Choose the technician.
4. Set the due date and add instructions.
5. Click **Save**.

**Assignment Statuses**

| Status | Meaning |
|---|---|
| Assigned | Job created and assigned, not yet started |
| In Progress | Technician has begun work |
| Completed | Work finished and submitted |
| Cancelled | Job cancelled before completion |

---

#### 8.4.3 Site Visits

**Path:** Field Operations → Site Visits

**Recording a Visit**
1. Click **New Visit**.
2. Look up the terminal by ID or assignment number.
3. Fill in: date, findings, work performed, status.
4. Upload any photos or attachments.
5. Click **Save**.

**Batch Submission**
For submitting multiple visits at once, use the batch import function, upload visit data in the required format, review, and confirm.

---

#### 8.4.4 Support Tickets

**Path:** Field Operations → Support Tickets

Full technical details are in [Section 9](#9-ticket-system--full-technical-reference). From a user perspective:

**Creating a Ticket**
1. Click **Create Ticket**.
2. Select the **Ticket Type**: POS Terminal issue (requires selecting the terminal) or Internal issue.
3. Select the **Assignment Type**: Public (any employee can attend) or Direct (assigned to a specific employee).
4. Set **Priority**: Low, Normal, High, or Emergency.
5. Choose the **Issue Type/Category**.
6. Provide a **Title** and detailed **Description**.
7. Click **Submit**.

**Ticket Statuses**

| Status | Meaning |
|---|---|
| Open | Newly created, awaiting action |
| In Progress | Actively being worked on |
| Pending | Awaiting external input, parts, or confirmation |
| Resolved | Issue has been fixed |
| Closed | Ticket formally closed |

---

### 8.5 Project Management

#### 8.5.1 Creating a New Project

**Path:** Project Management → New Project

**Step 1: Basic Information**
- **Project Name** — Include period, type, and location (e.g., "Q1 2026 Terminal Maintenance - Harare").
- **Client** — Select from active clients. System shows client terminal counts.
- **Project Type** — Maintenance & Repairs, Installation & Setup, or Support & Troubleshooting.

**Step 2: Timeline**
- **Start Date** — Pre-filled with today; adjust as needed.
- **Duration (Days)** — Enter the number of project days.
- **End Date** — Auto-calculated from start date + duration.

**Step 3: Optional Fields**
- Budget, Project Manager, Priority, Description, Notes.

**Step 4: Upload Terminals**
1. Download the **CSV template**.
2. Fill in terminal IDs (and optionally merchant details).
3. Upload the CSV and click **Preview**.
4. The preview modal shows:
   - **Ready to Assign** — Terminals found in the system.
   - **Already Assigned** — Terminals already in this project (will be skipped).
   - **Not Found** — Terminals not in the system (can be created if data is provided).
5. Check/uncheck terminals as needed. Enable "Create missing terminals" to auto-create.
6. Click **Confirm & Assign Terminals**, then **Create Project & Continue**.

#### 8.5.2 Project Closure

**Path:** Project Management → Project Closure and Reports

1. Navigate to the project and click **Closure Wizard**.
2. The wizard steps through confirming terminal completion, reviewing metrics, and generating the closure report.
3. Customize the report if needed, then generate the **PDF report**.
4. Optionally **email the report** to stakeholders.

---

### 8.6 Client Management

#### 8.6.1 Managing Clients

**Path:** Client Management → Clients

**Adding a Client**
1. Click **Add New Client**.
2. Enter: company name, contact person, email, phone, address, region, and status.
3. Click **Save**.

#### 8.6.2 Client Dashboards

**Path:** Client Management → Client Dashboards

Each client has a dedicated dashboard showing terminal inventory, active projects, service history, and analytics. From here you can add terminals, create projects, generate reports, and export data — all filtered to that specific client.

---

### 8.7 Employee Management

#### 8.7.1 Managing Employees

**Path:** Employee Management → Employees

**Adding an Employee**
1. Click **Onboard New Employee**.
2. Enter: first name, last name, email, phone, employee number, department.
3. Assign a **Primary Role** (determines system permissions).
4. Optionally check one or more **Additional Roles**.
5. Set status to Active.
6. Click **Create Employee**.

Employees with multiple roles display color-coded role badges in the list:
- Admin = Green
- Supervisor = Orange
- Technician = Blue

**Deactivating an Employee**
- Open the employee's profile.
- Toggle status to **Inactive**.
- Inactive employees cannot log in.

#### 8.7.2 Role Management

**Path:** Employee Management → Role Management

**Creating a Role**
1. Click **Add New Role**.
2. Enter a name and description.
3. Check the permissions this role should have (see [Section 12](#12-role-based-access-control)).
4. Click **Save**.

**Cloning a Role**
- Click **Clone** on any existing role to create a copy — then modify name and permissions as needed.

---

### 8.8 Technician Portal

#### 8.8.1 Technician Dashboard

**Path:** Technician Portal → Employee Dashboard

Technicians see a simplified, focused view:
- **Pending Jobs** — Assignments waiting to be started
- **In-Progress Jobs** — Current active work
- **Recently Completed** — Finished assignments
- **Territory Map** — Assigned regions and areas

#### 8.8.2 My Job Assignments

**Path:** Technician Portal → My Job Assignment

- View all jobs assigned to you with terminal details, client, and due dates.
- Update job status as you work.
- View work order instructions.
- Submit visit reports when work is done.

---

### 8.9 Reports & Analytics

#### 8.9.1 Reports Dashboard

**Path:** Reports & Analytics → Reports Dashboard

System-wide analytics including terminal status breakdown, service metrics, completion rates, and system health indicators. All data exportable to CSV.

#### 8.9.2 Technician Visit Reports

**Path:** Reports & Analytics → Technician Visits

Filter by technician, date range, terminal, or client. View visit details including field photos. Generate individual PDF reports or export to CSV.

#### 8.9.3 Report Builder

**Path:** Reports & Analytics → Report Builder

> **Access:** Available to ALL authenticated users, regardless of role — no permission errors should occur here.

1. **Select Data Source** — Clients, Projects, Terminals, Regions.
2. **Set Filters** — Narrow by client, project, date range, region, or terminal status.
3. **Choose Columns** — Select which fields to include.
4. **Run Report** — Click **Run Report** to preview results.
5. **Export** — Download as CSV.

**Saving Report Templates**
- Click **Save Template** after configuring a report.
- Saved templates appear in the template list for quick reuse.
- Templates can be duplicated to create variations.

---

## 9. Ticket System — Full Technical Reference

### 9.1 Overview

The ticket system handles both POS terminal issues and internal company issues with a flexible assignment model and a multi-step staged resolution workflow.

### 9.2 Ticket Types

| Type | Description | Required Fields |
|---|---|---|
| `pos_terminal` | Issue with a specific POS terminal | `pos_terminal_id` (required) |
| `internal` | General internal company issue | Terminal optional |

### 9.3 Assignment Types

| Type | Description | Required Fields |
|---|---|---|
| `public` | Any employee with permissions can view and claim the ticket | None |
| `direct` | Assigned directly to a specific employee | `assigned_to` (employee ID, required) |

### 9.4 Ticket Priorities

| Priority | Response Expectation |
|---|---|
| Low | Attend when convenient |
| Normal | Standard SLA applies |
| High | Prioritise above normal tickets |
| Emergency | Immediate response required |

### 9.5 Database Schema — tickets table (relevant columns)

```sql
ticket_type         ENUM('pos_terminal', 'internal')   DEFAULT 'pos_terminal'
assignment_type     ENUM('public', 'direct')            DEFAULT 'public'
estimated_resolution_days  INT  NULLABLE               -- Days-based (not minutes)
status              ENUM('open','in_progress','pending','resolved','closed')
priority            ENUM('low','normal','high','emergency')
```

### 9.6 Filtering Tickets

**Web UI query parameters:**

```
?ticket_type=pos_terminal    Show only POS terminal tickets
?ticket_type=internal        Show only internal tickets
?assignment_type=public      Show only public tickets
?assignment_type=direct      Show only direct (assigned) tickets
?pending=true                Show open + in_progress + pending tickets
```

**Pending status** includes: `open`, `in_progress`, and `pending` — not resolved or closed.

### 9.7 Ticket Statistics Available

```php
$stats = [
    'open'          => // Count of open tickets
    'in_progress'   => // Count of in-progress tickets
    'pending'       => // Count of open/in_progress/pending tickets combined
    'resolved'      => // Count of resolved tickets
    'critical'      => // Count of emergency priority tickets
    'pos_terminal'  => // Count of POS terminal tickets
    'internal'      => // Count of internal tickets
    'public'        => // Count of public tickets
    'direct'        => // Count of direct assignment tickets
]
```

---

## 10. Staged Resolution System

### 10.1 Overview

For complex issues, the ticket system supports multi-step resolution with full audit trail tracking. Each step can be handled by a different employee, with all transfers and work documented.

### 10.2 Key Concepts

- **Steps** are numbered work units within a ticket.
- Each step has a **status**: `in_progress`, `completed`, `transferred`, or `resolved`.
- Only one step can be `in_progress` at any time.
- Completed and transferred steps are **immutable** (preserving audit trail integrity).
- Estimated resolution time is tracked in **days**, not minutes.

### 10.3 Database Schema — ticket_steps table

```sql
CREATE TABLE ticket_steps (
  id                    BIGINT PRIMARY KEY AUTO_INCREMENT,
  ticket_id             BIGINT NOT NULL,
  employee_id           BIGINT NOT NULL,
  step_number           INT NOT NULL,
  status                ENUM('in_progress', 'completed', 'transferred', 'resolved') NOT NULL,
  description           VARCHAR(255) NOT NULL,
  notes                 TEXT NULLABLE,
  resolution_notes      TEXT NULLABLE,
  transferred_reason    VARCHAR(255) NULLABLE,
  transferred_to        BIGINT NULLABLE,
  completed_at          TIMESTAMP NULLABLE,
  created_at            TIMESTAMP,
  updated_at            TIMESTAMP,

  FOREIGN KEY (ticket_id)      REFERENCES tickets(id),
  FOREIGN KEY (employee_id)    REFERENCES employees(id),
  FOREIGN KEY (transferred_to) REFERENCES employees(id),
  INDEX (ticket_id),
  INDEX (employee_id),
  INDEX (status)
);
```

### 10.4 Resolution Workflows

#### Scenario A — Single Employee Resolution
1. Employee creates ticket → initial step auto-created: *"Ticket created and opened"*.
2. Employee opens **View Steps** and adds work steps with descriptions and notes.
3. Employee marks final step complete with resolution notes.
4. System marks ticket as **Resolved**.

#### Scenario B — Multi-step Resolution with Transfer
1. Employee 1 creates ticket → initial step auto-created.
2. Employee 1 adds step: *"Diagnosed as hardware issue".*
3. Employee 1 clicks **Complete & Transfer**, fills in:
   - Transfer to: Employee 2
   - Reason: *"Requires hardware replacement expertise"*
   - Work done: *"Identified faulty power supply"*
4. Employee 2 sees the transferred ticket in their queue.
5. Employee 2 adds steps, works through the fix.
6. Employee 2 resolves the ticket: *"Power supply replacement successful."*

### 10.5 Audit Trail

From the ticket detail view, click **View Steps** to see the complete, immutable history:

- Who did the work and when
- Description of each step performed
- Reason for any transfers
- Resolution notes for completed steps
- Final resolution summary

### 10.6 Controller Methods (TicketController)

| Method | Route | Purpose |
|---|---|---|
| `addStep()` | `POST /tickets/{ticket}/steps` | Add a new work step |
| `completeStep()` | `PATCH /tickets/{ticket}/steps/{step}/complete` | Mark a step as completed |
| `transferStep()` | `POST /tickets/{ticket}/steps/{step}/transfer` | Transfer ticket to another employee |
| `resolveTicket()` | `PATCH /tickets/{ticket}/resolve` | Mark the entire ticket as resolved |
| `getAuditTrail()` | `GET /tickets/{ticket}/audit-trail` | Retrieve complete audit trail |

---

## 11. API Reference

### 11.1 Authentication

The API uses token-based authentication via Laravel Sanctum. Include the token in the `Authorization` header:

```
Authorization: Bearer {your_api_token}
```

### 11.2 Ticket Endpoints

#### List Tickets

```http
GET /api/tickets
```

**Query Parameters:**

| Parameter | Type | Description |
|---|---|---|
| `ticket_type` | string | `pos_terminal` or `internal` |
| `assignment_type` | string | `public` or `direct` |
| `pending` | boolean | `true` to show open/in_progress/pending only |
| `status` | string | Filter by specific status |
| `priority` | string | Filter by priority level |

---

#### Create Ticket

```http
POST /api/tickets
Content-Type: application/json
```

**Request Body:**

```json
{
  "title": "Terminal Not Responding",
  "description": "POS terminal 045 is unresponsive since 09:00",
  "issue_type": "hardware_malfunction",
  "priority": "urgent",
  "ticket_type": "pos_terminal",
  "assignment_type": "public",
  "pos_terminal_id": 45
}
```

**For direct assignment:**

```json
{
  "title": "Database Sync Issue",
  "description": "Transaction sync failure detected",
  "issue_type": "software_issue",
  "priority": "high",
  "ticket_type": "internal",
  "assignment_type": "direct",
  "assigned_to": 12
}
```

**Validation Rules:**
- `ticket_type = 'pos_terminal'` → `pos_terminal_id` is **required**
- `assignment_type = 'direct'` → `assigned_to` is **required**

---

#### Add Work Step to Ticket

```http
POST /api/tickets/{ticket_id}/steps
Content-Type: application/json

{
  "description": "Diagnosed network connectivity issue",
  "notes": "Checked IP configuration and DNS settings"
}
```

**Response:**

```json
{
  "message": "Work step added successfully",
  "step": {
    "id": 3,
    "ticket_id": 1,
    "step_number": 2,
    "employee_id": 5,
    "status": "in_progress",
    "description": "Diagnosed network connectivity issue",
    "notes": "Checked IP configuration and DNS settings"
  }
}
```

---

#### Complete a Step

```http
PATCH /api/tickets/{ticket_id}/steps/{step_id}/complete
Content-Type: application/json

{
  "resolution_notes": "Issue was DNS misconfiguration, reconfigured to use 8.8.8.8"
}
```

---

#### Transfer a Step

```http
POST /api/tickets/{ticket_id}/steps/{step_id}/transfer
Content-Type: application/json

{
  "transferred_to": 7,
  "transferred_reason": "Requires specialist hardware knowledge",
  "notes": "Completed software diagnosis, hardware fault confirmed"
}
```

---

#### Resolve a Ticket

```http
PATCH /api/tickets/{ticket_id}/resolve
Content-Type: application/json

{
  "resolution_notes": "Hardware replaced successfully. System fully operational."
}
```

---

#### Get Audit Trail

```http
GET /api/tickets/{ticket_id}/audit-trail
```

**Response:**

```json
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
      "completed_at": "2026-02-01 10:30:00"
    }
  ]
}
```

---

### 11.3 Common Ticket Filter Examples

```http
# All pending POS terminal tickets
GET /api/tickets?ticket_type=pos_terminal&pending=true

# All high-priority direct-assigned tickets
GET /api/tickets?assignment_type=direct&priority=high

# All open internal tickets
GET /api/tickets?ticket_type=internal&status=open

# All public tickets across all types
GET /api/tickets?assignment_type=public
```

---

## 12. Role-Based Access Control

### 12.1 How It Works

Every employee is assigned a **Primary Role** plus optional **Additional Roles**. Permissions are additive — if any role grants a permission, the employee has it.

The system uses **Spatie Laravel Permission** under the hood.

### 12.2 Permission Reference

| Permission | Description |
|---|---|
| `all` | Full system access (Super Admin) |
| `view_dashboard` | Access the main dashboard |
| `manage_clients` | Create, edit, delete clients |
| `view_clients` | View client information |
| `manage_terminals` | Create, edit, delete terminals |
| `view_terminals` | View terminal information |
| `manage_projects` | Create, edit, close projects |
| `view_projects` | View project information |
| `manage_jobs` | Create and manage job assignments |
| `assign_jobs` | Assign jobs to technicians |
| `view_jobs` | View job assignments |
| `manage_assets` | Create, edit, manage assets |
| `view_assets` | View asset inventory |
| `approve_requests` | Approve or reject asset requests |
| `manage_visits` | Create and manage site visits |
| `view_visits` | View site visit records |
| `manage_tickets` | Create and manage support tickets |
| `view_tickets` | View support tickets |
| `manage_roles` | Create and configure roles |
| `manage_employees` | Create and manage employees |
| `view_employees` | View employee records |
| `manage_settings` | Access system settings |
| `view_reports` | View reports and analytics |
| `export_reports` | Export report data |
| `view_own_data` | View own profile and data |
| `view_own_requests` | View own asset requests |

### 12.3 Standard Role Profiles

| Role | Typical Permissions |
|---|---|
| **Super Admin** | `all` — full access to everything |
| **Project Manager** | manage_projects, view_clients, view_terminals, assign_jobs, view_reports |
| **Technician** | view_own_data, view_jobs, manage_visits, manage_tickets, view_reports |
| **Office Admin** | manage_clients, manage_employees, manage_assets, approve_requests |
| **Supervisor** | manage_jobs, view_employees, view_reports, manage_tickets |
| **Viewer** | view_dashboard, view_reports, view_clients, view_terminals |

### 12.4 Report Builder Access

> The Report Builder (`/reports/builder`) is accessible to **all authenticated users** regardless of role. No specific permission is required. Employees should not receive a 403 error on this page.

---

## 13. System Administration

### 13.1 System Settings

**Path:** Administration → System Settings

#### Category Management

Manage system-wide categories used across the application including: asset statuses, ticket priorities, issue types, terminal statuses.

**Adding a category:**
1. Select the category type from the list.
2. Click **Add New**.
3. Enter name and optional description.
4. Click **Save**.

Categories support drag-and-drop reordering.

#### Department Management

Manage organisational departments used in employee records: Add, edit, or delete departments as needed.

#### Asset Category Field Management

Define custom fields for specific asset categories:
1. Select an asset category.
2. Click **Add Field**.
3. Configure: field name, field type (text, number, dropdown, etc.), and whether required.
4. Reorder fields to control display order.

These custom fields appear automatically when creating or editing assets of that category.

---

### 13.2 Seed Data / Default Records

The system ships with the following seeded records:

| Type | Value |
|---|---|
| Super Admin Email | `admin@miav.com` |
| Super Admin Role | `super_admin` |
| Employee Number | `EMP001` |
| Department | `IT` |
| Start Date | `2024-01-01` |
| Status | `active` |

---

## 14. Testing Guide — Full Suite

### 14.1 Prerequisites

| Item | Detail |
|---|---|
| **Development URL** | http://51.21.252.67:8080 |
| **Production URL** | http://51.21.252.67 |
| **Browser** | Chrome, Firefox, or Edge (latest) |
| **Credentials** | Use assigned employee credentials |

> All new features must be tested against the **development environment** first.

---

### 14.2 Authentication Tests

#### Test 1.1 — Valid Login
1. Navigate to `[ENV_URL]/login`
2. Enter valid email and password
3. Click Login

**Expected:** Logged in, redirected to dashboard, username shown in header.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 1.2 — Invalid Login
1. Navigate to login, enter incorrect password
2. Click Login

**Expected:** Error message displayed, not logged in.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 1.3 — Session Expiry
1. Login, leave the browser idle for extended period
2. Attempt to perform an action

**Expected:** Session expired, redirected to login. Re-login works successfully.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.3 Employee Management Tests

#### Test 2.1 — View Employees
1. Login as Admin, navigate to Employee Management → Employees

**Expected:** Employee list with name, email, department, status, and colored role badges.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 2.2 — Create Employee (Single Role)
1. Click Onboard New Employee
2. Fill required fields, set Primary Role to Technician, no Additional Roles
3. Click Create Employee

**Expected:** Success message, unique employee number, one role badge visible.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 2.3 — Create Employee (Multiple Roles)
1. Click Onboard New Employee, fill fields
2. Primary Role: Technician, also check Supervisor in Additional Roles
3. Click Create Employee

**Expected:** Employee created with two colored role badges (Technician, Supervisor).
**Status:** `[ ] Pass  [ ] Fail`

#### Test 2.4 — Edit Employee — Add Roles
1. Find employee with one role, click edit
2. Check one or more Additional Roles, click Update

**Expected:** Employee now shows multiple role badges.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 2.5 — Edit Employee — Remove Roles
1. Find employee with multiple roles, click edit
2. Uncheck all Additional Roles, keep Primary Role, click Update

**Expected:** Employee now shows only one role badge (Primary Role).
**Status:** `[ ] Pass  [ ] Fail`

#### Test 2.6 — Search and Filter Employees
1. Use search box, then apply department, status, and role filters

**Expected:** All filters work correctly and can be combined.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.4 Report Builder Tests

#### Test 3.1 — Access Report Builder (Any User)
1. Login with any account (Admin, Supervisor, Technician, etc.)
2. Navigate to Reports & Analytics → Report Builder

**Expected:** Page loads. No 403 error. Available to all authenticated users.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 3.2 — Run Custom Report
1. Select data source, choose fields, add optional filters
2. Click Run Report

**Expected:** Report executes and results display in table format.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 3.3 — Export Report to CSV
1. Run a report, click Export → CSV Format

**Expected:** CSV file downloads with correct data. No permission error.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 3.4 — Load Report Template
1. Click Load Template, select a saved template, click Load

**Expected:** Template loads, report configuration populated, report can be run.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.5 Asset Management Tests

#### Test 4.1 — View Assets
**Expected:** Asset list with details, status, location, assignment. Pagination works.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 4.2 — Create New Asset
**Expected:** Asset created with unique tag, visible in inventory.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 4.3 — Assign Asset to Employee
**Expected:** Asset assigned, status updates to Assigned, employee can see it in their profile.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.6 Ticket System Tests

#### Test 5.1 — Create POS Terminal Ticket (Public)
1. Create ticket with type=pos_terminal, assignment=public, select a terminal
**Expected:** Ticket created successfully.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 5.2 — Create POS Terminal Ticket Without Terminal ID
1. Attempt to create pos_terminal ticket without selecting a terminal
**Expected:** Validation error: pos_terminal_id is required.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 5.3 — Create Direct Ticket Without Employee
1. Attempt to create direct assignment ticket without assigning an employee
**Expected:** Validation error: assigned_to is required.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 5.4 — Create Internal Ticket (Direct)
1. Create internal ticket with assignment=direct, select employee
**Expected:** Ticket created, assigned to specified employee.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 5.5 — Filter Tickets by Type
1. Filter by `ticket_type=internal`, then by `ticket_type=pos_terminal`
**Expected:** Only matching tickets displayed for each filter.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 5.6 — View Pending Tickets
1. Apply pending filter
**Expected:** Only open, in_progress, and pending tickets shown. Resolved/closed excluded.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.7 Staged Resolution Tests

#### Test 6.1 — Auto-create Initial Step
1. Create a new ticket
**Expected:** An initial step "Ticket created and opened" is automatically created.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 6.2 — Add Work Step
1. Open ticket, click View Steps, add a work step with description and notes
**Expected:** Step saved with auto-incremented step number and in_progress status.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 6.3 — Complete a Step
1. Open a step, click Complete, add resolution notes
**Expected:** Step marked completed with timestamp, cannot be edited (immutable).
**Status:** `[ ] Pass  [ ] Fail`

#### Test 6.4 — Transfer Ticket
1. On a step, click Complete & Transfer
2. Select target employee, enter reason and notes

**Expected:** Current step marked transferred, new step created for target employee.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 6.5 — View Full Audit Trail
1. Open ticket, click View Steps
**Expected:** Complete chronological history of all steps, employees, timestamps, and transfer reasons.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 6.6 — Resolve Ticket
1. Open ticket steps, click Resolve, add final resolution notes
**Expected:** Ticket status changes to Resolved. No further steps can be added.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 6.7 — Estimated Days Display
1. Create a ticket, check resolution time field label
**Expected:** Field displayed as "Estimated Resolution Days" — not minutes.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.8 Job Assignment Tests (Technician)

#### Test 7.1 — View Assigned Jobs (as Technician)
**Expected:** Only jobs assigned to the logged-in technician displayed.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 7.2 — Update Job Status
1. Open a job, change status, add notes, save
**Expected:** Status updated, notes saved with timestamp.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.9 Project Management Tests

#### Test 8.1 — Create Project
**Expected:** Project created, visible in list, manager can access it.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 8.2 — Upload Terminals via CSV
**Expected:** Preview shows ready/already assigned/not-found categories. Only ready terminals imported.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 8.3 — Project Closure
**Expected:** Closure wizard completes, PDF report generated.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.10 Permissions & Role Tests

#### Test 10.1 — Admin Full Access
**Expected:** Admin can access all modules with no permission denied errors.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 10.2 — Technician Restricted Access
**Expected:** Technician can access Jobs, Report Builder, Profile. Appropriate 403 for admin areas.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 10.3 — Unauthorized Access Prevention
1. Logout, try to access a protected URL directly
**Expected:** Redirected to login. Session cleared.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.11 Security Tests

#### Test 15.1 — SQL Injection Prevention
1. Enter `' OR '1'='1` in search fields
**Expected:** Input sanitised. No database error or unauthorized data returned.
**Status:** `[ ] Pass  [ ] Fail`

#### Test 15.2 — XSS Prevention
1. Enter `<script>alert('test')</script>` in text fields, submit and view
**Expected:** Script not executed. Content displayed as escaped text or stripped.
**Status:** `[ ] Pass  [ ] Fail`

---

### 14.12 Test Summary Sheet

| Category | Total Tests | Passed | Failed |
|---|---|---|---|
| Authentication | 3 | | |
| Employee Management | 6 | | |
| Report Builder | 4 | | |
| Asset Management | 3 | | |
| Ticket System | 6 | | |
| Staged Resolution | 7 | | |
| Job Assignments | 2 | | |
| Project Management | 3 | | |
| Permissions | 3 | | |
| Security | 2 | | |
| **TOTAL** | **39** | | |

**Tester Name:** _______________________  
**Date:** _______________________  
**Sign-off:** _______________________

---

## 15. CSV Templates & Data Formats

### 15.1 Terminal Import Template

```csv
terminal_id,merchant_name,merchant_phone,physical_address,city,region,status
77202134,ABC Store,+27123456789,123 Main St,Johannesburg,Gauteng,active
77202135,XYZ Shop,+27987654321,456 High St,Cape Town,Western Cape,active
```

### 15.2 Project Terminal Upload — Simple (Existing Terminals Only)

```csv
terminal_id
77202134
77202135
77202136
```

### 15.3 Project Terminal Upload — Full (Create Missing Terminals)

```csv
terminal_id,merchant_name,merchant_phone,physical_address,city,region,status
77202134,ABC Store,+27123456789,123 Main St,Johannesburg,Gauteng,active
```

### 15.4 Import Rules

- Always use the **system-provided templates** — do not change column names.
- Terminal IDs must be unique across the system.
- `status` must be one of: `active`, `inactive`, `in-service`.
- Avoid special characters in merchant names unless quoted.
- Date fields must be in `YYYY-MM-DD` format.

---

## 16. Troubleshooting Reference

### 16.1 Common User Issues

| Issue | Solution |
|---|---|
| Cannot log in | Check email/password. Contact admin if account is deactivated. |
| Page shows "403 Forbidden" | Your role does not have permission for this page. Contact admin to update your role. |
| Report Builder shows 403 | Permission configuration issue — admin must grant basic access or check Spatie seeder. |
| CSV import fails | Ensure file matches the template exactly. Check for special characters, extra columns, or BOM encoding. |
| Terminal not found during upload | Verify the terminal ID exists in the system and is assigned to the correct client. |
| Report shows no data | Broaden filter criteria. Try removing date range filters or expanding client selection. |
| Slow page loading | Large datasets take longer. Narrow filter criteria or export and process externally. |
| Images not loading | Storage symlink may be missing. Run `php artisan storage:link` on the server. |

### 16.2 Common Server/Development Issues

| Issue | Cause | Solution |
|---|---|---|
| `estimated_resolution_time` still showing | Migration not run | `sudo -u www-data php artisan migrate --force` |
| View Steps button not working | Cached routes | `php artisan route:cache && php artisan config:cache` |
| Steps not saving to database | DB connection or permissions issue | Check `.env` DB settings; verify `www-data` has write access |
| Transfer not creating new step | Employee lookup failing | Test `App\Models\Employee::find($id)` in tinker |
| 500 Server Error after deploy | Config/cache stale | Run full `php artisan optimize:clear` |
| `.env` changes not taking effect | Config cached | `php artisan config:clear && php artisan config:cache` |
| Permission denied on `storage/` | Wrong file ownership | `sudo chown -R www-data:www-data storage/ bootstrap/cache/` |

### 16.3 Diagnostic Commands

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check if tables exist
>>> DB::table('ticket_steps')->count();

# Verify employee lookup works
>>> App\Models\Employee::find(1);

# Check current route cache
php artisan route:list | grep ticket

# View latest log entries
tail -100 storage/logs/laravel.log
```

---

## 17. Database Reference

### 17.1 Databases

| Environment | Database Name |
|---|---|
| Production | `miav_system` |
| Development | `miav_system_dev` |
| Local | Local MySQL (via XAMPP config) |

### 17.2 Database Backup

**Before any production deployment**, take a backup:

```bash
# SSH into server first
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67

# Create a timestamped backup of the production database
mysqldump -u [db_user] -p miav_system > /var/backups/miav_system_$(date +%Y%m%d_%H%M%S).sql

# Or use the backup stored in the project
cp /var/www/html/revival_production/database/backups/latest.sql \
   /var/backups/pre_deploy_$(date +%Y%m%d).sql
```

### 17.3 Key Tables

| Table | Purpose |
|---|---|
| `employees` | All employee records |
| `roles` | Defined roles |
| `permissions` | All system permissions |
| `model_has_roles` | Employee ↔ Role assignments |
| `role_has_permissions` | Role ↔ Permission assignments |
| `clients` | Client company records |
| `pos_terminals` | POS terminal assets |
| `projects` | Project lifecycle records |
| `job_assignments` | Technician job assignments |
| `site_visits` | Field technician visit records |
| `tickets` | Support tickets |
| `ticket_steps` | Multi-step resolution steps (audit trail) |
| `assets` | Internal company assets |
| `asset_requests` | Employee asset requests |
| `business_licenses` | License and compliance records |

### 17.4 Key Migration Files

| Migration | Purpose |
|---|---|
| `..._create_tickets_table.php` | Core tickets table |
| `2026_01_21_add_ticket_type_and_assignment_type.php` | Adds `ticket_type`, `assignment_type` columns |
| `..._create_ticket_steps_table.php` | Creates staged resolution steps table |
| `..._change_estimated_resolution_to_days.php` | Converts time field from minutes to days |

---

## 18. Security Considerations

### 18.1 Authentication & Session

- All routes are guarded by Laravel's `auth` middleware.
- Sessions expire after a configured timeout — users are redirected to login.
- CSRF tokens protect all form submissions.
- API routes use Sanctum token authentication.

### 18.2 Input Validation & SQL Injection

- All user inputs are validated through Laravel Form Requests before reaching the database.
- Eloquent ORM is used throughout — parameterised queries prevent SQL injection.
- Never use raw `DB::statement()` with unsanitised user data.

### 18.3 XSS Protection

- All output is escaped using Blade's `{{ }}` syntax.
- User-supplied HTML content is not rendered raw unless explicitly reviewed and sanitised.

### 18.4 Authorization

- Every controller action checks permissions via `$this->authorize()` or `canAccessModule()` middleware.
- Role permissions are enforced at the middleware layer — a missing permission results in a redirect with an error, not a silent access grant.

### 18.5 File Uploads

- Uploaded files (CSV imports, license documents, visit photos) are validated for type and size.
- Uploaded files are stored in `storage/` (not directly in `public/`) and served via routes where permission checks apply.

### 18.6 SSH Key Security

- The `.pem` key file (`RTLDEV (1).pem`) must never be committed to version control.
- Store the key securely. If the key is compromised, rotate it immediately through the AWS console.

---

## Appendix — Quick Reference Card

### Environment URLs

| Environment | URL | Database |
|---|---|---|
| Production | http://51.21.252.67 | `miav_system` |
| Development | http://51.21.252.67:8080 | `miav_system_dev` |

### Connect to Server

```bash
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67
```

### Update Development

```bash
cd /var/www/html/revival_dev
sudo -u www-data git pull origin fix/sessions-fallback
sudo -u www-data php artisan migrate
sudo -u www-data php artisan cache:clear
```

### Deploy to Production

```bash
# On GitHub: merge your branch to main first
cd /var/www/html/revival_production
sudo -u www-data git pull origin main
sudo -u www-data php artisan migrate
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
```

### Clear All Caches

```bash
sudo -u www-data php artisan optimize:clear
```

### View Logs

```bash
tail -f /var/www/html/revival_production/storage/logs/laravel.log
```

---

*Revival Technologies — MIAV Dashboard System*  
*Document Version 2.0 | February 27, 2026*  
*For system support, contact your system administrator at admin@miav.com*
