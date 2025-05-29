# 🎒 Laravel Backpack MVP Project

This is the MVP version of Backpackers, a Laravel + Backpack-based web application for managing events, applications, and user roles with impersonation capabilities.

---

## 📦 Requirements

-   PHP 8.x
-   Composer
-   Node.js & npm
-   MySQL or compatible database
-   Laravel 10+
-   Backpack for Laravel

---

## 🚀 Installation & Setup

```bash
# 1. Clone the repository
https://github.com/aimanbadhrul/backpackers.git
preferably use laragon to manage environment

# 2. Install dependencies
composer install
npm install && npm run dev

# 3. Copy and configure .env
cp .env.example .env
php artisan key:generate

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Create storage link
php artisan storage:link

# 6. Run the app
php artisan serve
```
