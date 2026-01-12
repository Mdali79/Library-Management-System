# Debugging Registration Issues on Server

## Problem

-   **Locally**: OTP email sent → User verifies OTP → `registration_status = 'pending'` ✅
-   **On Server**: No OTP email sent → User created with `is_verified = true` and wrong status ❌

## Debugging Steps

### 1. Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Look for these log entries:

#### Registration Started

```
[INFO] Registration request received
```

-   Should show email, username, role, IP, session_id

#### OTP Email Attempt

```
[INFO] Attempting to send OTP email
```

-   Should appear after registration form submission

#### OTP Email Success/Failure

```
[INFO] OTP email sent successfully
```

OR

```
[ERROR] CRITICAL: Failed to send OTP email - Registration stopped
```

-   If you see ERROR, check the error message and stack trace

#### Session Issues

```
[ERROR] CRITICAL: Session data lost after email send
```

-   Indicates session storage problem

#### User Creation

```
[INFO] User being created
```

-   Shows where user is being created from (should be `RegisterController::verifyOtp`)
-   Check `created_from` field

```
[INFO] User created successfully
```

-   Shows final user status (should have `registration_status = 'pending'`)

### 2. Check Email Configuration

Visit diagnostic route (as Admin):

```
https://your-domain.com/diagnostics/registration
```

Check:

-   `mail_host` - Should be your SMTP server
-   `mail_username_set` - Should be `true`
-   `mail_password_set` - Should be `true`
-   `queue_connection` - Should be `sync` (unless queue worker is running)

### 3. Test Email Sending Manually

```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerificationMail;

Mail::to('your-email@example.com')->send(new OtpVerificationMail('123456', 'Test User'));
```

If this fails, check:

-   SMTP credentials in `.env`
-   Firewall blocking port 587/465
-   SMTP server accessibility

### 4. Check Session Storage

```bash
ls -la storage/framework/sessions/
```

Should see:

-   Directory exists
-   Permissions: `drwxrwxr-x` (775) or `drwxr-xr-x` (755)
-   Files being created during registration

If not:

```bash
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions  # Adjust user:group as needed
```

### 5. Verify Registration Flow

The correct flow should be:

1. **POST /register** → `RegisterController::register()`

    - Validates input
    - Stores data in session
    - Sends OTP email
    - Redirects to `/verify-otp`

2. **GET /verify-otp** → `RegisterController::showOtpVerification()`

    - Checks session has registration data
    - Shows OTP input form

3. **POST /verify-otp** → `RegisterController::verifyOtp()`
    - Validates OTP
    - Creates user with `registration_status = 'pending'`
    - Redirects to login

### 6. Check for Bypass

If users are being created without OTP verification, check logs for:

```
[WARNING] User created without registration_status
[WARNING] User created with is_verified=true but invalid registration_status
```

These indicate users are being created outside the normal flow.

### 7. Common Issues

#### Issue: Email Fails Silently

**Symptoms:**

-   No OTP email sent
-   Registration continues anyway

**Check:**

-   Look for `[ERROR] CRITICAL: Failed to send OTP email` in logs
-   If not present, exception might not be caught
-   Verify `try-catch` block in `RegisterController::register()`

#### Issue: Session Not Persisting

**Symptoms:**

-   Redirects to OTP page but shows "session expired"
-   User created anyway (somehow)

**Check:**

-   Session driver in `.env`
-   Session storage permissions
-   Session domain/cookie settings
-   Look for `[ERROR] CRITICAL: Session data lost` in logs

#### Issue: Users Created with Wrong Status

**Symptoms:**

-   Users have `is_verified = true` but `registration_status = 'approved'` or NULL
-   Users can login without approval

**Check:**

-   Look for `[INFO] User being created` in logs
-   Check `created_from` field - should be `RegisterController::verifyOtp`
-   If different, user is being created elsewhere

### 8. Quick Fixes

#### Force All Users to Pending

```sql
UPDATE users
SET registration_status = 'pending', is_verified = 0
WHERE registration_status IS NULL
   OR (registration_status != 'approved' AND is_verified = 1);
```

#### Clear All Sessions

```bash
rm -rf storage/framework/sessions/*
```

#### Test Registration Flow

1. Clear browser cookies
2. Register new user
3. Watch logs in real-time: `tail -f storage/logs/laravel.log`
4. Check each step in the flow

## Expected Log Sequence

When registration works correctly, you should see:

```
[INFO] Registration request received
[INFO] Attempting to send OTP email
[INFO] OTP email sent successfully
[INFO] Redirecting to OTP verification page
[INFO] OTP verified successfully - Creating user account
[INFO] User being created (created_from: RegisterController::verifyOtp)
[INFO] User created successfully (registration_status: pending, is_verified: 1)
```

## If Still Not Working

1. **Compare .env files** - Local vs Server
2. **Check PHP version** - Should match local
3. **Check Laravel version** - Should match local
4. **Review server error logs** - Apache/Nginx logs
5. **Check database** - Verify migrations ran correctly
6. **Test in isolation** - Create minimal test to isolate issue

## Contact Points

If issue persists, provide:

1. Relevant log entries from `laravel.log`
2. Output from `/diagnostics/registration`
3. Server environment details (PHP version, Laravel version)
4. `.env` configuration (mask sensitive data)
