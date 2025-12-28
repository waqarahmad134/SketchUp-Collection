# How to Access the Admin Panel

## Quick Steps

### 1. Create Admin User

Open terminal in the `backend` directory and run:

```bash
php artisan make:filament-user
```

You'll be prompted to enter:
- **Name**: Your name (e.g., "Admin")
- **Email**: Your email (e.g., "admin@example.com")  
- **Password**: Choose a secure password

### 2. Start Laravel Server

```bash
php artisan serve
```

The server will start at: `http://localhost:8000`

### 3. Access Admin Panel

Open your browser and go to:

**Admin Panel URL:** `http://localhost:8000/admin`

You'll see the login page. Use the email and password you just created.

### 4. Alternative: Create User via Tinker

If you prefer, you can also create a user using Laravel Tinker:

```bash
php artisan tinker
```

Then in tinker, run:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = bcrypt('your-password');
$user->save();
```

Exit tinker with `exit` and then login at `http://localhost:8000/admin`

## What You'll See

Once logged in, you'll see:
- **Dashboard** - Overview of your admin panel
- **Products** - Manage all products and bundles
  - List all products
  - Create new product/bundle
  - Edit existing products
  - Upload images
  - Set pricing, categories, features

## Admin Panel Features

### Products Management
- ✅ Create/Edit/Delete products
- ✅ Upload product images
- ✅ Set prices and discounts
- ✅ Mark products as bundles
- ✅ Add features and tags
- ✅ Set categories
- ✅ Toggle active/inactive status

### Quick Links
- Dashboard: `http://localhost:8000/admin`
- Products List: `http://localhost:8000/admin/products`
- Create Product: `http://localhost:8000/admin/products/create`

## Troubleshooting

### Can't Access Admin Panel?

1. **Server not running?**
   ```bash
   php artisan serve
   ```

2. **User doesn't exist?**
   ```bash
   php artisan make:filament-user
   ```

3. **Login page shows but can't login?**
   - Check that you're using the correct email
   - Try resetting password or creating a new user

4. **500 Error?**
   - Clear cache: `php artisan cache:clear`
   - Clear config: `php artisan config:clear`
   - Check logs: `tail -f storage/logs/laravel.log`

## Default URLs

- **Admin Login:** http://localhost:8000/admin/login
- **Admin Dashboard:** http://localhost:8000/admin
- **Products:** http://localhost:8000/admin/products

## Security Note

⚠️ **Important:** Change the default admin password after first login for security!

