# TrackTern

A Laravel web application built with Breeze authentication.

## Features

- 🔐 User Authentication (Login/Register)  
- 📊 Dashboard
- 👤 User Profile Management
- 🎨 Modern UI with Tailwind CSS
- 📱 Responsive Design

## Tech Stack

- **Framework:** Laravel 12.x
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js
- **Database:** MySQL  
- **Authentication:** Laravel Breeze
- **Build Tool:** Vite

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL

## Installation

1. Clone the repository
```bash
git clone https://github.com/BH69/tracktern.git
cd tracktern
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node.js dependencies
```bash
npm install
```

4. Copy environment file
```bash
cp .env.example .env
```

5. Generate application key
```bash
php artisan key:generate
```

6. Configure your database in `.env` file
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracktern
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations
```bash
php artisan migrate
```

8. Build assets
```bash
npm run dev
```

## Development

Start the development server:
```bash
# Laravel development server
php artisan serve

# Asset building (in another terminal)
npm run dev
```

## License

Open source under the [MIT License](LICENSE).

## Author

Built by Raduel
