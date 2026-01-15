# Docker Setup with Caddy + FrankenPHP

This project uses Docker with Caddy and FrankenPHP instead of Laravel Sail.

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Update .env file:**
   - Set `DB_HOST=mariadb`
   - Set `REDIS_HOST=redis`
   - Set `MAIL_HOST=mailpit`
   - Configure other variables as needed

3. **Start containers:**
   ```bash
   ./docker.sh up
   ```

4. **Install dependencies (first time):**
   ```bash
   ./docker.sh composer install
   ./docker.sh npm install
   ```

5. **Generate application key:**
   ```bash
   ./docker.sh artisan key:generate
   ```

6. **Run migrations:**
   ```bash
   ./docker.sh artisan migrate
   ```

7. **Build frontend assets:**
   ```bash
   ./docker.sh npm run build
   ```

## Docker Helper Script

The `./docker.sh` script provides convenient commands:

### Container Management
- `./docker.sh up` - Start all containers
- `./docker.sh down` - Stop all containers
- `./docker.sh restart` - Restart all containers
- `./docker.sh build` - Build/rebuild containers
- `./docker.sh ps` - Show container status
- `./docker.sh logs` - Show container logs

### Development Commands
- `./docker.sh shell` - Open bash shell in app container
- `./docker.sh artisan [command]` - Run artisan command
- `./docker.sh composer [command]` - Run composer command
- `./docker.sh npm [command]` - Run npm command
- `./docker.sh test` - Run tests
- `./docker.sh pint` - Run Laravel Pint

### Database & Cache
- `./docker.sh mysql` - Open MySQL/MariaDB CLI
- `./docker.sh redis` - Open Redis CLI

### Utility Commands
- `./docker.sh fresh` - Fresh install (destroy volumes and rebuild)
- `./docker.sh clean` - Remove all containers and volumes

## Services

### Application (FrankenPHP)
- **URL:** http://localhost
- **Port:** 80 (HTTP), 443 (HTTPS)
- **Vite Dev Server:** http://localhost:5173

### MariaDB
- **Host:** mariadb (from Docker) or localhost (from host)
- **Port:** 3306
- **Database:** atomcms
- **Username:** atomcms
- **Password:** password (change in .env)

### Redis
- **Host:** redis (from Docker) or localhost (from host)
- **Port:** 6379

### Mailpit
- **SMTP Port:** 1025
- **Web UI:** http://localhost:8025

## Development Workflow

### Running the Development Server

For development with hot reload:

```bash
./docker.sh npm run dev:atom
# or
./docker.sh npm run dev:dusk
```

### Running Tests

```bash
./docker.sh test
# or specific test
./docker.sh test --filter=ExampleTest
```

### Code Formatting

```bash
./docker.sh pint
```

### Accessing Logs

```bash
./docker.sh logs
# or specific service
./docker.sh logs app
```

## FrankenPHP Features

This setup uses FrankenPHP, which provides:

- **High Performance:** Built on Caddy and Go
- **Modern PHP:** PHP 8.4 support
- **HTTP/2 & HTTP/3:** Built-in support
- **Automatic HTTPS:** Via Caddy (when configured)
- **Worker Mode:** Long-running PHP processes (optional)

## Production Considerations

For production deployment:

1. Update `Dockerfile` to optimize for production:
   - Remove development dependencies
   - Enable OPcache optimizations
   - Configure worker mode

2. Configure SSL certificates in `docker/caddy/Caddyfile`

3. Set proper environment variables:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - Strong database passwords
   - Proper SMTP settings

4. Use Docker secrets for sensitive data

## Troubleshooting

### Permission Issues

If you encounter permission issues:

```bash
./docker.sh root-shell
chown -R www-data:www-data /app/storage /app/bootstrap/cache
```

### Database Connection Issues

Ensure `DB_HOST=mariadb` in your `.env` file when running in Docker.

### Port Conflicts

If ports are already in use, update them in `.env`:

```
APP_PORT=8080
FORWARD_DB_PORT=3307
FORWARD_REDIS_PORT=6380
```

## Differences from Laravel Sail

| Feature | Sail | This Setup |
|---------|------|------------|
| Web Server | Nginx/Apache | Caddy + FrankenPHP |
| PHP | PHP-FPM | FrankenPHP |
| Command | `sail` | `./docker.sh` |
| Database | MySQL/MariaDB | MariaDB |
| Mail | Mailhog | Mailpit |

## Additional Resources

- [FrankenPHP Documentation](https://frankenphp.dev/)
- [Caddy Documentation](https://caddyserver.com/docs/)
- [Docker Documentation](https://docs.docker.com/)
