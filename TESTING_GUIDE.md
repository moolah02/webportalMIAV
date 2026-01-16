# Revival Technologies System Testing Guide

## Overview
This comprehensive testing guide covers all major features of the Revival Technologies Management System. Follow these test cases to verify system functionality.

---

## Prerequisites
- **Production URL:** http://51.21.252.67
- **Test Credentials:** Use your assigned employee credentials
- **Browser:** Chrome, Firefox, or Edge (latest version)

---

## 1. Authentication & Login Tests

### Test 1.1: User Login
**Steps:**
1. Navigate to http://51.21.252.67/login
2. Enter valid email and password
3. Click "Login"

**Expected Result:**
- ✅ Successfully logged in
- ✅ Redirected to dashboard
- ✅ User name displayed in header

**Status:** [ ] Pass [ ] Fail

---

### Test 1.2: Invalid Login
**Steps:**
1. Navigate to login page
2. Enter incorrect password
3. Click "Login"

**Expected Result:**
- ✅ Error message displayed
- ✅ Not logged in

**Status:** [ ] Pass [ ] Fail

---

## 2. Employee Management Tests

### Test 2.1: View Employees List
**Steps:**
1. Login as Admin or Manager
2. Navigate to "Employee Management" → "Employees"
3. Verify employee list displays

**Expected Result:**
- ✅ List of employees shown
- ✅ Employee details visible (Name, Email, Department, Status, Roles)
- ✅ **Multiple role badges displayed** for employees with multiple roles
- ✅ Color-coded role badges (Admin=Green, Supervisor=Orange, Technician=Blue)

**Status:** [ ] Pass [ ] Fail

---

### Test 2.2: Create New Employee (Single Role)
**Steps:**
1. Click "+ Onboard New Employee"
2. Fill in required fields:
   - First Name: Test
   - Last Name: Employee
   - Email: test.employee@revival-technologies.com
   - Password: Test123!
   - Primary Role: Select "Technician"
   - Department: Select any
   - Hire Date: Today's date
   - Status: Active
3. Do NOT check any "Additional Roles"
4. Click "Create Employee"

**Expected Result:**
- ✅ Success message displayed
- ✅ Employee created with unique employee number
- ✅ Redirected to employees list
- ✅ New employee visible in list with ONE role badge

**Status:** [ ] Pass [ ] Fail

---

### Test 2.3: Create New Employee (Multiple Roles)
**Steps:**
1. Click "+ Onboard New Employee"
2. Fill in required fields:
   - First Name: Multi
   - Last Name: Role
   - Email: multi.role@revival-technologies.com
   - Password: Multi123!
   - Primary Role: Select "Technician"
   - Department: Select any
   - Hire Date: Today's date
   - Status: Active
3. **Check "Supervisor" in Additional Roles section**
4. Click "Create Employee"

**Expected Result:**
- ✅ Success message displayed
- ✅ Employee created successfully
- ✅ New employee visible with **TWO role badges** (Technician, Supervisor)
- ✅ Both badges display with different colors

**Status:** [ ] Pass [ ] Fail

---

### Test 2.4: Edit Employee - Add Additional Roles
**Steps:**
1. Find an employee with only one role
2. Click edit icon (pencil)
3. Keep existing Primary Role
4. **Check one or more boxes in "Additional Roles" section**
5. Click "Update Employee"

**Expected Result:**
- ✅ Success message displayed
- ✅ Employee now shows **multiple role badges** in the list
- ✅ All selected roles are displayed

**Status:** [ ] Pass [ ] Fail

---

### Test 2.5: Edit Employee - Remove Additional Roles
**Steps:**
1. Find an employee with multiple roles
2. Click edit icon
3. **Uncheck all "Additional Roles" boxes**
4. Keep Primary Role selected
5. Click "Update Employee"

**Expected Result:**
- ✅ Success message displayed
- ✅ Employee now shows only **one role badge** (Primary Role)

**Status:** [ ] Pass [ ] Fail

---

### Test 2.6: Search and Filter Employees
**Steps:**
1. Use search box to search for employee name
2. Filter by department
3. Filter by status
4. Filter by role

**Expected Result:**
- ✅ Search returns matching results
- ✅ Filters work correctly
- ✅ Can combine multiple filters

**Status:** [ ] Pass [ ] Fail

---

## 3. Report Builder Access Tests

### Test 3.1: Access Report Builder (All Users)
**Steps:**
1. Login with **any employee account** (Admin, Supervisor, Technician, etc.)
2. Navigate to "Reports & Analytics" → "Report Builder"
3. Or directly access: http://51.21.252.67/reports/builder

**Expected Result:**
- ✅ Report Builder page loads successfully
- ✅ **NO** "403 Forbidden" or "You do not have permission" errors
- ✅ Report builder interface is visible
- ✅ Available to ALL authenticated users regardless of role

**Status:** [ ] Pass [ ] Fail

---

### Test 3.2: Create and Run Custom Report
**Steps:**
1. Access Report Builder
2. Select data source (Clients, Projects, Terminals, etc.)
3. Select fields to include
4. Add filters (optional)
5. Click "🔄 Run Report"

**Expected Result:**
- ✅ Report executes successfully
- ✅ Results displayed in table
- ✅ Data is accurate and formatted correctly

**Status:** [ ] Pass [ ] Fail

---

### Test 3.3: Export Report to CSV
**Steps:**
1. Run a report (from Test 3.2)
2. Click "📊 Export" dropdown
3. Select "CSV Format"

**Expected Result:**
- ✅ CSV file downloads
- ✅ File contains report data
- ✅ Available to ALL users (no permission error)

**Status:** [ ] Pass [ ] Fail

---

### Test 3.4: Load Report Template
**Steps:**
1. Access Report Builder
2. Click "📂 Load Template"
3. Select a saved template
4. Click "Load"

**Expected Result:**
- ✅ Template loads successfully
- ✅ Report configuration populated
- ✅ Can run loaded template

**Status:** [ ] Pass [ ] Fail

---

## 4. Asset Management Tests

### Test 4.1: View Assets
**Steps:**
1. Navigate to "Assets Management" → "Asset Inventory"
2. Browse asset list

**Expected Result:**
- ✅ Assets displayed with details
- ✅ Can see asset status, location, assignment
- ✅ Pagination works

**Status:** [ ] Pass [ ] Fail

---

### Test 4.2: Create New Asset
**Steps:**
1. Click "Add New Asset"
2. Fill in asset details
3. Submit form

**Expected Result:**
- ✅ Asset created successfully
- ✅ Unique asset tag generated
- ✅ Asset visible in inventory

**Status:** [ ] Pass [ ] Fail

---

### Test 4.3: Assign Asset to Employee
**Steps:**
1. Find an available asset
2. Click "Assign" button
3. Select employee
4. Set quantity and assignment details
5. Submit

**Expected Result:**
- ✅ Asset assigned successfully
- ✅ Asset status updated to "Assigned"
- ✅ Employee can see assigned asset in their profile

**Status:** [ ] Pass [ ] Fail

---

## 5. Business License Management Tests

### Test 5.1: View Business Licenses
**Steps:**
1. Navigate to "Business Licenses"
2. View license list

**Expected Result:**
- ✅ Licenses displayed
- ✅ Can see status, expiry dates, clients
- ✅ Expiring licenses highlighted

**Status:** [ ] Pass [ ] Fail

---

### Test 5.2: Add New Business License
**Steps:**
1. Click "Add New License"
2. Fill in license details
3. Upload supporting document
4. Submit

**Expected Result:**
- ✅ License created successfully
- ✅ Document uploaded and linked
- ✅ License visible in list

**Status:** [ ] Pass [ ] Fail

---

## 6. Job Assignment Tests (For Technicians)

### Test 6.1: View Job Assignments
**Steps:**
1. Login as Technician
2. Navigate to "Field Operations" → "Job Assignments"

**Expected Result:**
- ✅ Assigned jobs displayed
- ✅ Can see job details, client, location
- ✅ Status clearly visible

**Status:** [ ] Pass [ ] Fail

---

### Test 6.2: Update Job Status
**Steps:**
1. Open a job assignment
2. Change status (In Progress, Completed, etc.)
3. Add notes
4. Save

**Expected Result:**
- ✅ Status updated successfully
- ✅ Notes saved
- ✅ Update logged with timestamp

**Status:** [ ] Pass [ ] Fail

---

## 7. Client Dashboard Tests

### Test 7.1: View Client List
**Steps:**
1. Navigate to "Client Management" → "Clients"
2. Browse client list

**Expected Result:**
- ✅ Clients displayed with key information
- ✅ Can search and filter clients
- ✅ Client status visible

**Status:** [ ] Pass [ ] Fail

---

### Test 7.2: View Client Dashboard
**Steps:**
1. Click on a client
2. View client dashboard

**Expected Result:**
- ✅ Dashboard loads with client metrics
- ✅ Shows terminals, projects, tickets
- ✅ Charts and statistics display correctly

**Status:** [ ] Pass [ ] Fail

---

## 8. Project Management Tests

### Test 8.1: Create New Project
**Steps:**
1. Navigate to "Projects"
2. Click "Create New Project"
3. Fill in project details
4. Assign project manager
5. Submit

**Expected Result:**
- ✅ Project created successfully
- ✅ Project visible in list
- ✅ Manager can access project

**Status:** [ ] Pass [ ] Fail

---

### Test 8.2: Update Project Status
**Steps:**
1. Open a project
2. Update project status
3. Add progress notes
4. Save changes

**Expected Result:**
- ✅ Status updated
- ✅ Progress tracked
- ✅ History logged

**Status:** [ ] Pass [ ] Fail

---

## 9. System Settings Tests

### Test 9.1: Update User Profile
**Steps:**
1. Click user menu → "Profile"
2. Update personal information
3. Save changes

**Expected Result:**
- ✅ Profile updated successfully
- ✅ Changes reflected immediately
- ✅ Updated info displayed in header

**Status:** [ ] Pass [ ] Fail

---

### Test 9.2: Change Password
**Steps:**
1. Go to Profile
2. Click "Change Password"
3. Enter current and new password
4. Submit

**Expected Result:**
- ✅ Password changed successfully
- ✅ Can login with new password
- ✅ Old password no longer works

**Status:** [ ] Pass [ ] Fail

---

## 10. Permissions & Role Tests

### Test 10.1: Admin Access
**Steps:**
1. Login as Admin
2. Attempt to access all modules

**Expected Result:**
- ✅ Full access to all features
- ✅ Can manage users, roles, settings
- ✅ No permission denied errors

**Status:** [ ] Pass [ ] Fail

---

### Test 10.2: Technician Access
**Steps:**
1. Login as Technician
2. Try accessing various modules

**Expected Result:**
- ✅ Can access: Jobs, Reports, Profile
- ✅ Can access: Report Builder
- ✅ Limited access to admin features
- ✅ Appropriate error messages for restricted areas

**Status:** [ ] Pass [ ] Fail

---

### Test 10.3: Supervisor Access
**Steps:**
1. Login as Supervisor
2. Try accessing various modules

**Expected Result:**
- ✅ Can access: Team management, Jobs, Reports
- ✅ Can access: Report Builder
- ✅ Can view subordinate data
- ✅ Cannot access admin-only features

**Status:** [ ] Pass [ ] Fail

---

## 11. Performance & UI Tests

### Test 11.1: Page Load Speed
**Steps:**
1. Navigate between different modules
2. Note load times

**Expected Result:**
- ✅ Pages load within 2-3 seconds
- ✅ No excessive delays
- ✅ Smooth transitions

**Status:** [ ] Pass [ ] Fail

---

### Test 11.2: Mobile Responsiveness
**Steps:**
1. Access system on mobile device or resize browser
2. Test key features

**Expected Result:**
- ✅ Layout adapts to screen size
- ✅ Navigation menu accessible
- ✅ Forms are usable on mobile

**Status:** [ ] Pass [ ] Fail

---

### Test 11.3: Browser Compatibility
**Steps:**
1. Test system on Chrome, Firefox, Edge
2. Verify key features work

**Expected Result:**
- ✅ Consistent behavior across browsers
- ✅ No display issues
- ✅ All features functional

**Status:** [ ] Pass [ ] Fail

---

## 12. Data Integrity Tests

### Test 12.1: Database Consistency
**Steps:**
1. Create an employee with multiple roles
2. View employee in different areas of system
3. Edit employee roles
4. Verify roles update everywhere

**Expected Result:**
- ✅ Role changes reflect immediately
- ✅ Data consistent across all views
- ✅ No orphaned or duplicate data

**Status:** [ ] Pass [ ] Fail

---

### Test 12.2: Audit Trail
**Steps:**
1. Make changes to employee, asset, or project
2. Check if changes are logged

**Expected Result:**
- ✅ Changes logged with timestamp
- ✅ User who made change recorded
- ✅ Before/after values captured (if applicable)

**Status:** [ ] Pass [ ] Fail

---

## 13. Error Handling Tests

### Test 13.1: Required Field Validation
**Steps:**
1. Try to create employee without required fields
2. Submit form

**Expected Result:**
- ✅ Validation errors displayed
- ✅ Form not submitted
- ✅ Clear error messages shown

**Status:** [ ] Pass [ ] Fail

---

### Test 13.2: Invalid Data Handling
**Steps:**
1. Enter invalid email format
2. Enter negative numbers where not allowed
3. Submit

**Expected Result:**
- ✅ Validation catches invalid data
- ✅ Helpful error messages displayed
- ✅ Form preserves valid entries

**Status:** [ ] Pass [ ] Fail

---

### Test 13.3: Session Timeout
**Steps:**
1. Login and remain inactive for extended period
2. Try to perform an action

**Expected Result:**
- ✅ Session expires after timeout
- ✅ Redirected to login
- ✅ Can login again successfully

**Status:** [ ] Pass [ ] Fail

---

## 14. Export & Reporting Tests

### Test 14.1: CSV Export
**Steps:**
1. Navigate to any data list (employees, assets, etc.)
2. Click "Export" or "Download CSV"

**Expected Result:**
- ✅ CSV file downloads
- ✅ Contains all visible data
- ✅ Proper formatting

**Status:** [ ] Pass [ ] Fail

---

### Test 14.2: Report Generation
**Steps:**
1. Generate different types of reports
2. Verify data accuracy

**Expected Result:**
- ✅ Reports generate successfully
- ✅ Data matches source data
- ✅ Filters work correctly

**Status:** [ ] Pass [ ] Fail

---

## 15. Security Tests

### Test 15.1: Unauthorized Access Prevention
**Steps:**
1. Logout
2. Try to access protected URL directly

**Expected Result:**
- ✅ Redirected to login page
- ✅ Cannot access protected resources
- ✅ Session properly cleared

**Status:** [ ] Pass [ ] Fail

---

### Test 15.2: SQL Injection Prevention
**Steps:**
1. Try entering SQL code in search fields
   - Example: `' OR '1'='1`
2. Submit

**Expected Result:**
- ✅ Input sanitized
- ✅ No database error
- ✅ No unauthorized data returned

**Status:** [ ] Pass [ ] Fail

---

### Test 15.3: XSS Prevention
**Steps:**
1. Try entering JavaScript in text fields
   - Example: `<script>alert('test')</script>`
2. Submit and view

**Expected Result:**
- ✅ Script not executed
- ✅ Displayed as text or stripped
- ✅ No security alert

**Status:** [ ] Pass [ ] Fail

---

## Test Summary

**Total Tests:** 48
**Passed:** _____
**Failed:** _____
**Pass Rate:** _____%

---

## Critical Issues Found

| Test # | Issue Description | Severity | Screenshots |
|--------|-------------------|----------|-------------|
|        |                   |          |             |
|        |                   |          |             |
|        |                   |          |             |

---

## Notes & Observations

```
[Add any additional observations, suggestions, or comments here]







```

---

## Sign-Off

**Tester Name:** _____________________
**Date:** _____________________
**Signature:** _____________________

**Client Approval:** _____________________
**Date:** _____________________

---

## Contact Information

**Support Email:** support@revival-technologies.com
**System Admin:** admin@miav.com
**Developer Contact:** [Your contact information]

---

*Document Version: 1.0*
*Last Updated: January 15, 2026*
