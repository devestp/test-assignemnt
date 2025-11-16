# WallGold - Order Book Trading Platform

This is a sophisticated order book trading platform built with Laravel, implementing Domain-Driven Design (DDD) principles. The application enables users to create and manage buy/sell orders while maintaining a live order book view. The system handles order matching, credit management, and idempotent transaction processing.

## Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture Overview](#architecture-overview)
- [How to Run](#how-to-run)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Database Design](#database-design)
- [API Design](#api-design)
- [Architecture Explanations](#architecture-explanations)
- [Code Style Guide](#code-style-guide)
- [Testing](#testing)
- [Authentication](#authentication)
- [Order Matching Logic](#order-matching-logic)
- [Order Simulation](#order-simulation)

## Project Overview

This project is designed as a platform that demonstrates advanced software engineering practices including:

- **Domain-Driven Design**: Clear separation between domain logic and infrastructure concerns
- **Aggregate Pattern**: Order and OrderBook aggregates manage complex business logic
- **Repository Pattern**: Abstracted data access with domain-oriented interfaces
- **Idempotency**: Safe handling of duplicate requests using token-based idempotency
- **Precision Arithmetic**: BigDecimal-based calculations to ensure financial accuracy
- **Type Safety**: Strong typing with PHP 8.2+ features and enums

## Features

- **Real-time Order Book**: Live view of buy and sell orders grouped by price
- **Order Management**: Create buy/sell orders with amount and price specifications
- **Automatic Order Matching**: Orders are automatically matched when compatible orders exist
- **Credit Management**: Users can only place orders within their available credit limit
- **Idempotent Operations**: Duplicate order requests are safely handled using idempotency tokens
- **Admin Panel**: Filament-based admin interface for order management
- **Transaction Safety**: Database transactions ensure data consistency during order processing

## Tech Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.3+ (with PHP-FPM)
- **Database**: PostgreSQL 18
- **Cache**: Redis 8
- **Authentication**: Laravel Sanctum (API tokens)
- **Admin Panel**: Filament 4.x
- **Precision Math**: Brick/Math (BigDecimal)
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx

## Architecture Overview

The application follows a **layered architecture** with clear boundaries:

```
┌─────────────────────────────────────────────────┐ ┌──────────┐ 
│         Application Layer (app/)                │ │          │
│  Controllers, Requests, Resources, Services     │ │          │
└─────────────────────────────────────────────────┘ │          │
                      ↓                             │   Core   │
┌─────────────────────────────────────────────────┐ │          │
│           Domain Layer (domain/)                │ │          │
│  Entities, Aggregates, Value Objects, Repos     │ │          │
│  Exceptions, Enums, Services (Interfaces)       │ │          │
└─────────────────────────────────────────────────┘ └──────────┘
```

### Key Architectural Patterns

- **Domain Layer**: Contains pure business logic with no framework dependencies
- **Application Layer**: Handles HTTP concerns, validation, and orchestration
- **Core Layer**: Shared foundation code and cross-cutting concerns

## How to Run

### Prerequisites

- Docker and Docker Compose installed
- Git

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd <repository>
   ```

2. **Build and start the containers**
   ```bash
   docker-compose up -d --build
   ```

3. **Install dependencies and set up the application**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app composer setup
   ```

   This will:
   - Install PHP dependencies
   - Generate application key
   - Run database migrations
   - Install NPM dependencies
   - Build frontend assets

4. **Seed the database (optional)**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

5. **Access the application**
   - **API**: http://localhost/api
   - **Admin Panel**: http://localhost/admin
   - **Web Server**: Nginx running on port 80
   - **Database**: PostgreSQL on port 5432
   - **Redis**: Redis on port 6379

### Development Commands

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Run tests
docker-compose exec app composer test

# Calculate order book snapshot
docker-compose exec app php artisan app:calculate-order-book

# Simulate order creation (for testing order book)
docker-compose exec app php artisan app:simulate-orders

# Simulate with initial orders
docker-compose exec app php artisan app:simulate-orders --initial=50

# Access container shell
docker-compose exec app bash

# View logs
docker-compose logs -f app
```

### Docker Services

- **app**: Laravel application (PHP-FPM)
- **nginx**: Web server
- **db**: PostgreSQL database
- **redis**: Redis cache
- **scheduler**: Laravel scheduler service

## Project Structure

```
wallgold/
├── app/                         # Application & Infrastructure Layer
│   ├── Casts/                   # Eloquent value object casts
│   ├── Console/                 # Artisan commands
│   ├── Contracts/               # Application contracts
│   ├── Filament/                # Admin panel resources
│   ├── Http/                    # HTTP layer
│   │   ├── Controllers/         # API controllers
│   │   ├── Requests/            # Form requests
│   │   └── Resources/           # API resources
│   ├── Models/                  # Eloquent models
│   ├── Repositories/            # Repository implementations
│   ├── Serializers/             # Serialization logic
│   └── Services/                # Service implementations
├── core/                        # Core Foundation Layer
│   ├── Database/                # Database base classes
│   ├── Foundation/              # Foundation classes
│   ├── Idempotency/             # Idempotency infrastructure
│   └── Support/                 # Support utilities
├── domain/                      # Domain Layer
│   ├── Aggregates/              # Domain aggregates
│   │   ├── Order/               # Order aggregate
│   │   └── OrderBook/           # OrderBook aggregates
│   ├── Concerns/                # Reusable traits
│   ├── Entities/                # Domain entities
│   ├── Enum/                    # Domain enums
│   ├── Exceptions/              # Domain exceptions
│   ├── Repositories/            # Repository interfaces
│   ├── Services/                # Service interfaces
│   └── ValueObjects/            # Value objects
├── database/
│   ├── factories/               # Model factories
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── tests/                       # Test suite
│   ├── Feature/                 # Feature tests
│   └── Unit/                    # Unit tests
└── routes/                      # Route definitions
    ├── api.php                  # API routes
    └── web.php                  # Web routes
```

## Requirements

The application implements the following functional requirements:

1. **Order Book Display**: A REST API showing the current order book with buy and sell orders grouped by price
2. **Order Creation**: Users can create buy/sell orders by specifying:
   - The amount of asset (minimum: 1)
   - The price for the asset (minimum: 1)
3. **Credit Validation**: Users can only place orders within their available credit limit
4. **Order Matching**: Orders are automatically matched when compatible opposite orders exist

## Database Design

### users table

| Column     | Type                         | Description                                             |
|------------|------------------------------|---------------------------------------------------------|
| id         | big integer (auto increment) | Primary key                                             |
| email      | varchar(255)                 | User's email address (unique)                           |
| credit     | decimal(21, 6)               | User's available credit (uses BigDecimal for precision) |
| created_at | timestamp                    | Record creation timestamp                               |
| updated_at | timestamp                    | Record update timestamp                                 |

### orders table

| Column            | Type                                | Description                                     |
|-------------------|-------------------------------------|-------------------------------------------------|
| id                | big integer (auto increment)        | Primary key                                     |
| user_id           | big integer (foreign key)           | Reference to users table                        |
| amount            | decimal(21, 6)                      | The amount of asset in the order                |
| price             | decimal(21, 6)                      | The price per unit for the order                |
| type              | enum('buy', 'sell')                 | Order type: buy or sell                         |
| idempotency_token | string (UUID)                       | Unique token for idempotency checks             |
| state             | enum('pending', 'completed')        | Order state: pending or completed               |
| matched_order_id  | big integer (foreign key, nullable) | Reference to the matched order (self-reference) |
| created_at        | timestamp                           | Record creation timestamp                       |
| updated_at        | timestamp                           | Record update timestamp                         |

**Indexes:**
- Primary key on `id`
- Foreign key on `user_id` → `users.id` (restrict on delete)
- Foreign key on `matched_order_id` → `orders.id` (restrict on delete)

### order_book_snapshots table

| Column     | Type                         | Description                                                   |
|------------|------------------------------|---------------------------------------------------------------|
| id         | big integer (auto increment) | Primary key                                                   |
| data       | json                         | Snapshot of the order book (buy/sell orders grouped by price) |
| created_at | timestamp                    | Record creation timestamp                                     |
| updated_at | timestamp                    | Record update timestamp                                       |

## API Design

All API endpoints require authentication via Laravel Sanctum. Include the bearer token in the `Authorization` header.

### Get Order Book

**Endpoint:** `GET /api/order-book`

**Authentication:** Required (Bearer token)

**Response:**
- **200 OK**
  ```json
  {
    "orderBook": {
      "buyOrders": [
        {
          "price": "100.50",
          "count": 5
        }
      ],
      "sellOrders": [
        {
          "price": "101.25",
          "count": 3
        }
      ]
    }
  }
  ```

**Description:** Returns the current order book with buy and sell orders grouped by price, showing the count of orders at each price level.

### Create Order

**Endpoint:** `POST /api/order`

**Authentication:** Required (Bearer token)

**Request Body:**
```json
{
  "type": "buy",
  "amount": "10.5",
  "price": "100.00",
  "idempotencyToken": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Parameters:**
- `type` (required, enum: `"buy"` | `"sell"`): The type of order
- `amount` (required, decimal: 0-6, min: 1): The amount of asset
- `price` (required, decimal: 0-6, min: 1): The price per unit
- `idempotencyToken` (required, UUID): Unique token to ensure idempotency

**Response:**
- **201 Created**: Order successfully created
  ```json
  {}
  ```

- **401 Unauthorized**: User is not authenticated
  ```json
  {}
  ```

- **403 Forbidden**: Insufficient credit
  ```json
  {
    "reason": "notEnoughCredit"
  }
  ```

- **422 Validation Error**: Validation failed
  ```json
  {
    "message": "The given data was invalid.",
    "errors": {
      "amount": ["The amount field is required."]
    }
  }
  ```

- **500 Internal Server Error**: Server error occurred
  ```json
  {}
  ```

**Description:** Creates a new buy or sell order. The order is automatically matched if a compatible opposite order exists. If matched, both orders are marked as completed. The user's credit is deducted when placing an order.

**Note:** The same `idempotencyToken` can be used multiple times safely - duplicate requests with the same token will return success without creating duplicate orders.

## Architecture Explanations

### Additional Data Bag vs. Inheritance

The application uses the **Additional Data Bag pattern** implemented via the `HasAdditionalData` trait instead of using inheritance hierarchies for value objects that need to carry extra data.

**Why Additional Data Bag?**

1. **Flexibility**: Value objects (VOs) like `SetOrderData` and `CreateOrderData` can carry implementation-specific data (like `idempotencyToken`) without polluting their domain-focused API.

2. **Single Responsibility**: VOs remain focused on their core domain purpose. Additional data for infrastructure concerns (like HTTP request tokens) doesn't become part of the domain contract.

3. **Composition over Inheritance**: Instead of creating class hierarchies (e.g., `SetOrderDataWithIdempotency extends SetOrderData`), we compose additional data dynamically.

4. **Loose Coupling**: The domain layer doesn't need to know about implementation details like idempotency tokens, but can still pass them through when needed.

**Example Usage:**
```php
// In SetOrder aggregate, we can pass additional data from one VO to another
$createOrderData = new CreateOrderData(...);
$createOrderData->additional($setOrderData->getAdditional()); // Passes idempotencyToken
```

This pattern is particularly useful in DDD contexts where domain objects should remain framework-agnostic, but we need to transmit implementation-specific metadata through the layers.

### Skipping DDD Strategic Design Phase

This application intentionally **skips the strategic design phase** of DDD (identifying bounded contexts, context mapping, etc.) for the following reasons:

1. **Single Bounded Context**: The application is small enough that it represents a single, cohesive bounded context focused on order book trading. There are no conflicting domain models or multiple subdomains requiring explicit boundaries.

2. **Prototype/Assignment Context**: As a focused assignment or prototype, the overhead of strategic DDD (event storming, context mapping, etc.) would be disproportionate to the benefit.

3. **Tactical Patterns Focus**: The emphasis is on demonstrating **tactical DDD patterns** (entities, aggregates, value objects, repositories, domain services) rather than strategic design.

4. **Clear Domain Boundary**: The domain is self-contained - orders, users, and order books belong to the same domain with no need for anti-corruption layers or context mappings.

**What We Still Apply:**
- Aggregates with transaction boundaries (`SetOrder`, `CalculateOrderBook`)
- Domain entities with rich behavior (`User`, `Order`)
- Value objects for immutable data (`CreateOrderData`, `OrderBook`)
- Repository interfaces in the domain layer
- Domain services for cross-aggregate operations
- Ubiquitous language in code and naming


## Code Style Guide

### Constants in Models

All Eloquent models use **public class constants** for column names instead of string literals throughout the codebase.

**Example:**
```php
class Order extends Model
{
    public const AMOUNT = 'amount';
    public const PRICE = 'price';
    public const TYPE = 'type';
    
    protected $fillable = [
        self::AMOUNT,
        self::PRICE,
        self::TYPE,
    ];
}
```

**Reasons for This Approach:**

1. **Type Safety**: Using constants provides IDE autocomplete and prevents typos in column names.

2. **Refactoring Safety**: If a column name changes, updating the constant definition updates all references automatically. IDEs can reliably find all usages.

3. **Self-Documenting Code**: Constants serve as documentation of available columns, especially useful for models with many fields.

4. **IDE Support**: Modern IDEs can track constant usages, provide refactoring tools, and highlight issues.

5. **Information Hiding**: This complies with the **Information Hiding** principle.

6. **Consistency**: All model attribute references follow the same pattern (`Order::AMOUNT` vs scattered string literals).

7. **Compile-Time Checks**: PHP will catch undefined constants at parse time, while string literals only fail at runtime.

**Usage Pattern:**
```php
// In repositories
OrderModel::where(OrderModel::AMOUNT, $amount)->get();

// In migrations
$table->decimal(Order::AMOUNT, 21, 6);

// In factories
'amount' => self::AMOUNT, // Even in factories, reference via constant when possible
```

This pattern is especially valuable in Laravel applications where column names are referenced in migrations, models, factories, repositories, and queries.

## Testing

The application includes comprehensive test coverage:

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test suite
docker-compose exec app php artisan test --testsuite=Feature
docker-compose exec app php artisan test --testsuite=Unit
```

**Test Structure:**
- **Feature Tests**: Integration tests for aggregates, API endpoints, repositories, services
- **Unit Tests**: Unit tests for entities, value objects, and domain logic

**Key Test Areas:**
- Order creation and matching logic
- Credit validation
- Idempotency handling
- Order book calculation
- Repository implementations
- API request/response handling

## Authentication

The API uses **Laravel Sanctum** for token-based authentication.

**Getting an API Token:**
1. Run the predefined seeder for users(UserSeeder). This will output the first user token.
2. Include the token in requests: `Authorization: Bearer {token}`

**Protected Routes:**
- `POST /api/order` - Requires authentication
- `GET /api/order-book` - Requires authentication

## Order Matching Logic

Orders are automatically matched when a compatible opposite order exists:

**Matching Criteria:**
- Orders must have opposite types (buy ↔ sell)
- Orders must have the same `amount`
- Orders must have the same `price`
- Orders must be in `pending` state
- The matched order must be different from the current order

**Matching Process:**
1. When a new order is created, the system searches for the oldest matching order
2. If found, both orders are marked as `completed`
3. Each order's `matched_order_id` is set to reference the other order
4. The matching uses database locks (`lockForUpdate()`) to prevent race conditions

**Order Book Calculation:**
- The order book groups pending orders by price level
- Each price level shows the count of orders at that price
- Buy orders and sell orders are calculated separately
- Snapshots can be generated via the `app:calculate-order-book` command

## Order Simulation

The application includes a command to simulate order creation for testing and demonstrating the order book functionality.

### Simulate Orders Command

**Command:** `php artisan app:simulate-orders`

**Purpose:** Continuously creates random buy/sell orders to populate the order book for testing purposes.

**Features:**
- Creates orders every 5 seconds automatically
- Randomly selects users from the database
- Generates orders with prices between 1000-1002 and amounts between 0-100
- Automatically handles credit validation (retries if user has insufficient credit)
- Can be stopped gracefully with `Ctrl+C`

**Options:**
- `--initial=N`: Creates N initial orders before starting the continuous simulation

**Usage Examples:**

```bash
# Start continuous simulation (creates orders every 5 seconds)
docker-compose exec app php artisan app:simulate-orders

# Create 50 initial orders first, then start continuous simulation
docker-compose exec app php artisan app:simulate-orders --initial=50
```

**How It Works:**
1. If `--initial` is provided, creates the specified number of orders using the factory
2. Enters a continuous loop that:
   - Randomly selects a user from the database
   - Generates a random order (buy or sell) with constrained price/amount ranges
   - Authenticates as that user and creates the order through the `SetOrder` aggregate
   - Waits 5 seconds before repeating
3. Handles `NotEnoughCreditException` by retrying with a different user
4. Responds to `SIGINT` (Ctrl+C) to stop gracefully

**Note:** This command requires the `pcntl` extension to handle signal interrupts. Ensure users exist in the database (via seeders) before running the simulation.

