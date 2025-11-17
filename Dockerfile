FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql intl zip bcmath pcntl

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]
