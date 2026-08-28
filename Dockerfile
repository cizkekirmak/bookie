FROM php:8.2-apache

# Gerekli sistem paketleri ve PHP eklentileri
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Apache mod_rewrite aktif et
RUN a2enmod rewrite

# Apache DocumentRoot ayarını public klasörüne yönlendir
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Composer kurulumu
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Proje dosyalarını kopyala
WORKDIR /var/www/html
COPY . /var/www/html

# Bağımlılıkları yükle
RUN composer install --no-dev --optimize-autoloader

# Depolama izinleri ve sembolik link
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && php artisan storage:link

EXPOSE 80

CMD ["apache2-foreground"]