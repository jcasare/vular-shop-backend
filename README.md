# Larvue Shop - Backend API

The backend API for the Larvue Shop e-commerce application, built with Laravel 12 and Laravel Sanctum for cookie/session-based SPA authentication.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2+)
- **Authentication:** Laravel Sanctum (cookie/session-based SPA mode)
- **Database:** MySQL
- **Queue/Cache/Sessions:** Database driver

## Features

- Cookie/session-based SPA authentication with CSRF protection
- Unified auth endpoints for all user roles (admin, customer)
- Role-based access control via middleware (`isAdmin`, `isCustomer`)
- Google OAuth login for customers
- Product management with quantity tracking, image uploads, soft deletes, and audit trails
- Shopping cart system
- Order management with items, details, and payments
- Customer management with addresses
- Country/state reference data

## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL

## Setup

```bash
git clone <https://github.com/jcasare/vular-shop-backend.git>
cd larvue-shop
```

Update `.env` with your database credentials, then run:

```bash
composer setup
```

This will install dependencies, generate the app key, run migrations, and build assets.

Create the storage symlink for serving uploaded images:

```bash
php artisan storage:link
```

To seed the database with default admin users:

```bash
php artisan db:seed
```

## Development

Start all dev services (server, queue, logs, Vite) concurrently:

```bash
composer dev
```

## Testing

```bash
composer test
```

## Authentication Flow

This app uses Sanctum's **cookie/session-based SPA authentication**, not token-based auth. The flow works as follows:

1. Frontend calls `GET /sanctum/csrf-cookie` to receive an `XSRF-TOKEN` cookie
2. Frontend sends login request — Laravel starts a session and sends back an `httpOnly` session cookie
3. All subsequent requests include the session cookie automatically (via `withCredentials: true` on axios)
4. The session cookie is `httpOnly`, meaning JavaScript cannot access it — protecting against XSS token theft

### Domain Architecture (Local Dev)

All services run on subdomains of `.larvue.localhost` so cookies can be shared:

| Service | URL | Purpose |
| --- | --- | --- |
| API | `http://api.larvue.localhost:8000` | Laravel backend |
| Shop | `http://shop.larvue.localhost:5173` | Customer frontend |
| Admin | `http://manage.larvue.localhost:5173` | Admin frontend |

**Why subdomains?** Session and CSRF cookies are set with `domain=.larvue.localhost`, making them accessible to all `*.larvue.localhost` subdomains. This is required for cross-origin cookie-based auth to work.

### Key `.env` Settings

```env
APP_URL=http://api.larvue.localhost:8000
SESSION_DOMAIN=.larvue.localhost
```

- `SESSION_DOMAIN=.larvue.localhost` — cookies are shared across all `*.larvue.localhost` subdomains
- `APP_URL` — must match the API subdomain so Laravel generates correct URLs

### CORS & Sanctum Configuration

- **`config/cors.php`** — frontend origins explicitly whitelisted with `supports_credentials: true`. A pattern (`/^http:\/\/[\w-]+\.larvue\.localhost(:\d+)?$/`) covers all `.larvue.localhost` subdomains.
- **`config/sanctum.php`** — all frontend and API subdomains listed as stateful domains so Sanctum applies session/CSRF middleware to requests from these origins.

## API Endpoints

### Public Auth

| Method | Endpoint                       | Description              |
| ------ | ------------------------------ | ------------------------ |
| POST   | `/api/auth/login`              | Login (all roles)        |
| POST   | `/api/auth/register`           | Register (customer only) |
| GET    | `/api/auth/google/redirect`    | Google OAuth redirect    |
| GET    | `/api/auth/google/callback`    | Google OAuth callback    |

### Protected Auth (any authenticated user)

| Method | Endpoint             | Description              |
| ------ | -------------------- | ------------------------ |
| GET    | `/api/auth/user`     | Get authenticated user   |
| POST   | `/api/auth/logout`   | Logout (destroy session) |

### Protected Admin (`auth:sanctum` + `isAdmin`)

| Method    | Endpoint                                        | Description          |
| --------- | ----------------------------------------------- | -------------------- |
| GET       | `/api/admin/products`                           | List products        |
| POST      | `/api/admin/products`                           | Create product       |
| GET       | `/api/admin/products/{id}`                      | Get product          |
| PUT/PATCH | `/api/admin/products/{id}`                      | Update product       |
| DELETE    | `/api/admin/products/{id}`                      | Delete product       |
| GET       | `/api/admin/categories`                         | List categories      |
| POST      | `/api/admin/categories`                         | Create category      |
| GET       | `/api/admin/categories/{id}`                    | Get category         |
| PUT/PATCH | `/api/admin/categories/{id}`                    | Update category      |
| DELETE    | `/api/admin/categories/{id}`                    | Delete category      |
| GET       | `/api/admin/products/{product}/discounts`       | List discounts       |
| POST      | `/api/admin/products/{product}/discounts`       | Create discount      |
| PUT       | `/api/admin/products/{product}/discounts/{id}`  | Update discount      |
| DELETE    | `/api/admin/products/{product}/discounts/{id}`  | Delete discount      |

### Public Shop

| Method | Endpoint                         | Description                     |
| ------ | -------------------------------- | ------------------------------- |
| GET    | `/api/shop/products`             | List products (search, filter)  |
| GET    | `/api/shop/products/featured`    | Featured products               |
| GET    | `/api/shop/products/{idOrSlug}`  | Get product by ID or slug       |
| GET    | `/api/shop/categories`           | List categories with counts     |

### Protected Shop (`auth:sanctum`)

| Method | Endpoint                        | Description                  |
| ------ | ------------------------------- | ---------------------------- |
| GET    | `/api/shop/cart`                | Get cart items               |
| POST   | `/api/shop/cart`                | Add item to cart             |
| POST   | `/api/shop/cart/sync`           | Sync guest cart after login  |
| PATCH  | `/api/shop/cart/{productId}`    | Update cart item quantity    |
| DELETE | `/api/shop/cart/{productId}`    | Remove cart item             |
| DELETE | `/api/shop/cart`                | Clear entire cart            |

## Database Schema

| Table                | Description                            |
| -------------------- | -------------------------------------- |
| `users`              | User accounts with role and admin flag |
| `categories`         | Product categories with slugs          |
| `products`           | Product catalog (soft deletes, audit)  |
| `product_discounts`  | Time-based sale prices per product     |
| `cart_items`         | Shopping cart items per user           |
| `orders`             | Customer orders with status and total  |
| `order_items`        | Line items within an order             |
| `order_details`      | Shipping/billing info for orders       |
| `payments`           | Payment records per order              |
| `customers`          | Customer profiles                      |
| `customer_addresses` | Customer address book                  |
| `countries`          | Country/state reference data (JSONB)   |

## Default Admin Users (Seeder)

| Email                    | Password   | Role        |
| ------------------------ | ---------- | ----------- |
| `superadmin@example.com` | `password` | Super Admin |
| `manager@example.com`    | `password` | Admin       |

## License

MIT
