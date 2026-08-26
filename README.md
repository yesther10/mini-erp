# Mini ERP

Initial MVP foundation for a small ERP prototype built with Laravel, Vue 3, Inertia, MySQL, and Docker.

## Stack

- Laravel 13
- Vue 3
- Inertia.js
- MySQL 8.4
- Nginx
- Docker Compose

## Prerequisites

- Docker
- Docker Compose

Host PHP, Composer, and Node are not required for the normal workflow.

## First-time setup

1. Copy the environment file:

   ```bash
   cp .env.example .env
   ```

2. Build the application image:

   ```bash
   docker compose build app
   ```

3. Start the containers:

   ```bash
   docker compose up -d mysql app nginx
   ```

4. Generate the app key if needed:

   ```bash
   docker compose run --rm app php artisan key:generate
   ```

5. Run migrations:

   ```bash
   docker compose run --rm app php artisan migrate
   ```

6. Install frontend dependencies if they are missing and start Vite:

   ```bash
   docker compose run --rm app npm install
   docker compose run --rm --service-ports app npm run dev
   ```

7. Open the app at <http://localhost:8000>.

## Daily workflow

Start the backend services:

```bash
docker compose up -d mysql app nginx
```

Start the frontend dev server when working on UI changes:

```bash
docker compose run --rm --service-ports app npm run dev
```

Useful commands:

```bash
docker compose run --rm app composer install
docker compose run --rm app php artisan migrate
docker compose run --rm app php artisan test
docker compose run --rm app npm run build
```

## Services

- `app`: PHP-FPM container with Composer and Node/NPM available
- `nginx`: web server exposing the app on port `8000`
- `mysql`: database server exposing port `3306`

## Current scope

This repository contains only the application foundation and a simple Inertia dashboard page to validate the stack integration. Business modules are intentionally not included yet.
