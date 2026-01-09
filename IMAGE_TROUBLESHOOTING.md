# Image Display Troubleshooting Guide

## Common Issues and Solutions

### Issue: Images showing locally but not on server

## Solution Steps:

### 1. **Create Storage Symlink (MOST COMMON ISSUE)**
Run this command on your server:
```bash
php artisan storage:link
```
This creates a symbolic link from `public/storage` to `storage/app/public`, allowing web access to uploaded files.

**Verify the symlink exists:**
```bash
ls -la public/storage
```
Should show: `public/storage -> ../storage/app/public`

### 2. **Check APP_URL in .env File**
Make sure your `.env` file on the server has the correct `APP_URL`:

**For production server:**
```env
APP_URL=http://your-domain.com
# or
APP_URL=https://your-domain.com
```

**For server with IP:**
```env
APP_URL=http://206.189.84.50:8083
```

**After changing APP_URL, clear config cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. **Check File Permissions**
Ensure storage directories have correct permissions:

```bash
# Set permissions for storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (replace www-data with your web server user)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### 4. **Verify Files Exist**
Check if files are actually uploaded:

```bash
# Check book covers
ls -la storage/app/public/book_covers/

# Check profile pictures
ls -la storage/app/public/profile_pictures/
```

### 5. **Check Image Paths in Database**
Verify the `cover_image` column in the `books` table contains correct paths:
- Should be like: `book_covers/filename.jpg`
- Should NOT include `public/` prefix

### 6. **Test Image URL Generation**
You can test by adding this temporarily to a view:
```php
{{ Storage::disk('public')->url('book_covers/test.jpg') }}
```

### 7. **Check Web Server Configuration**
For Apache, ensure `.htaccess` allows following symlinks:
```apache
Options +FollowSymLinks
```

For Nginx, ensure the location block allows access:
```nginx
location /storage {
    alias /path/to/your/project/storage/app/public;
    try_files $uri $uri/ =404;
}
```

### 8. **Alternative: Use Direct Asset Path**
If Storage::url() doesn't work, you can modify the accessor to use asset():

In `app/Models/book.php`, change:
```php
public function getCoverImageUrlAttribute()
{
    if (!$this->cover_image) {
        return asset('images/default-book-cover.png');
    }
    
    // Use asset() instead of Storage::url()
    return asset('storage/' . $this->cover_image);
}
```

## Quick Fix Commands (Run on Server)

```bash
# 1. Create storage symlink
php artisan storage:link

# 2. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 4. Verify symlink
ls -la public/storage
```

## Debugging Steps

1. **Check if symlink exists:**
   ```bash
   ls -la public/ | grep storage
   ```

2. **Check APP_URL:**
   ```bash
   php artisan tinker
   >>> config('app.url')
   ```

3. **Test image URL:**
   ```bash
   php artisan tinker
   >>> Storage::disk('public')->url('book_covers/test.jpg')
   ```

4. **Check file existence:**
   ```bash
   php artisan tinker
   >>> Storage::disk('public')->exists('book_covers/test.jpg')
   ```

## Most Likely Solution

**99% of the time, the issue is:**
1. Storage symlink not created → Run `php artisan storage:link`
2. APP_URL incorrect in .env → Update and clear cache

