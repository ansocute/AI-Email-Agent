FROM php:8.3-apache

# 1. Cài đặt các thư viện hệ thống
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Cài PHP extensions bắt buộc
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Cấu hình Apache Rewrite
RUN a2enmod rewrite

# 4. Cấu hình DocumentRoot cho Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 5. Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 6. Copy file composer trước để tận dụng Docker cache
COPY composer.json composer.lock ./

# 7. Cài đặt vendor packages với đúng PHP 8.3
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 8. Copy toàn bộ source code
COPY . .

# 9. Dump-autoload lại để đảm bảo classmap chuẩn
RUN composer dump-autoload --optimize --no-scripts

# 10. Phân quyền storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Entrypoint setup
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]