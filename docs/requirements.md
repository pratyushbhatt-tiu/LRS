# Laravel Recording System (LRS) - Requirements & Acceptance Criteria

## High-Level System Objective

The Laravel Recording System (LRS) is a production-grade document recording management system designed to track and manage the lifecycle of legal documents through various recording processes. The system provides role-based access control, comprehensive audit logging, and status-driven workflows to ensure data integrity and accountability throughout the document recording process.

## Defined User Roles & Responsibilities

### Admin
- **Responsibilities**: Full system access, user management, role assignment, system configuration
- **Access Level**: All modules and all permissions
- **Key Functions**: Create/edit users, assign roles, manage master data, view audit logs

### Operations
- **Responsibilities**: Day-to-day file processing, data entry, master data viewing
- **Access Level**: File creation and processing, read access to master data
- **Key Functions**: Create new files, update file information, process files through workflow

### QC (Quality Control)
- **Responsibilities**: Quality assurance, file approval, verification
- **Access Level**: File viewing, editing, and approval
- **Key Functions**: Review files, approve/reject files, edit file details for corrections

### Accounting
- **Responsibilities**: Financial reporting, data export, read-only file access
- **Access Level**: View files and master data, generate and export reports
- **Key Functions**: View file details, generate reports, export data for accounting purposes

### Read-Only
- **Responsibilities**: View-only access for stakeholders
- **Access Level**: Read access to files and master data
- **Key Functions**: View file information, view master data

## List of Modules

1. **Authentication & Authorization**
   - Login/logout
   - Password reset
   - Role-based access control

2. **User Management** (Admin only)
   - User creation
   - Role assignment
   - User deactivation

3. **Master Data Management**
   - Clients
   - Document Types
   - Recording Purposes
   - States
   - Counties
   - Cities

4. **File Management**
   - File creation
   - File tracking
   - Status management
   - File search and filtering

5. **Workflow Management**
   - Status transitions
   - Approval workflows
   - Process tracking

6. **Audit Logging**
   - User activity tracking
   - Data change history
   - Login/logout tracking

7. **Reporting**
   - File status reports
   - Activity reports
   - Data exports

## Global Rules

### Status-Driven Workflow
- All files must have a current status at all times
- Status changes must be tracked in file_status_history
- Status transitions must be logged with user attribution
- Previous status must be recorded for audit trail
- Status changes may require specific permissions based on role

### Auditability
- All login and logout events must be logged
- All role assignments must be logged
- All data modifications must be traceable to a user
- Audit logs must be immutable (no updates or deletes)
- Audit logs must include old and new values for changes
- Timestamps must be recorded for all audit events

### Role-Based Access
- Default deny: users have no access unless explicitly granted
- Unauthorized access attempts must return HTTP 403
- Menu items must be hidden if user lacks permission
- All controller actions must verify permissions
- No wildcard permissions allowed
- Permissions must be explicit and granular

## Acceptance Criteria (Draft)

### Phase 1: Foundation & Schema

#### 1. Authentication & Roles
- [ ] Users can log in with email and password
- [ ] Users can reset their password via email
- [ ] Public registration is disabled
- [ ] Five roles exist: Admin, Operations, QC, Accounting, Read-Only
- [ ] Each role has explicit permissions (no wildcards)
- [ ] Roles can be assigned to users
- [ ] Login/logout events are logged in audit_logs table

#### 2. Master Data Integrity
- [ ] All master data tables have unique constraints on code/name
- [ ] States table has unique state codes and names
- [ ] Counties are linked to states with foreign keys
- [ ] Cities are linked to both states and counties with foreign keys
- [ ] All master data tables support soft deletes
- [ ] All master data tables have active/inactive flags
- [ ] Cascade deletes work correctly for state → county → city

#### 3. Files Schema Integrity
- [ ] Files table has unique file_no with index
- [ ] All foreign keys are enforced (client, doc_type, recording_purpose, state, county)
- [ ] Received_date is stored as date type
- [ ] Current_status is required and indexed
- [ ] Partner_ref_no is optional and indexed
- [ ] File_status_history tracks all status changes
- [ ] File_status_history records user who made the change
- [ ] File_status_history includes from_status and to_status
- [ ] File deletion cascades to status history

#### 4. Audit Logging
- [ ] Audit_logs table exists with correct schema
- [ ] User_id is tracked (nullable for system events)
- [ ] Action type is recorded
- [ ] Auditable_type and auditable_id support polymorphic relations
- [ ] Old_values and new_values are stored as JSON
- [ ] Created_at timestamp is recorded
- [ ] Audit logs have no updated_at (immutable)
- [ ] Login events are logged
- [ ] Logout events are logged
- [ ] Role assignment events are logged

#### 5. Database Seeding
- [ ] All five roles are seeded with correct permissions
- [ ] One demo user exists for each role
- [ ] Demo user credentials are documented
- [ ] Sample clients are seeded (minimum 3)
- [ ] Sample document types are seeded (minimum 4)
- [ ] Sample recording purposes are seeded (minimum 3)
- [ ] Sample states are seeded (minimum 3)
- [ ] Sample counties are seeded with state relationships
- [ ] Sample cities are seeded with state and county relationships

#### 6. Engineering Standards
- [ ] All models have $fillable arrays defined
- [ ] Mass assignment protection is enabled
- [ ] Soft deletes are implemented where required
- [ ] Relationships are defined in models
- [ ] Foreign key constraints are enforced at database level
- [ ] Indexes are created on frequently queried columns
- [ ] CSRF protection is enabled
- [ ] No business logic exists in controllers (thin controllers)
- [ ] No routes or UI exist for master data (structure only)
- [ ] No file workflow logic exists yet (structure only)

## Demo User Credentials

All demo users use the same password for initial setup:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@lrs.local | password |
| Operations | operations@lrs.local | password |
| QC | qc@lrs.local | password |
| Accounting | accounting@lrs.local | password |
| Read-Only | readonly@lrs.local | password |

> **Security Note**: These credentials are for development only. In production, users must be created with secure passwords and forced to change on first login.

## Database Schema Summary

### Core Tables
- `users` - System users with authentication
- `roles` - User roles (via Spatie Permission)
- `permissions` - Granular permissions (via Spatie Permission)
- `model_has_roles` - User-role assignments (via Spatie Permission)
- `model_has_permissions` - Direct user permissions (via Spatie Permission)
- `role_has_permissions` - Role-permission assignments (via Spatie Permission)

### Audit & Tracking
- `audit_logs` - Comprehensive audit trail
- `file_status_history` - File status change tracking

### Master Data
- `clients` - Client organizations
- `doc_types` - Document type classifications
- `recording_purposes` - Purpose of recording
- `states` - US States
- `counties` - Counties within states
- `cities` - Cities within counties

### Transactional Data
- `files` - Main file tracking table

## Future Phases (Not Implemented in Phase 1)

- File CRUD UI
- File workflow implementation
- Status transition rules
- File search and filtering
- Reporting module
- Dashboard
- Notifications
- Document upload/attachment
- Advanced audit log viewing
- User management UI

## Technical Stack

- **Framework**: Laravel 11.x
- **Authentication**: Laravel Breeze (Blade)
- **Authorization**: Spatie Laravel Permission
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL (configurable)
- **Session**: Database-driven
- **Cache**: Database-driven

## Compliance & Security

- CSRF protection enabled on all forms
- Mass assignment protection via $fillable
- SQL injection prevention via Eloquent ORM
- Foreign key constraints enforced
- Soft deletes for data retention
- Audit logging for accountability
- Role-based access control
- Default deny security model
