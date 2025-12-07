# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# 1. Install dependency sistem
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# 2. Install ekstensi PHP yang wajib untuk Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Aktifkan Mod Rewrite Apache
RUN a2enmod rewrite

# 4. Ubah Document Root ke /public (Standar Laravel)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy File Proyek
WORKDIR /var/www/html
COPY . .

# 7. Install Dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# 8. Atur Hak Akses Folder Storage (PENTING!)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
