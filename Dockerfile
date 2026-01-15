FROM dunglas/frankenphp:latest

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache \
    redis \
    sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files (this will be overridden by volume mount in dev)
COPY . .

# Expose port
EXPOSE 80

# Start FrankenPHP with Laravel public directory
CMD ["frankenphp", "php-server", "--root", "/app/public", "--listen", ":80"]
