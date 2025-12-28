# Backend Setup Complete! 🎉

## What's Been Created

### 1. Laravel Backend API
- ✅ Laravel 12 with API routes
- ✅ Laravel Sanctum for authentication
- ✅ Product/Bundle management system
- ✅ RESTful API endpoints

### 2. Filament Admin Panel
- ✅ Admin panel at `/admin`
- ✅ Product resource with full CRUD
- ✅ Image upload support
- ✅ Product and bundle management

### 3. Database
- ✅ Products table migration created and run
- ✅ SQLite database configured (default)

## Next Steps

### 1. Create Admin User
```bash
cd backend
php artisan make:filament-user
```

Follow the prompts to create your admin account.

### 2. Start the Server
```bash
php artisan serve
```

- API: `http://localhost:8000/api/v1`
- Admin Panel: `http://localhost:8000/admin`

### 3. Configure CORS (if needed)
Update `config/cors.php` to allow requests from your Next.js frontend (usually `http://localhost:3000`).

### 4. Link Storage for Images
```bash
php artisan storage:link
```

This creates a symbolic link so uploaded images are accessible.

## API Endpoints

### Public Endpoints
- `GET /api/v1/products` - List all products (supports query params: `category`, `is_bundle`, `search`)
- `GET /api/v1/products/{id}` - Get product by ID or slug
- `POST /api/v1/register` - Register new user
- `POST /api/v1/login` - Login user

### Protected Endpoints (Requires Bearer Token)
- `GET /api/v1/user` - Get authenticated user
- `POST /api/v1/logout` - Logout user

## Example API Usage

### Register
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Products
```bash
curl http://localhost:8000/api/v1/products?category=interior&is_bundle=true
```

## Admin Panel Features

1. **Product Management**
   - Create, edit, delete products
   - Upload images
   - Set pricing
   - Mark as bundle
   - Add features and tags

2. **Categories**
   - Interior Design
   - Architecture
   - Landscape Design
   - Furniture
   - Textures

3. **Bundles**
   - Mark products as bundles
   - Link included products
   - Set bundle pricing

## Connecting Next.js Frontend

In your Next.js app, update API calls to point to:
```typescript
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';
```

Example fetch:
```typescript
const response = await fetch(`${API_URL}/products`);
const products = await response.json();
```

## Environment Variables

Make sure your `.env` file includes:
```
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000
```

## Troubleshooting

### Migration Errors
If you get migration errors, try:
```bash
php artisan migrate:fresh
```

### Admin Panel Not Loading
1. Clear cache: `php artisan cache:clear`
2. Clear config: `php artisan config:clear`
3. Check that you've created an admin user

### CORS Issues
Update `config/cors.php` to include your frontend URL in the `allowed_origins` array.

## File Structure

```
backend/
├── app/
│   ├── Filament/Resources/ProductResource.php  # Admin panel resource
│   ├── Http/Controllers/Api/                  # API controllers
│   └── Models/Product.php                      # Product model
├── database/migrations/                        # Database migrations
├── routes/api.php                             # API routes
└── README.md                                  # Full documentation
```

## Support

For issues or questions, check:
- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs
- Laravel Sanctum: https://laravel.com/docs/sanctum

