# Revival Technologies - System User Manual

**MIAV Dashboard System**
Version 1.0 | January 2026

---

## Table of Contents

1. [Getting Started](#1-getting-started)
2. [Company Dashboard](#2-company-dashboard)
3. [Employee Dashboard](#3-employee-dashboard)
4. [Assets Management](#4-assets-management)
5. [Field Operations](#5-field-operations)
6. [Project Management](#6-project-management)
7. [Client Management](#7-client-management)
8. [Employee Management](#8-employee-management)
9. [Technician Portal](#9-technician-portal)
10. [Reports & Analytics](#10-reports--analytics)
11. [Administration](#11-administration)
12. [User Roles & Permissions](#12-user-roles--permissions)

---

## 1. Getting Started

### 1.1 Logging In

1. Navigate to the system URL in your browser.
2. Enter your email address and password.
3. Click **Login**.

If you have forgotten your password, click the **Forgot Password** link and follow the email instructions.

### 1.2 Navigation

The system uses a left sidebar for navigation. Each section can be expanded or collapsed by clicking the section header. The sidebar contains:

- **Company Dashboard** - System overview and metrics
- **Employee Dashboard** - Personal workspace
- **Assets Management** - Asset inventory, terminals, licenses
- **Field Operations** - Deployment, jobs, visits, tickets
- **Project Management** - Projects, closure, reports
- **Client Management** - Client records and dashboards
- **Employee Management** - Staff and roles
- **Technician Portal** - Technician-specific views
- **Administration** - System settings
- **Reports & Analytics** - Reporting tools
- **My Account** - Profile and sign out

### 1.3 System Conventions

- **Required fields** are marked with a red asterisk (*).
- **Status badges** are color-coded: green (active/complete), yellow (pending), red (urgent/overdue), grey (inactive).
- **Search and filter** controls appear at the top of list pages.
- **Export** options (CSV, PDF) are available on most list pages.

---

## 2. Company Dashboard

**Path:** Dashboard > Company Dashboard

The Company Dashboard provides a high-level overview of system activity and key performance indicators.

### What You See

- **Terminal Metrics** - Total terminals, active count, deployment status
- **Client Count** - Number of active clients
- **Job Statistics** - Pending, in-progress, and completed jobs
- **License Tracking** - Upcoming license expirations
- **System Health** - Overall system metrics and KPIs

This dashboard is the default landing page for administrators and managers.

---

## 3. Employee Dashboard

**Path:** Dashboard > Employee Dashboard

The Employee Dashboard shows personal work information relevant to the logged-in user.

### What You See

- **My Job Assignments** - Jobs assigned to you with status
- **Completed Work** - Summary of completed tasks
- **Territory Information** - Your assigned regions/areas
- **Recent Activity** - Your latest actions in the system

---

## 4. Assets Management

### 4.1 Internal Assets

**Path:** Assets Management > Internal Assets

Manage the company's internal asset inventory including equipment, tools, and supplies.

#### Viewing Assets
- The asset list shows all items with name, category, stock level, and status.
- Use the **search bar** to find assets by name.
- Use **filters** to narrow by category or status.

#### Adding an Asset
1. Click **Add New Asset**.
2. Fill in the required fields: name, category, quantity.
3. If the category has custom fields (e.g., serial number, model), fill those in as well.
4. Click **Save**.

#### Managing Stock
- Click on an asset to view its details.
- Use **Update Stock** to adjust quantities (add or remove).
- Use **Bulk Update Stock** from the list page to update multiple assets at once.

#### Assigning Assets to Employees
1. Open an asset's detail page.
2. Click **Assign to Employee**.
3. Select the employee and quantity.
4. The assignment is tracked with dates for return monitoring.

#### Low Stock Alerts
The system automatically flags assets with stock below the minimum threshold. View these under **Alerts > Low Stock**.

#### Exporting Assets
Click **Export CSV** from the asset list to download a spreadsheet of all assets.

---

### 4.2 POS Terminals

**Path:** Assets Management > POS Terminals

Manage point-of-sale terminal records for all clients.

#### Viewing Terminals
- Browse the terminal list with filters for client, region, city, and status.
- Use the search bar to find terminals by terminal ID or merchant name.

#### Adding a Terminal
1. Click **Add New Terminal**.
2. Enter the terminal ID, merchant name, client, location details, and status.
3. Click **Save**.

#### Importing Terminals (Bulk)
1. Click **Import** from the terminals list.
2. Download the **CSV template** to see the required format.
3. Upload your CSV file.
4. The system shows a **preview** of the data and allows you to map columns to fields.
5. Review the preview and click **Import** to process.
6. The system reports how many terminals were created, updated, or skipped.

#### Terminal Actions
From a terminal's detail page, you can:
- **Create a Ticket** - Report an issue with this terminal.
- **Schedule Service** - Set up a maintenance or service visit.
- **Add Notes** - Record information about the terminal.

#### Exporting Terminals
Click **Export** to download terminal data as CSV.

---

### 4.3 Asset Requests (Shopping Cart)

**Path:** Assets Management > Request Assets / My Requests

Employees can browse and request assets through a shopping cart workflow.

#### Browsing the Catalog
1. Go to **Request Assets** to view available assets.
2. Each item shows availability and description.
3. Click **Add to Cart** to add items.

#### Managing Your Cart
1. Go to **Cart** to review selected items.
2. Adjust quantities or remove items.
3. Click **Checkout** when ready.

#### Submitting a Request
1. At checkout, review your items.
2. Add any notes or justification.
3. Click **Submit Request**.
4. Your request enters the approval queue.

#### Tracking Requests
- Go to **My Requests** to see the status of all your requests.
- Statuses: Pending, Approved, Rejected, Cancelled.
- You can **cancel** a pending request if no longer needed.

---

### 4.4 Asset Approvals

**Path:** Assets Management > Asset Approvals

Managers review and action employee asset requests.

#### Reviewing Requests
- The approval page lists all pending requests.
- Click on a request to see full details including items, quantities, and requester info.

#### Approving or Rejecting
1. Open a request.
2. Review the items and justification.
3. Click **Approve** or **Reject**.
4. Add a comment if needed (required for rejections).

#### Bulk Actions
- Select multiple requests using checkboxes.
- Click **Bulk Approve** or **Bulk Reject** to process in batch.

#### Approval Reports
- View approval statistics and trends.
- Export approval data for reporting.

---

### 4.5 Business Licenses

**Path:** Assets Management > Business Licenses

Track company licenses, certifications, and compliance documents.

#### Adding a License
1. Click **Add New License**.
2. Enter the license name, type, issuing body, dates, and upload the document.
3. Click **Save**.

#### Renewal Workflow
1. Open a license that is expiring.
2. Click **Renew**.
3. Enter the new expiration date and upload the updated document.
4. Click **Process Renewal**.

#### Reports
- **Expiring Licenses** - View licenses expiring soon.
- **Compliance Report** - Overview of all license statuses.

---

## 5. Field Operations

### 5.1 Terminal Deployment

**Path:** Field Operations > Terminal Deployment

The deployment page provides a hierarchical view for planning and assigning terminal work to technicians.

#### Using the Deployment Page
1. **Select a Client** from the dropdown.
2. The system loads the client's projects and terminals in a tree view.
3. Expand projects to see terminals grouped by region/city.
4. Select terminals to assign by checking boxes.

#### Assigning Terminals to Technicians
1. Select the terminals to deploy.
2. Choose a technician from the assignment panel.
3. The system shows the technician's current workload.
4. Click **Assign** to create the job assignment.

#### Bulk Assignment
- Select multiple terminals across projects.
- Click **Bulk Assign** to assign them all to one technician.

#### Work Orders
- After creating assignments, click **Generate Work Order**.
- Work orders can be printed or downloaded as PDF for field use.

#### Tracking Progress
- The deployment page shows progress bars for each project.
- Track completion percentages and remaining work.

#### Exporting
- Export assignment data in various formats for external use.

---

### 5.2 Job Assignments

**Path:** Field Operations > All Job Assignments

Create and manage individual job assignments for technicians.

#### Viewing Assignments
- The list shows all assignments with technician, terminal, status, and dates.
- Filter by status, technician, client, or region.

#### Creating an Assignment
1. Click **Create Assignment**.
2. Select the client, project, and terminal.
3. Choose the technician.
4. Set the due date and add instructions.
5. Click **Save**.

#### Updating Status
Assignments progress through these statuses:
- **Assigned** - Job created and assigned to technician
- **In Progress** - Technician has started work
- **Completed** - Work finished
- **Cancelled** - Job cancelled

To update: open the assignment and click the appropriate status button.

---

### 5.3 Site Visits

**Path:** Field Operations > Site Visits

Record and track technician visits to terminal locations.

#### Recording a Visit
1. Click **New Visit**.
2. Look up the terminal by ID or assignment number.
3. Fill in the visit details: date, findings, work performed, status.
4. Upload any photos or attachments.
5. Click **Save**.

#### Batch Submission
For submitting multiple visits at once:
1. Use the batch import function.
2. Upload visit data in the required format.
3. Review and confirm.

#### Viewing Visits
- Browse all visits with filters for date, technician, and terminal.
- Click on a visit to see full details including photos.

---

### 5.4 Support Tickets

**Path:** Field Operations > Support Tickets

The ticketing system handles issue reporting and resolution tracking.

#### Creating a Ticket
1. Click **Create Ticket**.
2. Select the type: POS Terminal issue or Internal issue.
3. For terminal issues, select the terminal.
4. Set the priority (Low, Normal, High, Emergency).
5. Choose the issue type/category.
6. Describe the issue in detail.
7. Click **Submit**.

#### Ticket Statuses
- **Open** - Newly created, awaiting action
- **In Progress** - Being worked on
- **Pending** - Waiting for external input
- **Resolved** - Issue fixed
- **Closed** - Ticket closed

#### Staged Resolution System

For complex issues, the system supports a multi-step resolution process:

1. **Add Resolution Steps** - Break down the fix into numbered steps, each with an estimated number of days.
2. **Work Through Steps** - Mark each step as complete as work progresses.
3. **Transfer Steps** - If a step requires a different technician, transfer it to them.
4. **Resolve** - Once all steps are complete, resolve the ticket.
5. **Audit Trail** - View the complete history of all changes and actions taken.

#### Assigning Tickets
- Open a ticket and click **Assign**.
- Select the technician responsible.

#### Filtering and Search
- Filter tickets by status, priority, issue type, or assigned technician.
- Search across ticket descriptions and details.

---

## 6. Project Management

### 6.1 Viewing Projects

**Path:** Project Management > Projects

The project list shows all projects with:
- Project name and code
- Client
- Status (Active, Completed, On Hold, Cancelled)
- Type (Maintenance, Installation, Support, etc.)
- Progress percentage
- Terminal count

Use filters to narrow by client, status, or type. Use the search bar to find projects by name or code.

### 6.2 Creating a New Project

**Path:** Project Management > New Project

The project creation page guides you through setup:

#### Step 1: Basic Information
1. **Project Name** - Include period, type, and location for clarity (e.g., "Q1 2026 Terminal Maintenance - Harare").
2. **Client** - Select from active clients. The system shows client terminal counts and information.
3. **Project Type** - Choose: Maintenance & Repairs, Installation & Setup, or Support & Troubleshooting.

#### Step 2: Timeline
1. **Start Date** - Pre-filled with today's date, adjust as needed.
2. **Duration (Days)** - Enter the number of days for the project.
3. **End Date** - Automatically calculated from start date and duration.

#### Step 3: Optional Fields
Expand the optional section to set:
- **Budget** - Project budget amount
- **Project Manager** - Assign a project manager
- **Priority** - Normal, High, Low, or Emergency
- **Description** - Project objectives and scope
- **Notes** - Special requirements

#### Step 4: Upload Terminals
1. Download the **CSV template** using the link provided.
2. Fill in terminal IDs (and optionally merchant details).
3. Upload the file using **Choose File**.
4. Click **Preview** to review.
5. The preview modal shows:
   - **Ready to Assign** - Terminals found in the system
   - **Already Assigned** - Terminals already in this project (skipped)
   - **Not Found** - Terminals not in the system (can be created if data provided)
6. Check/uncheck terminals as needed.
7. If "Create missing terminals" is checked, terminals with full data will be created.
8. Click **Confirm & Assign Terminals**.
9. Click **Create Project & Continue**.

### 6.3 Editing a Project

**Path:** Click **Edit** on a project's detail page

The edit page allows you to:
- Update all project fields (name, type, dates, budget, etc.)
- Upload additional terminals using the same CSV upload process
- View currently assigned terminals via the **View List** button

### 6.4 Project Detail Page

Clicking on a project shows:
- Project information summary
- Terminal assignment status and count
- Progress tracking (visits completed vs. total terminals)
- Links to deployment, reports, and closure

### 6.5 Project Closure

**Path:** Project Management > Project Closure and Reports

When a project is complete:

1. Navigate to the project and click **Closure Wizard**.
2. The wizard walks through:
   - Confirm terminal completion status
   - Review project metrics
   - Generate closure report
3. The system auto-generates a report based on terminal visit data.
4. Customize the report if needed.
5. Generate the **PDF report**.
6. Optionally **email the report** to stakeholders.

#### Closure Reports
- View all generated closure reports from the **Closure Reports** list.
- Regenerate reports if data has changed.
- Download or email reports at any time.

---

## 7. Client Management

### 7.1 Managing Clients

**Path:** Client Management > Clients

#### Viewing Clients
- Browse the client list with company name, contact info, region, and status.
- Filter by region or status.
- Search by company name.

#### Adding a Client
1. Click **Add New Client**.
2. Enter: company name, contact person, email, phone, address, region, and status.
3. Click **Save**.

#### Editing a Client
1. Click on a client to view details.
2. Click **Edit** to update information.
3. Save changes.

### 7.2 Client Dashboards

**Path:** Client Management > Client Dashboards

Each client has a dedicated dashboard showing:
- Terminal inventory for this client
- Active projects
- Service history
- Analytics and metrics

From the client dashboard, you can:
- **Add terminals** directly to the client
- **Create projects** for the client
- **Generate reports** specific to this client
- **Export data** for the client

---

## 8. Employee Management

### 8.1 Managing Employees

**Path:** Employee Management > Employees

#### Viewing Employees
- Browse the employee list with name, email, department, role, and status.
- Filter by department, role, or status.
- Search by name or email.

#### Adding an Employee
1. Click **Add New Employee**.
2. Enter: first name, last name, email, phone, employee number, department.
3. Assign a role (determines system permissions).
4. Set status to Active.
5. Click **Save**.

#### Editing an Employee
1. Click on an employee to view their profile.
2. Click **Edit** to update information.
3. You can change their role, department, or status.

#### Deactivating an Employee
- Open the employee's profile.
- Toggle their status to **Inactive**.
- Inactive employees cannot log in.

### 8.2 Role Management

**Path:** Employee Management > Role Management

Roles define what each user can access in the system.

#### Viewing Roles
- See all roles with their permission counts.

#### Creating a Role
1. Click **Add New Role**.
2. Enter a role name and description.
3. Check the permissions this role should have (see Section 12).
4. Click **Save**.

#### Cloning a Role
- Click **Clone** on an existing role to create a copy.
- Modify the name and permissions as needed.

#### Editing Permissions
- Click on a role to view its permissions.
- Check or uncheck individual permissions.
- Click **Update Permissions**.

---

## 9. Technician Portal

### 9.1 Technician Dashboard

**Path:** Technician Portal > Employee Dashboard

Technicians see a simplified view focused on their work:
- **Pending Jobs** - Assignments waiting to be started
- **In-Progress Jobs** - Current work
- **Recently Completed** - Finished assignments
- **Territory Map** - Assigned regions/areas

### 9.2 My Job Assignments

**Path:** Technician Portal > My Job Assignment

View all jobs assigned to you:
- See terminal details, client, and due dates
- Update job status as you work
- View work order instructions
- Submit visit reports when work is done

---

## 10. Reports & Analytics

### 10.1 Reports Dashboard

**Path:** Reports & Analytics > Reports Dashboard

The reports dashboard provides system-wide analytics:
- Terminal status breakdown (active, inactive, in-service)
- Service metrics and completion rates
- System health indicators
- CSV export for all displayed data

### 10.2 Technician Visit Reports

**Path:** Reports & Analytics > Technician Visits

View detailed technician visit reports:
- Filter by technician, date range, terminal, or client.
- View visit details including photos taken in the field.
- Generate **PDF reports** for individual visits.
- Export visit data to CSV.

### 10.3 Report Builder

**Path:** Reports & Analytics > Report Builder

Create custom reports tailored to your needs:

1. **Select Data Source** - Choose what to report on (clients, projects, terminals, regions).
2. **Set Filters** - Narrow data by client, project, date range, region, or terminal status.
3. **Choose Columns** - Select which fields to include.
4. **Run Report** - Preview the results.
5. **Export** - Download as CSV.

#### Saving Report Templates
- After configuring a report, click **Save Template**.
- Give it a name and optional tags.
- Saved templates appear in your template list for quick reuse.
- You can **duplicate** templates to create variations.

---

## 11. Administration

### 11.1 System Settings

**Path:** Administration > System Settings

#### Category Management
Manage system-wide categories used across the application:
- Asset statuses
- Ticket priorities
- Ticket issue types
- Terminal statuses
- Any other configurable categories

To add a category:
1. Select the category type.
2. Click **Add New**.
3. Enter the name and optional description.
4. Save.

Categories can be reordered by drag and drop.

#### Department Management
Manage organizational departments:
- Add, edit, or delete departments.
- Departments are used when creating employees.

#### Asset Category Field Management
Define custom fields for asset categories:
1. Select an asset category.
2. Click **Add Field**.
3. Configure: field name, field type (text, number, dropdown, etc.), and whether it's required.
4. Reorder fields to control display order.
5. These fields appear when creating or editing assets of that category.

---

## 12. User Roles & Permissions

The system uses role-based access control. Each employee is assigned a role, and each role has specific permissions.

### Common Permissions

| Permission | Description |
|---|---|
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
| `all` | Full system access (Super Admin) |

### Typical Role Examples

**Super Admin** - Full access to all system features.

**Project Manager** - Manage projects, view clients and terminals, assign jobs, view reports.

**Technician** - View own jobs, submit visit reports, view terminals, create tickets.

**Office Admin** - Manage clients, employees, assets, approve requests.

**Viewer** - Read-only access to dashboards and reports.

---

## Appendix A: CSV Template Formats

### Terminal Import Template
```
terminal_id,merchant_name,merchant_phone,physical_address,city,region,status
77202134,ABC Store,+27123456789,123 Main St,Johannesburg,Gauteng,active
```

### Project Terminal Upload Template (Simple)
```
terminal_id
77202134
77202135
77202136
```

### Project Terminal Upload Template (Full - for creating new terminals)
```
terminal_id,merchant_name,merchant_phone,physical_address,city,region,status
77202134,ABC Store,+27123456789,123 Main St,Johannesburg,Gauteng,active
```

---

## Appendix B: Keyboard Shortcuts & Tips

- **Quick Search** - Use the search bar on any list page to find records quickly.
- **Filters persist** - Applied filters remain active until cleared.
- **Bulk operations** - On list pages, select multiple items with checkboxes for batch actions.
- **Download templates** - Always use the system-provided CSV templates to avoid import errors.
- **Browser back** - The system supports standard browser back navigation.

---

## Appendix C: Troubleshooting

| Issue | Solution |
|---|---|
| Cannot log in | Check email/password. Contact admin if account is deactivated. |
| Page shows "403 Forbidden" | Your role does not have permission for this page. Contact admin. |
| CSV import fails | Ensure your file matches the template format. Check for special characters. |
| Terminal not found during upload | Verify the terminal ID exists in the system and belongs to the correct client. |
| Report shows no data | Check your filter criteria. Try broadening the date range or removing filters. |
| Slow page loading | Large datasets may take longer. Try narrowing filters or exporting data. |

---

*Revival Technologies - MIAV Dashboard System*
*For support, contact your system administrator.*
