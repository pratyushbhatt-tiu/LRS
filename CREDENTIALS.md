# Laravel Recording System - Demo Credentials

## Demo User Accounts

All demo users are pre-seeded in the database with the following credentials:

### Admin Account
- **Email**: `admin@lrs.local`
- **Password**: `password`
- **Role**: Admin
- **Permissions**: Full system access

### Operations Account
- **Email**: `operations@lrs.local`
- **Password**: `password`
- **Role**: Operations
- **Permissions**: File creation, processing, and master data viewing

### QC Account
- **Email**: `qc@lrs.local`
- **Password**: `password`
- **Role**: QC
- **Permissions**: File viewing, editing, and approval

### Accounting Account
- **Email**: `accounting@lrs.local`
- **Password**: `password`
- **Role**: Accounting
- **Permissions**: File and master data viewing, report generation and export

### Read-Only Account
- **Email**: `readonly@lrs.local`
- **Password**: `password`
- **Role**: Read-Only
- **Permissions**: View-only access to files and master data

## Database Setup

### MySQL Configuration (Recommended)

Update your `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lrs_db
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### Running Migrations and Seeders

```bash
# Create the database first (if not exists)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS lrs_db;"

# Run migrations
php artisan migrate:fresh

# Run seeders
php artisan db:seed
```

## Security Notes

> ⚠️ **IMPORTANT**: These credentials are for **DEVELOPMENT ONLY**

- Never use these credentials in production
- All passwords should be changed immediately in production environments
- Implement password complexity requirements
- Force password change on first login for production users
- Use environment-specific .env files
- Never commit .env files to version control

## First Login

1. Start the development server: `php artisan serve`
2. Navigate to: `http://localhost:8000`
3. Click "Log in"
4. Use any of the demo credentials above
5. You will be redirected to the dashboard

## Troubleshooting

### Cannot log in
- Ensure migrations have been run: `php artisan migrate:fresh`
- Ensure seeders have been run: `php artisan db:seed`
- Check database connection in `.env`
- Clear config cache: `php artisan config:clear`

### Permission errors
- Ensure Spatie Permission tables are migrated
- Ensure RolesAndPermissionsSeeder has been run
- Check that user has been assigned a role

### Database connection errors
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`
