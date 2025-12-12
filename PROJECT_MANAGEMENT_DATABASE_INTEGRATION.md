# Project Management Database Integration - Documentation

## Overview

The Project Management module has been fully integrated with a MySQL database, enabling real-time data persistence, comprehensive activity logging, and complete CRUD (Create, Read, Update, Delete) operations for projects, milestones, and tasks.

## Architecture

### Database Schema

The system uses 6 interconnected tables:

1. **clients** - Client information
2. **projects** - Project master records
3. **milestones** - Project phases/milestones
4. **tasks** - Individual work items
5. **activity_log** - Comprehensive audit trail
6. **attachments** - File attachments (optional)

### API Endpoints

#### Project Management
- **GET** `/api/admin/projects_api.php` - List all projects
- **GET** `/api/admin/projects_api.php?id={projectId}` - Get single project
- **POST** `/api/admin/projects_api.php` - Create project
- **PUT** `/api/admin/projects_api.php` - Update project
- **DELETE** `/api/admin/projects_api.php` - Delete project

#### Milestones
- **GET** `/api/admin/tasks_api.php?action=milestones&project_id={id}` - List milestones
- **POST** `/api/admin/tasks_api.php?action=milestones` - Create milestone
- **PUT** `/api/admin/tasks_api.php?action=milestones` - Update milestone
- **DELETE** `/api/admin/tasks_api.php?action=milestones` - Delete milestone

#### Tasks
- **GET** `/api/admin/tasks_api.php?action=tasks&project_id={id}` - List tasks
- **POST** `/api/admin/tasks_api.php?action=tasks` - Create task
- **PUT** `/api/admin/tasks_api.php?action=tasks` - Update task
- **DELETE** `/api/admin/tasks_api.php?action=tasks` - Delete task

#### Activity Log
- **GET** `/api/admin/activity_log_api.php?project_id={id}` - Retrieve activity logs
- **POST** `/api/admin/activity_log_api.php` - Create manual log entry
- **DELETE** `/api/admin/activity_log_api.php` - Clean old logs

## Setup Instructions

### 1. Database Initialization

Run the setup script to create all required tables:

```bash
curl http://localhost/mostrecent.softwarecreativelabs.com/api/setup_project_db.php
```

Or visit in browser:
```
http://localhost/mostrecent.softwarecreativelabs.com/api/setup_project_db.php
```

Expected Response:
```json
{
  "success": true,
  "message": "Database setup completed successfully!",
  "tables_created": ["clients", "projects", "milestones", "tasks", "activity_log", "attachments"],
  "tables_existed": [],
  "errors": []
}
```

### 2. Verify Installation

Test the Project Management module:

1. Navigate to: `http://localhost/mostrecent.softwarecreativelabs.com/AdminPortal/billing-admin.html`
2. Login as admin
3. Click "Project Management" in the sidebar
4. Verify the database-integrated interface loads

### 3. Create Initial Data

#### Create a Client (if needed)

```bash
curl -X POST http://localhost/mostrecent.softwarecreativelabs.com/api/admin/clients.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Client",
    "email": "client@example.com",
    "company": "Test Company"
  }'
```

#### Create a Project

```bash
curl -X POST http://localhost/mostrecent.softwarecreativelabs.com/api/admin/projects_api.php \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "title": "Test Project",
    "description": "A test project",
    "amount": 5000,
    "status": "pending",
    "start_date": "2025-01-01",
    "deadline": "2025-02-01"
  }'
```

## Features

### 1. Real-Time Data Persistence

All user actions are immediately saved to the database:
- Creating/updating/deleting projects
- Changing project status
- Updating progress percentages
- Adding notes and descriptions

### 2. Comprehensive Error Handling

The system includes multiple layers of error handling:

#### Client-Side Validation
- Required field validation
- Date range validation (deadline after start date)
- Amount validation (non-negative)
- Progress range validation (0-100%)

#### Server-Side Validation
- Input sanitization
- Type checking and conversion
- Foreign key validation
- Database constraint enforcement

#### User Notifications
- Toast notifications for all operations
- Success confirmations
- Error messages with specific details
- Loading indicators during API calls

### 3. Activity Logging

Every action is logged with:
- Entity type (project, milestone, task)
- Action performed (created, updated, deleted)
- User information (admin_id, admin_name)
- Timestamp
- IP address
- Old and new values for updates
- Full description of changes

### 4. Input Validation

#### Project Fields
| Field | Validation | Rules |
|-------|-----------|-------|
| title | Required | Trimmed, non-empty |
| client_id | Required | Must exist in clients table |
| amount | Optional | Must be non-negative decimal |
| progress | Optional | Must be 0-100% |
| status | Optional | pending, in_progress, review, completed, cancelled, on_hold |
| start_date | Optional | Valid date format |
| deadline | Optional | Must be >= start_date |

#### Task Fields
| Field | Validation | Rules |
|-------|-----------|-------|
| title | Required | Trimmed, non-empty |
| milestone_id | Required | Must exist in milestones table |
| status | Optional | pending, in_progress, completed, overdue, on_hold, cancelled |
| priority | Optional | low, medium, high, urgent |
| progress | Optional | Must be 0-100% |
| due_date | Optional | Valid date format |

### 5. Data Integrity

- **Cascade Deletes**: Deleting a project automatically removes all associated milestones, tasks, and activity logs
- **Foreign Key Constraints**: All relationships are enforced at the database level
- **Transaction Support**: Complex operations maintain data consistency
- **Timestamp Management**: All records include created_at and updated_at timestamps

## Usage Guide

### Creating a Project

1. Click "New Project" button
2. Fill in required fields:
   - **Title** (required)
   - **Client** (required) - Enter client ID
3. Optional fields:
   - Description
   - Amount (in ZAR)
   - Start Date
   - Deadline
4. Click "Create Project"
5. Confirmation notification will appear
6. Project immediately appears in the list

### Updating a Project

1. Click "Edit" button on desired project
2. Modify any fields
3. Click "Save Changes"
4. Verification:
   - Success notification appears
   - Project list updates instantly
   - Activity log captures the change

### Deleting a Project

1. Click "Delete" button on desired project
2. Confirm deletion in the dialog
3. Project and all related data are removed
4. Activity log records the deletion

### Monitoring Activity

View the activity log in the project details view:
1. Click "View" on a project
2. Scroll to "Recent Activity" section
3. See all changes with timestamps and user info

## Database Queries

### Get All Active Projects

```sql
SELECT p.*, c.name as client_name 
FROM projects p
LEFT JOIN clients c ON p.client_id = c.id
WHERE p.status != 'completed'
ORDER BY p.deadline ASC;
```

### Get Project Timeline

```sql
SELECT m.*, COUNT(t.id) as task_count
FROM milestones m
LEFT JOIN tasks t ON m.id = t.milestone_id
WHERE m.project_id = ?
ORDER BY m.order_index ASC;
```

### Get Activity History

```sql
SELECT * FROM activity_log
WHERE project_id = ?
ORDER BY created_at DESC
LIMIT 100;
```

### Calculate Project Progress

```sql
SELECT p.id, p.title, 
       AVG(t.progress) as avg_progress,
       COUNT(CASE WHEN t.status = 'completed' THEN 1 END) as completed_tasks,
       COUNT(t.id) as total_tasks
FROM projects p
LEFT JOIN tasks t ON p.id = t.project_id
GROUP BY p.id;
```

## Error Handling Examples

### Validation Error
```json
{
  "success": false,
  "message": "Title is required"
}
```

### Not Found Error
```json
{
  "success": false,
  "message": "Project not found"
}
```

### Database Error
```json
{
  "success": false,
  "message": "Database constraint violation"
}
```

## Performance Optimization

### Pagination

List endpoints support pagination:
```
GET /api/admin/projects_api.php?page=1&limit=20
```

Response includes:
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 150,
    "total_pages": 8
  }
}
```

### Filtering

Projects can be filtered by status and client:
```
GET /api/admin/projects_api.php?status=in_progress&client_id=5
```

### Indexing

Database includes indexes for:
- client_id
- project_id
- status
- deadline
- created_at
- milestone_id
- task_id
- priority

## Troubleshooting

### Issue: Database tables not created

**Solution**: Run the setup script again
```bash
curl http://localhost/mostrecent.softwarecreativelabs.com/api/setup_project_db.php
```

### Issue: Projects not loading

**Check**:
1. Browser console for JavaScript errors
2. Network tab to see API responses
3. PHP error logs in XAMPP

```bash
tail -f C:\xampp\apache\logs\error.log
tail -f C:\xampp\php\logs\php_errors.log
```

### Issue: Changes not persisting

**Check**:
1. Database connection in `/api/db.php`
2. Write permissions on database tables
3. API response status codes

### Issue: Activity log not recording

**Check**:
1. SESSION variables are set
2. activity_log table exists
3. Foreign key constraints are satisfied

## Security Considerations

### Input Validation
- All user input is validated and sanitized
- SQL prepared statements prevent injection
- Type casting enforces data types

### Access Control
- API requires admin session (check in production)
- IP logging for audit trail
- User attribution on all actions

### Data Protection
- All sensitive fields are properly escaped
- No sensitive data in activity logs
- Proper error messages without exposing internals

## Maintenance

### Regular Tasks

#### Weekly
- Review activity logs for unusual patterns
- Check for database performance issues

#### Monthly
- Archive old activity logs (>90 days)
```bash
curl -X DELETE http://localhost/mostrecent.softwarecreativelabs.com/api/admin/activity_log_api.php \
  -H "Content-Type: application/json" \
  -d '{"days_old": 90}'
```

#### Quarterly
- Backup database
- Review project completion rates
- Analyze user activity trends

## API Response Codes

| Code | Meaning |
|------|---------|
| 200 | Success (GET, PUT, DELETE) |
| 201 | Created (POST) |
| 400 | Bad Request (validation error) |
| 405 | Method Not Allowed |
| 500 | Server Error |

## Migration from LocalStorage

If migrating from the old localStorage-based system:

1. Export old data from browser console:
```javascript
JSON.stringify(JSON.parse(localStorage.getItem('scl_projects')))
```

2. Create projects via API with that data

3. Verify all data transferred correctly

4. Clear localStorage:
```javascript
localStorage.removeItem('scl_projects');
localStorage.removeItem('scl_projects_archived');
localStorage.removeItem('scl_activity_logs');
```

## Support & Debugging

### Enable Debug Mode

In `project-manager-db.js`, change line:
```javascript
const DEBUG = true;
```

This will log all API calls to console.

### Common Issues and Solutions

**Issue**: "Network Error" notifications
- Check API endpoint URLs
- Verify PHP files exist in `/api/admin/`
- Check browser console for CORS errors

**Issue**: Slow loading
- Check database indexes
- Verify network latency
- Consider pagination

**Issue**: Data inconsistencies
- Review activity logs
- Check for concurrent updates
- Verify cascade delete behavior

## Future Enhancements

Potential improvements for future versions:

1. **Real-time Sync**: WebSocket support for live updates
2. **Collaboration**: Multiple users editing same project
3. **Notifications**: Email alerts for status changes
4. **Advanced Filtering**: Complex query builder
5. **Bulk Operations**: Import/export functionality
6. **Custom Fields**: User-defined project attributes
7. **Time Tracking**: Hours logged per task
8. **Resource Planning**: Team capacity management
9. **Gantt Charts**: Visual timeline view
10. **Integrations**: Slack, Zapier, etc.

## Files Included

### API Endpoints
- `/api/setup_project_db.php` - Database initialization
- `/api/admin/projects_api.php` - Project CRUD operations
- `/api/admin/tasks_api.php` - Task and milestone CRUD
- `/api/admin/activity_log_api.php` - Activity log retrieval

### Frontend
- `/AdminPortal/js/project-manager-db.js` - Main JavaScript class
- `/AdminPortal/billing-admin.html` - Updated admin dashboard

### Documentation
- This file: `PROJECT_MANAGEMENT_DATABASE_INTEGRATION.md`

## Version History

### Version 1.0 (December 2025)
- Initial database integration
- CRUD operations for projects, milestones, tasks
- Activity logging system
- Input validation and error handling
- Toast notifications
- Real-time data persistence

## License

This module is part of the SCL Digital Agency project management system.

## Support

For issues or questions, contact the development team.
