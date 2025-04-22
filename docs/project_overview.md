# Herd ENT Laravel Application - Project Overview

## Project Architecture

This Laravel application follows a standard MVC architecture with the following key components:

### Backend
- **Models** (`app/Models/`): Define database structure and relationships
- **Controllers** (`app/Http/Controllers/`): Handle request/response logic
- **Routes** (`routes/web.php`): Define application endpoints
- **Middleware**: Handle authentication and permissions

### Frontend
- **Vue.js Components** (`resources/js/Pages/`): Frontend views using Inertia.js
- **CSS** (`resources/css/`): Styling with Tailwind CSS

## Key Module Overview

### 1. User Management
- Authentication using Laravel Breeze
- Role-based permissions (Administrator, Clinician, Biller, FrontDesk)
- Profile management

### 2. Patient Management
- Patient registration and record keeping
- Search and filtering capabilities
- Patient history tracking

### 3. Appointment System
- Calendar-based scheduling
- Appointment status tracking
- Reminders functionality

### 4. Encounter/EMR Module
- SOAP note interface
- ENT-specific examination fields
- File attachment capabilities

### 5. Billing System
- CPT and ICD code assignment
- Invoice generation
- Payment tracking

### 6. Reporting
- Financial reports
- Clinical statistics
- Usage metrics

## Development Guidelines

### Adding New Features
1. Create/modify relevant Model(s)
2. Implement Controller logic
3. Add routes in appropriate route file
4. Create Vue component(s) for the UI
5. Add permissions if needed

### Testing
- Run feature tests: `php artisan test`
- Run specific test: `php artisan test --filter=TestName`

### Permission System
The application uses Spatie Permission package with these main roles:
- super-admin: Full system access
- administrator: Administrative access
- clinician: Access to patient records and encounters
- biller: Access to invoicing and billing codes
- frontdesk: Access to appointments and patient registration

## Deployment
- Environment configuration through `.env`
- Database migrations: `php artisan migrate`
- Production build: `npm run build` 