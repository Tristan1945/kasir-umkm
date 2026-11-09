FROM php:8.4-apache

# Install ekstensi yang dibutuhkan Laravel.
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory dan salin source aplikasi.
WORKDIR /var/www/html
COPY . .

# Install dependency production.
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Arahkan Apache ke folder public Laravel dan siapkan permission.
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "php artisan migrate --force && apache2-foreground"]
