# Server Registration Configuration Guide

## Problem: Registration Bypassing OTP and Approval on Server

If registration is working locally but not on the server (bypassing OTP verification and showing "Registration successful" instead), check the following:

## Critical Configuration Checklist

### 1. Email Configuration (`.env` file)

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="eLibrary"
```

**Important:**
- Verify SMTP credentials are correct
- Test email sending: `php artisan tinker` then `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`
- Check `storage/logs/laravel.log` for email errors

### 2. Queue Configuration

```env
QUEUE_CONNECTION=sync
```

**Critical:** OTP emails must be sent synchronously. If `QUEUE_CONNECTION` is set to `database` or `redis`, emails will be queued and won't send unless a queue worker is running.

**Options:**
- Use `QUEUE_CONNECTION=sync` (recommended for OTP emails)
- OR run queue worker: `php artisan queue:work` (if using database/redis queue)

### 3. Session Configuration

```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false  # Set to true if using HTTPS
SESSION_SAME_SITE=lax
```

**Critical Checks:**
- Ensure `storage/framework/sessions` directory exists and is writable (permissions: 775 or 755)
- Verify session files are being created during registration
- Check session domain matches your server domain
- If using HTTPS, set `SESSION_SECURE_COOKIE=true`

### 4. Environment Settings

```env
APP_ENV=production
APP_DEBUG=false
```

**Note:** Even in production, temporarily enable `APP_DEBUG=true` to see detailed error messages during troubleshooting.

## Common Issues and Solutions

### Issue 1: Email Not Sending

**Symptoms:**
- Registration completes but no OTP email received
- No error message shown to user

**Diagnosis:**
1. Check `storage/logs/laravel.log` for email errors
2. Verify SMTP credentials in `.env`
3. Test email sending manually (see above)
4. Check if queue worker is running (if using queue)

**Solution:**
- Fix SMTP credentials
- Set `QUEUE_CONNECTION=sync` if no queue worker
- Check firewall/security settings blocking SMTP port 587/465

### Issue 2: Session Not Persisting

**Symptoms:**
- Registration redirects to OTP page but shows "session expired"
- User sees "Registration successful" instead of OTP verification

**Diagnosis:**
1. Check `storage/framework/sessions` directory permissions
2. Verify session driver in `.env`
3. Check session domain/cookie settings
4. Review `storage/logs/laravel.log` for session errors

**Solution:**
- Fix directory permissions: `chmod -R 775 storage/framework/sessions`
- Ensure session driver matches your setup
- Check cookie domain matches server domain
- Verify session lifetime is sufficient (120 minutes default)

### Issue 3: Registration Bypassing OTP

**Symptoms:**
- User sees "Registration successful! You can now login" immediately
- No OTP verification step shown

**Root Causes:**
1. Email sending fails silently
2. Session not persisting between requests
3. Exception not being caught properly

**Solution:**
- Check email configuration (see Issue 1)
- Check session configuration (see Issue 2)
- Review logs for errors
- Ensure exception handling is working

## Diagnostic Tools

### Access Diagnostic Route

As an Admin user, visit:
```
https://your-domain.com/diagnostics/registration
```

This will show:
- Mail configuration status
- Queue configuration
- Session configuration
- Storage permissions
- Current session ID

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Failed to send OTP email` - Email sending errors
- `Session data lost` - Session persistence issues
- `OTP verification page accessed without session data` - Session problems

## Testing Steps

1. **Test Email Sending:**
   ```bash
   php artisan tinker
   Mail::raw('Test email', function($m) { 
       $m->to('your-email@example.com')->subject('Test'); 
   });
   ```

2. **Test Session Storage:**
   - Register a new user
   - Check if `storage/framework/sessions` contains new session files
   - Verify file permissions are correct

3. **Test Registration Flow:**
   - Register with a test email
   - Check if redirected to OTP verification page
   - Verify OTP email is received
   - Complete OTP verification
   - Verify user is created with `registration_status = 'pending'`

4. **Test Login Blocking:**
   - Try to login with pending user
   - Should see "pending approval" message
   - Should NOT be able to login

## Server-Specific Considerations

### Shared Hosting
- May have restrictions on SMTP ports
- Session storage may need different driver (database)
- File permissions may be restricted

### VPS/Dedicated Server
- Full control over configuration
- Can use file-based sessions
- Can configure SMTP freely

### Cloud Platforms (AWS, DigitalOcean, etc.)
- May need to configure security groups for SMTP
- May need to use SES or other mail services
- Session storage may need Redis/database

## Migration to Fix Existing Data

If users were created without proper `registration_status`, run:

```bash
php artisan migrate
```

This will:
- Set `registration_status = 'pending'` for NULL values
- Set `is_verified = false` for pending users

## Security Notes

1. **Never expose diagnostic route in production** - Remove after fixing issues
2. **Use HTTPS** - Set `SESSION_SECURE_COOKIE=true` when using HTTPS
3. **Protect SMTP credentials** - Never commit `.env` file to version control
4. **Monitor logs** - Regularly check for registration errors

## Support

If issues persist after checking all above:
1. Check `storage/logs/laravel.log` for detailed errors
2. Verify all environment variables are set correctly
3. Test email sending manually
4. Test session storage manually
5. Compare server configuration with working local setup
