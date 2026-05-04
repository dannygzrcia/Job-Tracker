# Job Tracker API

A REST API built with PHP, Slim Framework, and MySQL to track job applications.

## Endpoints
- POST /api/register
- POST /api/login
- GET /api/jobs
- POST /api/jobs
- PUT /api/jobs/{id}
- DELETE /api/jobs/{id}

## Setup
1. Clone the repo
2. Run `composer install`
3. Create a `.env` file with your DB credentials
4. Import the database schema
5. Run with `php -S localhost:8000 -t public`
