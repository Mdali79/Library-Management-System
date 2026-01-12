# Fix Server Registration Issue

## Problem Identified

From server logs, users are being created with:
- `registration_status = "approved"` ❌ (should be "pending")
- `created_from = "Illuminate\\Events\\Dispatcher"` ❌ (bypassing OTP flow)
- No OTP email logs before user creation ❌

This means users are being created **without going through OTP verification**.

## Root Cause

Users are being created through an **event listener** that bypasses the normal registration flow. The `Registered` event from Laravel might be firing and creating users automatically.

## Fixes Applied

### 1. Enhanced UserObserver
- **Blocks unauthorized 'approved' status**: If user is created with `registration_status = 'approved'` but NOT from `UserRegistrationController::approve`, it's forced to `'pending'`
- **Better call stack tracking**: Logs full call stack to identify where users are being created from
- **Automatic status correction**: Ensures all users have proper status

### 2. Registration Method Safeguard
- **Prevents user creation in register()**: Added check to ensure NO user is created during registration
- **Deletes incorrectly created users**: If a user is somehow created, it's deleted immediately
- **Logs security breaches**: All unauthorized user creations are logged

## What to Check on Server

### 1. Check for Old Registration Code

Search for any code that might be creating users directly:

```bash
grep -r "User::create\|User::firstOrCreate" app/
grep -r "event(new Registered" app/
grep -r "Registered::dispatch" app/
```

### 2. Check Event Listeners

Check if `Registered` event is being listened to:

```bash
grep -r "Registered::class" app/
grep -r "SendEmailVerificationNotification" app/
```

### 3. Check Routes

Verify only one registration route exists:

```bash
grep -r "register" routes/
```

Should only see:
- `GET /register` → `RegisterController::showRegistrationForm`
- `POST /register` → `RegisterController::register`

### 4. Check Database for Incorrect Users

```sql
SELECT id, email, username, is_verified, registration_status, created_at 
FROM users 
WHERE registration_status = 'approved' 
  AND approved_by IS NULL 
  AND created_at > '2026-01-01'
ORDER BY created_at DESC;
```

These users were created incorrectly and should be set to 'pending':

```sql
UPDATE users 
SET registration_status = 'pending', is_verified = 0 
WHERE registration_status = 'approved' 
  AND approved_by IS NULL 
  AND created_at > '2026-01-01';
```

## Testing After Fix

1. **Clear all sessions on server:**
   ```bash
   rm -rf storage/framework/sessions/*
   ```

2. **Test registration:**
   - Register a new user
   - Check logs: Should see "Registration request received" and "OTP email sent"
   - Should NOT see "User being created" until OTP is verified
   - User should have `registration_status = 'pending'`

3. **Check logs for security:**
   ```bash
   tail -f storage/logs/laravel.log | grep -i "SECURITY\|User being created"
   ```

   Should see:
   - `[INFO] Registration request received`
   - `[INFO] Attempting to send OTP email`
   - `[INFO] OTP email sent successfully`
   - `[INFO] Redirecting to OTP verification page`
   - (After OTP verification) `[INFO] User being created` with `created_from: RegisterController::verifyOtp`

## Expected Behavior

### Correct Flow:
1. User submits registration form
2. `RegisterController::register()` stores data in session
3. OTP email sent
4. Redirect to OTP verification page
5. User enters OTP
6. `RegisterController::verifyOtp()` creates user with `registration_status = 'pending'`
7. UserObserver ensures status is correct
8. User cannot login until admin approves

### What Should NOT Happen:
- ❌ User created during `register()` method
- ❌ User created with `registration_status = 'approved'` (unless from admin)
- ❌ User created without OTP verification
- ❌ User can login without approval

## If Issue Persists

1. **Check if UserObserver is registered:**
   ```bash
   grep -r "UserObserver" app/Providers/
   ```
   Should see it in `AppServiceProvider::boot()`

2. **Check if observer is working:**
   - Look for `[INFO] User being created` in logs
   - If not present, observer might not be registered

3. **Check for conflicting code:**
   - Look for any code that creates users outside of `RegisterController::verifyOtp()`
   - Check for event listeners that create users
   - Check for database triggers or stored procedures

4. **Compare server code with local:**
   - Ensure all files are synced
   - Check if server has old cached code
   - Clear config cache: `php artisan config:clear`
   - Clear route cache: `php artisan route:clear`

## Quick Fix Command

If users are still being created incorrectly, run this SQL to fix existing users:

```sql
-- Fix all users created without proper approval
UPDATE users 
SET registration_status = 'pending', 
    is_verified = 0 
WHERE (registration_status = 'approved' AND approved_by IS NULL)
   OR (registration_status IS NULL)
   OR (is_verified = 1 AND registration_status != 'approved' AND registration_status != 'pending');
```

Then check logs to see if new registrations are working correctly.
