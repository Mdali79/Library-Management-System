# 🔐 Login Credentials

## Default Login Accounts

### 👨‍💼 Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Role**: Admin
- **Access**: Full system access (all features)

### 👨‍🎓 Student Account
- **Username**: `student`
- **Password**: `student123`
- **Role**: Student
- **Access**: Student dashboard, book browsing, reservations

### 📝 Other Accounts
If you need to create more accounts, you can:

1. **Register New Account**: Visit `http://localhost:8000/register`
2. **Create via Tinker**:
   ```bash
   php artisan tinker
   ```
   Then:
   ```php
   App\Models\User::create([
       'name' => 'Your Name',
       'username' => 'your_username',
       'email' => 'your@email.com',
       'role' => 'Student', // or 'Teacher', 'Librarian', 'Admin'
       'department' => 'Computer Science',
       'password' => bcrypt('your_password'),
       'is_verified' => true,
   ]);
   ```

## Login URL

**Local Development**: `http://localhost:8000`

## Password Reset

If you need to reset a password, use tinker:

```bash
php artisan tinker
```

Then:
```php
$user = App\Models\User::where('username', 'admin')->first();
$user->password = bcrypt('new_password');
$user->save();
```

## Roles Available

1. **Admin** - Full system access
2. **Librarian** - Book management, issue/return, fines
3. **Teacher** - Can borrow more books (limit: 10)
4. **Student** - Standard borrowing (limit: 5)

## Notes

- All accounts are verified by default
- All users belong to Computer Science department
- Default password for factory-created users: `password`

