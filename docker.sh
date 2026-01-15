#!/usr/bin/env bash

# AtomCMS Docker Helper Script
# Replacement for Laravel Sail commands

set -e

COMPOSE="docker compose"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Helper function to print colored messages
print_message() {
    echo -e "${GREEN}[AtomCMS]${NC} $1"
}

print_error() {
    echo -e "${RED}[Error]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[Warning]${NC} $1"
}

# Display help
show_help() {
    cat << EOF
AtomCMS Docker Helper Script

Usage: ./docker.sh [command] [options]

Commands:
    up              Start all containers
    down            Stop all containers
    restart         Restart all containers
    build           Build/rebuild containers
    ps              Show container status
    logs            Show container logs

    shell           Open bash shell in app container
    root-shell      Open bash shell as root in app container

    artisan         Run artisan command
    composer        Run composer command
    npm             Run npm command
    test            Run tests
    pint            Run Laravel Pint

    mysql           Open MySQL/MariaDB CLI
    redis           Open Redis CLI

    fresh           Fresh install (destroy volumes and rebuild)
    clean           Remove all containers and volumes

Examples:
    ./docker.sh up
    ./docker.sh artisan migrate
    ./docker.sh composer install
    ./docker.sh npm run dev
    ./docker.sh test --filter=ExampleTest

EOF
}

# Check if docker compose is available
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed or not in PATH"
    exit 1
fi

# Main command handler
case "${1}" in
    up)
        print_message "Starting containers..."
        $COMPOSE up -d
        ;;
    down)
        print_message "Stopping containers..."
        $COMPOSE down
        ;;
    restart)
        print_message "Restarting containers..."
        $COMPOSE restart
        ;;
    build)
        print_message "Building containers..."
        $COMPOSE build "${@:2}"
        ;;
    ps)
        $COMPOSE ps
        ;;
    logs)
        $COMPOSE logs -f "${@:2}"
        ;;
    shell)
        print_message "Opening shell in app container..."
        $COMPOSE exec app bash
        ;;
    root-shell)
        print_message "Opening root shell in app container..."
        $COMPOSE exec -u root app bash
        ;;
    artisan)
        $COMPOSE exec app php artisan "${@:2}"
        ;;
    composer)
        $COMPOSE exec app composer "${@:2}"
        ;;
    npm)
        $COMPOSE exec app npm "${@:2}"
        ;;
    test)
        $COMPOSE exec app php artisan test "${@:2}"
        ;;
    pint)
        $COMPOSE exec app ./vendor/bin/pint "${@:2}"
        ;;
    mysql)
        $COMPOSE exec mariadb mysql -u"${DB_USERNAME:-atomcms}" -p"${DB_PASSWORD:-password}" "${DB_DATABASE:-atomcms}"
        ;;
    redis)
        $COMPOSE exec redis redis-cli
        ;;
    fresh)
        print_warning "This will destroy all data and rebuild containers. Are you sure? (y/N)"
        read -r response
        if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
            print_message "Destroying containers and volumes..."
            $COMPOSE down -v
            print_message "Building containers..."
            $COMPOSE build
            print_message "Starting containers..."
            $COMPOSE up -d
            print_message "Running migrations..."
            $COMPOSE exec app php artisan migrate:fresh --seed
        else
            print_message "Cancelled."
        fi
        ;;
    clean)
        print_warning "This will remove all containers and volumes. Are you sure? (y/N)"
        read -r response
        if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
            print_message "Removing containers and volumes..."
            $COMPOSE down -v
            print_message "Done."
        else
            print_message "Cancelled."
        fi
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        if [ -z "${1}" ]; then
            show_help
        else
            print_error "Unknown command: ${1}"
            echo ""
            show_help
            exit 1
        fi
        ;;
esac
