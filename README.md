##  Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/AbdeleahmanEl-Desoky/ecomerce.git
cd ecomerce
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install


### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

### Step 4: Configure Environment Variables

Edit the `.env` file with your configuration:

```env
# Application
APP_NAME="Laravel E-Commerce API"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT Configuration
JWT_SECRET=your_jwt_secret
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Cache Configuration (Redis recommended)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ecommerce.local"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🗄️ Database Setup

### Step 1: Create Database

```bash
# Create MySQL database
mysql -u root -p
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 2: Run Migrations

```bash
# Run database migrations
php artisan migrate
```

### Step 3: Seed Database with Test Data

```bash
# Seed database with realistic test data
php artisan db:seed

# Or run fresh migration with seeding
php artisan migrate:fresh --seed
```

### 📊 Seeded Data Overview

After seeding, you'll have:

- **~21 Users**: 6 admins + 15 customers
- **~69 Categories**: 3-level nested structure
- **~71 Products**: Realistic products with proper categorization

## 🏃‍♂️ Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve

# Application will be available at:
# http://localhost:8000
```

### Queue Worker (Optional)

```bash
# Start queue worker for background jobs
php artisan queue:work
```

### Cache Optimization (Production)

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## 🔑 Test Credentials

After seeding, use these credentials for testing:

### Admin Users
| Email | Password | Role |
|-------|----------|------|
| admin@ecommerce.com | admin123 | admin |
| john.admin@ecommerce.com | password123 | admin |
| sarah.manager@ecommerce.com | password123 | admin |

### Customer Users
| Email | Password | Role |
|-------|----------|------|
| alice@example.com | customer123 | customer |
| bob@example.com | customer123 | customer |
| carol@example.com | customer123 | customer |

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication Endpoints

#### Admin Authentication
```bash
# Admin Login
POST /api/v1/admin/auth/login
Content-Type: application/json

{
  "email": "admin@ecommerce.com",
  "password": "admin123"
}
```

#### Customer Authentication
```bash
# Customer Registration
POST /api/v1/customer/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}

# Customer Login
POST /api/v1/customer/auth/login
Content-Type: application/json

{
  "email": "alice@example.com",
  "password": "customer123"
}
```

### Product Endpoints

```bash
# List Products (Public)
GET /api/v1/products?page=1&per_page=10

# Get Product Details (Public)
GET /api/v1/products/{id}

# Create Product (Admin Only)
POST /api/v1/admin/products
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "iPhone 15 Pro",
  "description": "Latest iPhone with advanced features",
  "price": 999.99,
  "stock_quantity": 50,
  "sku": "IPHONE-15-PRO",
  "status": "active",
  "category_id": "category-uuid"
}
```

### Order Endpoints

```bash
# Create Order (Customer)
POST /api/v1/customer/orders
Authorization: Bearer {customer_token}
Content-Type: application/json

{
  "items": [
    {
      "product_id": "product-uuid",
      "quantity": 2,
      "price_at_time": 99.99
    }
  ],
  "notes": "Please deliver after 5 PM"
}

# List Customer Orders
GET /api/v1/customer/orders
Authorization: Bearer {customer_token}

# Cancel Order (Within 24 hours)
PATCH /api/v1/customer/orders/{id}/cancel
Authorization: Bearer {customer_token}
```

### Category Endpoints

```bash
# List Categories (Public)
GET /api/v1/categories

# Create Category (Admin Only)
POST /api/v1/admin/categories
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Electronics",
  "slug": "electronics",
  "parent_id": null
}
```

### Rate Limiting

API endpoints are protected with rate limiting:

- **General API**: 60 requests/minute
- **Authentication**: 5 requests/5 minutes
- **Orders**: 10 requests/minute
- **Products**: 100 requests/minute
- **Admin**: 200 requests/minute

## 🏗️ Architecture

### Modular Structure

```
modules/
├── Category/          # Category management
├── Order/            # Order processing
├── Product/          # Product management
├── RateLimit/        # API rate limiting
└── User/             # User authentication
```

### Service Layer Pattern

- **Controllers**: Handle HTTP requests/responses
- **Services**: Business logic and orchestration
- **Repositories**: Data access layer
- **Models**: Data representation
- **DTOs**: Data transfer objects
- **Events/Jobs**: Asynchronous processing

### Key Design Patterns

- **Repository Pattern**: Data access abstraction
- **Service Layer**: Business logic separation
- **DTO Pattern**: Clean data transfer
- **Event-Driven**: Decoupled components
- **SOLID Principles**: Maintainable code

## 🔧 Troubleshooting

### Common Issues

#### 1. JWT Secret Not Set
```bash
php artisan jwt:secret
```

#### 2. Database Connection Error
- Check database credentials in `.env`
- Ensure database server is running
- Verify database exists

#### 3. Permission Errors
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. Composer Dependencies
```bash
# Clear composer cache
composer clear-cache
composer install
```

#### 5. Application Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
