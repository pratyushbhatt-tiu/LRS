# LRS Deployment Guide

Follow these steps to deploy the Laravel Recording System (LRS) to a production or staging environment.

## 1. Prerequisites
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Database**: MySQL 8.0 or PostgreSQL
- **Web Server**: Nginx or Apache
- **Node.js**: 18+ (for build process)

---

## 2. Server Setup

### Clone and Install
```bash
git clone <repository_url> lrs
cd lrs
composer install --optimize-autoloader --no-dev
```

### Environment Configuration
1. Copy the example file: `cp .env.example .env`
2. Update the following values in `.env`:
   - `APP_URL`: Your domain
   - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `APP_ENV=production`
   - `APP_DEBUG=false`

### Key Generation
```bash
php artisan key:generate
```

---

## 3. Database & Optimization

### Migrations and Seeding
```bash
# Run migrations with production-safe flag
php artisan migrate --force

# Seed initial permissions and master data
php artisan db:seed --class=RolesAndPermissionsSeeder --force
```

### Application Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Frontend Compilation
Build the production assets using Vite:
```bash
npm install
npm run build
```

---

## 5. Background Processing (Optional)
If utilizing large bulk imports, ensure the queue worker is running:
```bash
php artisan queue:work --queue=default --timeout=300
```

---

## 6. Permissions & Storage
Ensure the web server has write access to the standard Laravel directories:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

---

## 7. Post-Deployment Check
1. Access `APP_URL/login`.
2. Verify Admin login.
3. Access `/reports` to confirm "Operational Intelligence" dashboard is hydrating from the database.
