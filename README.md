# DoToday API

![Laravel](https://img.shields.io/badge/Laravel-12.0-red)
![License](https://img.shields.io/badge/License-MIT-green)

A modern task management API built with Laravel, featuring PostgreSQL for data storage and Meilisearch for lightning-fast search capabilities.

## Features

- **Task Management**: Create, update, and organize your daily tasks
- **User Authentication**: Secure authentication system with Laravel Sanctum
- **Search**: Fast and efficient task searching with Meilisearch
- **Docker Ready**: Easy deployment with Docker and Docker Compose

## Quick Start

### Using the Helper Script

We've included a convenient script to manage your deployment:

```bash
# Start app
./deploy.sh start

# Stop the app
./deploy.sh stop

# View logs
./deploy.sh logs
```

### Manual Setup

1. **Clone the repository**

2. **Set up environment variables**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

4. **Access the application**
   
   The API will be available at http://localhost:8080

## Docker Architecture

The application is containerized with Docker and includes the following services:

| Service | Description | Port |
|---------|-------------|------|
| **app** | PHP-FPM application container | - |
| **nginx** | Web server | 8000 |
| **db** | PostgreSQL database | 5432 |
| **meilisearch** | Search engine | 7700 |

## Development Commands

### Database Operations

```bash
# Run migrations
./deploy.sh migrate

# Seed the database
./deploy.sh seed

# Connect to PostgreSQL
./deploy.sh psql
```

### Application Commands

```bash
# Run artisan commands
./deploy.sh artisan <command>

# Run composer commands
./deploy.sh composer <command>

# Access bash shell in the app container
./deploy.sh bash
```

## Deployment Workflow

1. **Update your code**
   ```bash
   git pull
   ```

2. **Rebuild containers with latest changes**
   ```bash
   ./deploy.sh rebuild
   ```

3. **Run migrations if needed**
   ```bash
   ./deploy.sh migrate
   ```

The startup script automaticaly does:
- Waiting for database connection
- Installing dependencies
- Running migrations
- Caching configs

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.
