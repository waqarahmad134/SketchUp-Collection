# SketchUp Collection Backend API

Laravel backend API for the SketchUp Collection project with Filament admin panel.

## Features

- RESTful API for products/bundles
- Laravel Sanctum authentication
- Filament admin panel for content management
- Product and bundle management
- Image upload support

## Requirements

- PHP >= 8.2
- Composer
- SQLite (default) or MySQL/PostgreSQL

## Installation

1. Install dependencies:
```bash
composer install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Run migrations:
```bash
php artisan migrate
```

5. Create admin user:
```bash
php artisan make:filament-user
```

## Running the Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`
Admin panel will be available at `http://localhost:8000/admin`

## API Endpoints

### Public Endpoints

- `GET /api/v1/products` - List all products
- `GET /api/v1/products/{id}` - Get product details
- `POST /api/v1/register` - Register new user
- `POST /api/v1/login` - Login user

### Protected Endpoints (Requires Authentication)

- `GET /api/v1/user` - Get authenticated user
- `POST /api/v1/logout` - Logout user

## API Usage Examples

### Register User
```bash
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```bash
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Get Products
```bash
GET /api/v1/products?category=interior&is_bundle=true&search=chair
```

### Get Product by ID or Slug
```bash
GET /api/v1/products/interior-skp-bundle
```

## Admin Panel

Access the admin panel at `/admin` and manage:
- Products and bundles
- Categories
- Images
- Features and tags

## CORS Configuration

The API is configured to accept requests from the Next.js frontend. Update CORS settings in `config/cors.php` if needed.

## Database

By default, the project uses SQLite. To use MySQL or PostgreSQL:

1. Update `.env` file with your database credentials
2. Run migrations: `php artisan migrate`

## File Storage

Product images are stored in `storage/app/public/products`. Make sure to create a symbolic link:

```bash
php artisan storage:link
```

## License

Proprietary - All rights reserved


============
Underdevelop
============

Light box for images // Done but need ui improvement
Swiper added in product detail page // Done need little improvement
Remove Filter from bundles // Done with extra api call remove

Coupons // Working
Breadcrumbs
Images With name and title while saving not the random name 
Logo watermark auto handle using admin
Images alt in DB
Images masonary in bundle case with respect to categories
Self Service of interior , exterior and others

===============
Admin Panel New
===============