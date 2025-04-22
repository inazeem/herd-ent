# Herd ENT Laravel Application

A Laravel-based application for ENT practice management with features for invoicing, user management, and permissions.

## Features

- User Authentication and Authorization
- Role-based Permissions
- Invoice Management
- Patient Records
- Quote Management

## Requirements

- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

## Installation

1. Clone the repository
```bash
git clone https://github.com/inazeem/herd-ent.git
cd herd-ent
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in the .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=herd_ent
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders
```bash
php artisan migrate --seed
```

6. Compile assets
```bash
npm run dev
```

7. Start the server
```bash
php artisan serve
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
