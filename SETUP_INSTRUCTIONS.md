# Setup Instructions

## Quick Start

### 1. Install Dependencies (if not already done)
```bash
composer install
npm install
```

### 2. Environment Setup
Make sure your `.env` file is configured with:
- Database connection details
- Mail settings (for notifications)
- App URL

### 3. Run Migrations
```bash
php artisan migrate
```

This will create/update all necessary database tables:
- Users table (with roles and new fields)
- Students table (with new fields)
- Books table (with new fields)
- Book issues table (with new fields)
- Fines table (new)
- Book reservations table (new)
- Settings table (updated)

### 4. Run Seeders
```bash
php artisan db:seed
```

This will:
- Create default categories (Literature, Science, Mathematics, Engineering, Computer Sciences, Business)
- Create default settings (return days, fine rates, borrowing limits)

### 5. Create Storage Link
```bash
php artisan storage:link
```

This creates a symbolic link for storing book cover images.

### 6. Set Permissions (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

### 7. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Testing the System

### Create an Admin User
You can create an admin user manually in the database or through tinker:
```bash
php artisan tinker
```

Then:
```php
$user = App\Models\User::create([
    'name' => 'Admin',
    'username' => 'admin',
    'email' => 'admin@example.com',
    'role' => 'Admin',
    'password' => bcrypt('password'),
    'is_verified' => true,
]);
```

### Access the System
1. Go to `/register` to create a new account
2. Select a role (Student, Teacher, Librarian, or Admin)
3. Fill in all required fields
4. Login at `/` with your username and password

## Important Notes

### Blade Views
The backend is fully functional, but you'll need to update/create Blade views to match the new features. See `IMPLEMENTATION_SUMMARY.md` for a list of views that need to be created/updated.

### Email/SMS Notifications
The notification system is set up but requires:
1. Mail configuration in `.env`
2. SMS service integration (optional, can use email only)
3. Mail classes creation (DueDateReminder, OverdueAlert, BookAvailableNotification)

### PDF/Excel Export
To enable PDF/Excel export, install:
```bash
composer require barryvdh/laravel-dompdf
# or
composer require maatwebsite/excel
```

## Features Available

✅ User Registration with Roles
✅ Role-Based Dashboards
✅ Advanced Book Search
✅ Book Cover Image Upload
✅ Book Quantity Management
✅ Book Issue with Borrowing Limits
✅ Book Return with Fine Calculation
✅ Fine Management System
✅ Book Reservation System
✅ Comprehensive Reporting
✅ Notification Framework

## Troubleshooting

### Migration Errors
If you get migration errors, you may need to:
1. Drop existing tables (if in development)
2. Run `php artisan migrate:fresh --seed`

### Storage Link Issues
If images don't display:
1. Check if `public/storage` link exists
2. Run `php artisan storage:link` again
3. Check file permissions

### Permission Errors
Make sure storage and cache directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
```

