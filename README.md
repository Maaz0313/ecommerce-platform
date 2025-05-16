<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## E-Commerce Platform Features

This e-commerce platform is built with Laravel and includes the following features:

### Customer Features

-   Product browsing with category filtering
-   Product details with image gallery
-   Shopping cart with AJAX functionality (add, update, remove items without page reload)
-   "Buy Now" feature for direct checkout from product pages
-   User registration and authentication
-   Checkout with authentication requirement
-   Multiple payment methods (Cash on Delivery, Credit/Debit Card)
-   Coupon code system for discounts
-   Order history and tracking
-   Order cancellation functionality
-   Contact form

### Admin Features

-   Secure admin authentication system (separate from customer login)
-   Dashboard with sales analytics
-   Product management with image upload and drag-and-drop reordering
-   Category management
-   Order management with Amazon-style status updates
-   Coupon management (fixed amount and percentage discounts)
-   User management
-   Contact message management

### Payment Features

-   Cash on Delivery (COD) payment option
-   Credit/Debit Card payments (Visa/Mastercard)
-   Pakistani Rupee (₨) currency support
-   Payment status tracking
-   Secure payment processing

### Technical Features

-   Responsive design with Bootstrap 5
-   AJAX cart operations for seamless user experience
-   Image optimization and thumbnail generation
-   Security measures to prevent unauthorized access
-   User-specific order protection
-   Form data preservation during checkout process

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development/)**
-   **[Active Logic](https://activelogic.com)**

## Order Management System

The platform implements an Amazon-style order status system:

-   **Order Received**: Initial status when an order is placed
-   **Preparing for Shipment**: Order is being processed and prepared
-   **Shipped**: Order has been shipped to the customer
-   **Delivered**: Order has been successfully delivered
-   **Cancelled**: Order has been cancelled by customer or admin

Customers can cancel orders only when they are in "Order Received" or "Preparing for Shipment" status.

## Coupon System

The platform includes a flexible coupon system with the following features:

-   **Discount Types**: Fixed amount (₨) or percentage (%) discounts
-   **Usage Limits**: Set maximum number of times a coupon can be used
-   **Minimum Order Amount**: Set minimum purchase requirement for coupon eligibility
-   **Validity Period**: Set start and expiration dates for coupons
-   **Coupon Status**: Enable/disable coupons as needed

Coupons are applied at checkout and automatically validated before application.

## E-Commerce Platform Setup

### Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan migrate --seed` to create the database tables and seed with sample data
5. Run `php artisan storage:link` to create the symbolic link for uploads
6. Run `php artisan key:generate` to generate an application key
7. Run `php artisan serve` to start the development server

### Admin Access

An admin user is created during seeding with the following credentials:

-   Email: admin@example.com
-   Password: admin123

Alternatively, you can run the setup script to create or update the admin user:

```
php setup_admin.php
```

### Payment Configuration

The platform supports multiple payment methods:

#### Cash on Delivery

-   Enabled by default, no additional configuration required

#### Credit/Debit Card Payments

To configure card payments:

1. Copy the required environment variables from `stripe-env-example.txt` to your `.env` file
2. Update with your payment gateway credentials:

```
# Payment Gateway API Keys
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
STRIPE_WEBHOOK_TOLERANCE=300
```

For production, replace the test keys with live keys from your payment gateway provider.

### Security Features

The platform includes the following security features:

-   Separate admin authentication system
-   Admin users are prevented from using the public login page
-   Regular users are prevented from accessing admin functionality
-   Order information is protected - users can only see their own orders
-   Authentication required for checkout and order management
-   Secure payment processing with industry-standard practices
-   CSRF protection for all forms
-   Input validation and sanitization throughout the application

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
