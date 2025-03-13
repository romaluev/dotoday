#!/bin/bash

# DoToday API Deployment Script
# This script helps with common deployment tasks

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print colored output
print_message() {
  echo -e "${GREEN}[DoToday]${NC} $1"
}

print_warning() {
  echo -e "${YELLOW}[Warning]${NC} $1"
}

print_error() {
  echo -e "${RED}[Error]${NC} $1"
}

# Check if docker and docker-compose are installed
if ! command -v docker &> /dev/null; then
  print_error "Docker is not installed. Please install Docker first."
  exit 1
fi

if ! command -v docker-compose &> /dev/null; then
  print_error "Docker Compose is not installed. Please install Docker Compose first."
  exit 1
fi

# Check if .env file exists, if not create from example
if [ ! -f .env ]; then
  print_message "Creating .env file from .env.example..."
  cp .env.example .env
  print_message ".env file created. You may want to edit it before continuing."
  exit 0
fi

case "$1" in
  start)
    print_message "Starting DoToday API containers..."
    docker-compose up -d
    print_message "DoToday API is now running at http://localhost:8080"
    ;;
    
  stop)
    print_message "Stopping DoToday API containers..."
    docker-compose down
    print_message "DoToday API containers stopped."
    ;;
    
  restart)
    print_message "Restarting DoToday API containers..."
    docker-compose restart
    print_message "DoToday API containers restarted."
    ;;
    
  rebuild)
    print_message "Rebuilding and starting DoToday API containers..."
    docker-compose up -d --build
    print_message "DoToday API rebuilt and started at http://localhost:8080"
    ;;
    
  logs)
    print_message "Showing logs for DoToday API containers..."
    docker-compose logs -f
    ;;
    
  migrate)
    print_message "Running database migrations..."
    docker-compose exec app php artisan migrate
    print_message "Migrations completed."
    ;;
    
  seed)
    print_message "Running database seeders..."
    docker-compose exec app php artisan db:seed
    print_message "Database seeded."
    ;;
    
  artisan)
    if [ -z "$2" ]; then
      print_error "Please provide an Artisan command."
      echo "Usage: $0 artisan [command]"
      exit 1
    fi
    
    shift
    print_message "Running Artisan command: $@"
    docker-compose exec app php artisan "$@"
    ;;
    
  composer)
    if [ -z "$2" ]; then
      print_error "Please provide a Composer command."
      echo "Usage: $0 composer [command]"
      exit 1
    fi
    
    shift
    print_message "Running Composer command: $@"
    docker-compose exec app composer "$@"
    ;;
    
  bash)
    print_message "Opening bash shell in the app container..."
    docker-compose exec app bash
    ;;
    
  psql)
    print_message "Connecting to PostgreSQL database..."
    docker-compose exec db psql -U postgres -d dotoday
    ;;

  debug)
    print_message "Checking container status..."
    docker-compose ps
    print_message "\nChecking app container logs..."
    docker-compose logs app
    ;;
    
  *)
    echo "DoToday API Deployment Script"
    echo ""
    echo "Usage: $0 [command]"
    echo ""
    echo "Commands:"
    echo "  start       Start all containers"
    echo "  stop        Stop all containers"
    echo "  restart     Restart all containers"
    echo "  rebuild     Rebuild and start containers"
    echo "  logs        Show container logs"
    echo "  migrate     Run database migrations"
    echo "  seed        Run database seeders"
    echo "  artisan     Run an Artisan command"
    echo "  composer    Run a Composer command"
    echo "  bash        Open bash shell in the app container"
    echo "  psql        Connect to PostgreSQL database"
    echo "  debug       Show container status and logs for debugging"
    ;;
esac
