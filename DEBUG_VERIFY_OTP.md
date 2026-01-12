# Debug verifyOtp Not Being Called on Server

## Problem

-   **Locally**: `verifyOtp()` method is called when OTP form is submitted ✅
-   **On Server**: `verifyOtp()` method is NOT being called ❌

## Possible Causes

### 1. Route Caching

Server might have cached routes that don't include the verifyOtp route.

**Fix:**

```bash
php artisan route:clear
php artisan route:cache  # Only if needed
php artisan config:clear
php artisan cache:clear
```

### 2. CSRF Token Issues

CSRF token might be invalid or missing on server.

**Check:**

-   Look for `419` errors in server logs
-   Check if `@csrf` is present in the form
-   Verify CSRF token is being sent

**Fix:**

-   Clear browser cookies
-   Check `APP_KEY` in `.env` matches between local and server
-   Verify session domain settings

### 3. Session Not Persisting

Session might not be persisting between GET `/verify-otp` and POST `/verify-otp`.

**Check logs for:**

```
[INFO] === showOtpVerification METHOD CALLED ===
[INFO] === verifyOtp METHOD CALLED ===
```

If you see the first but not the second, session is likely the issue.

**Fix:**

-   Check `storage/framework/sessions` permissions
-   Verify `SESSION_DRIVER` in `.env`
-   Check session domain/cookie settings
-   Ensure session files are being created

### 4. Form Submission Failing

Form might not be submitting due to JavaScript errors or validation.

**Check:**

-   Browser console for JavaScript errors
-   Network tab to see if POST request is being sent
-   Check if form is actually submitting

### 5. Route Not Matching

Route might not be matching due to URL rewriting or case sensitivity.

**Check:**

-   Verify route exists: `php artisan route:list | grep verify-otp`
-   Check if URL matches exactly: `/verify-otp` (not `/verify-otp/` or `/Verify-Otp`)

### 6. Middleware Blocking

Middleware might be blocking the request.

**Check:**

-   Look for middleware errors in logs
-   Verify `VerifyCsrfToken` middleware is not blocking
-   Check if any custom middleware is interfering

## Debugging Steps

### Step 1: Check if Route Exists

```bash
php artisan route:list | grep verify
```

Should show:

```
GET|HEAD  verify-otp ................ verify.otp › RegisterController@showOtpVerification
POST      verify-otp ................ verify.otp › RegisterController@verifyOtp
```

### Step 2: Clear All Caches

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 3: Check Logs

```bash
tail -f storage/logs/laravel.log
```

Look for:

-   `[INFO] === showOtpVerification METHOD CALLED ===` - Should appear when page loads
-   `[INFO] === verifyOtp METHOD CALLED ===` - Should appear when form is submitted

### Step 4: Test Form Submission

1. Open browser developer tools (F12)
2. Go to Network tab
3. Submit OTP form
4. Check if POST request to `/verify-otp` is being sent
5. Check response status code:
    - `200` = Success (method was called)
    - `419` = CSRF token error
    - `404` = Route not found
    - `500` = Server error

### Step 5: Check Session

```bash
ls -la storage/framework/sessions/
```

Should see session files being created. Check:

-   Files exist
-   Permissions are correct (775 or 755)
-   Files are being updated

### Step 6: Test Route Directly

```bash
curl -X POST https://your-domain.com/verify-otp \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "otp=123456&_token=YOUR_CSRF_TOKEN"
```

Replace `YOUR_CSRF_TOKEN` with actual token from the form.

## Expected Log Sequence

When working correctly, you should see:

```
[INFO] Registration request received
[INFO] Attempting to send OTP email
[INFO] OTP email sent successfully
[INFO] Redirecting to OTP verification page
[INFO] === showOtpVerification METHOD CALLED ===
[INFO] === verifyOtp METHOD CALLED ===  <-- This is missing on server!
[INFO] OTP verified successfully - Creating user account
[INFO] User being created
[INFO] User created successfully
```

## Quick Fixes

### Fix 1: Clear Route Cache

```bash
php artisan route:clear
```

### Fix 2: Fix Session Permissions

```bash
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
```

### Fix 3: Verify APP_KEY

Ensure `.env` has:

```
APP_KEY=base64:... (should match local)
```

### Fix 4: Check Web Server Configuration

If using Apache, ensure `.htaccess` is working:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

If using Nginx, ensure proper rewrite rules.

## If Still Not Working

1. **Compare route files**: Ensure `routes/web.php` is identical on server
2. **Check middleware**: Verify no middleware is blocking the route
3. **Test with tinker**:
    ```bash
    php artisan tinker
    Route::getRoutes()->match(Request::create('/verify-otp', 'POST'));
    ```
4. **Check server error logs**: Look in Apache/Nginx error logs for clues
5. **Enable debug mode**: Temporarily set `APP_DEBUG=true` to see detailed errors

## Common Server-Specific Issues

### Shared Hosting

-   May require `.htaccess` modifications
-   Session storage might need to be database instead of file
-   URL rewriting might be different

### VPS/Dedicated Server

-   Check file permissions
-   Verify web server user has access to storage
-   Check SELinux/AppArmor if enabled

### Cloud Platforms

-   Check load balancer configuration
-   Verify session affinity/sticky sessions
-   Check if multiple servers need shared session storage
