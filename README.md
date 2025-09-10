# Event Ticketing

This is a Laravel-based project that uses Vite for asset bundling and front-end development.

## Requirements

- PHP >= 8.0
- Composer
- Node.js & npm
- Laravel 9.x or higher
- Vite

## Installation

1. Clone the repository:
  ```bash
  git clone https://github.com/your-repo/event-ticketing.git
  cd event-ticketing
  ```

2. Install PHP dependencies:
  ```bash
  composer install
  ```

3. Install Node.js dependencies:
  ```bash
  npm install
  ```

4. Copy the `.env.example` file to `.env` and configure your environment variables:
  ```bash
  cp .env.example .env
  ```

5. Generate the application key:
  ```bash
  php artisan key:generate
  ```

6. Run database migrations:
  ```bash
  php artisan migrate
  ```

7. Build assets with Vite:
  ```bash
  npm run build
  ```

8. Start the development server:
  ```bash
  php artisan serve
  npm run dev
  ```

## Features

- Laravel backend
- Vite-powered front-end
- Scalable and maintainable architecture

## License

This project is open-source and available under the [MIT License](LICENSE).