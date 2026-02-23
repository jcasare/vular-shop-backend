# Larvue Shop - Backend API

The backend API for the Larvue Shop e-commerce application, built with Laravel 12 and Laravel Sanctum for token-based authentication.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2+)
- **Authentication:** Laravel Sanctum (token-based)
- **Database:** MySQL
- **Queue/Cache/Sessions:** Database driver

## Features

- Token-based API authentication with admin role enforcement
- Product management with soft deletes and audit trails
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

## API Endpoints

### Public

| Method | Endpoint     | Description |
| ------ | ------------ | ----------- |
| POST   | `/api/login` | Admin login |

### Protected (Sanctum + Admin)

| Method | Endpoint      | Description            |
| ------ | ------------- | ---------------------- |
| GET    | `/api/user`   | Get authenticated user |
| POST   | `/api/logout` | Logout (revoke token)  |

## Database Schema

| Table                | Description                            |
| -------------------- | -------------------------------------- |
| `users`              | User accounts with role and admin flag |
| `products`           | Product catalog (soft deletes, audit)  |
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
