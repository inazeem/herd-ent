# Herd ENT - Installation Guide

## Prerequisites

Before installing Herd ENT, ensure you have the following:

- PHP 8.1 or higher
- Composer 2.0+
- Node.js 16+ and NPM
- MySQL 8.0+ or PostgreSQL 14+
- Git

## Step-by-Step Installation

### 1. Clone the Repository

```bash
git clone https://github.com/inazeem/herd-ent.git
cd herd-ent
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file and configure your database connection:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=herd_ent
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Additional configurations:

```
APP_NAME="Herd ENT"
APP_URL=http://localhost:8000

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Database Setup

Create your database:

```bash
# MySQL
mysql -u root -p
CREATE DATABASE herd_ent;
exit;

# OR PostgreSQL
createdb herd_ent
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

This will:
- Create all necessary database tables
- Seed the database with:
  - Default roles (super-admin, administrator, clinician, biller, frontdesk)
  - Default permissions
  - Admin user (email: admin@example.com, password: password)
  - Sample data for testing

### 6. Storage Configuration

```bash
php artisan storage:link
```

### 7. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 8. Start the Application

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### 9. Default Login Credentials

After installation, you can log in with:

- Email: admin@example.com
- Password: password

## Troubleshooting

### Common Issues

1. **Database Connection Issues**
   - Verify your database credentials in the `.env` file
   - Ensure your database server is running

2. **Permission Errors**
   - Ensure the `storage` and `bootstrap/cache` directories are writable:
     ```bash
     chmod -R 775 storage bootstrap/cache
     ```

3. **Composer Dependencies Issues**
   - Try clearing composer cache:
     ```bash
     composer clear-cache
     ```

4. **Node.js/NPM Issues**
   - Verify your Node.js version:
     ```bash
     node -v
     ```
   - Clear NPM cache:
     ```bash
     npm cache clean --force
     ```

## Updating the Application

To update the application to the latest version:

```bash
git pull
composer install
php artisan migrate
npm install
npm run build
php artisan optimize:clear
``` 