FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user=www-data
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Set up Nginx
RUN apt-get update && apt-get install -y nginx

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copy application files
COPY --chown=www-data:www-data . /var/www/html/

# Set permissions
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Create startup script
COPY docker/startup.sh /usr/local/bin/startup
RUN chmod +x /usr/local/bin/startup

# Set the entrypoint to our startup script
ENTRYPOINT ["startup"]
