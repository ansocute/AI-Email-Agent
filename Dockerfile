FROM php:8.2-apache

# 1. Cài đặt các thư viện hệ thống cần thiết (bao gồm libpq-dev cho PostgreSQL)
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

# 2. Cài đặt các PHP extensions bắt buộc cho Laravel & Neon PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Bật module rewrite của Apache cho Laravel
RUN a2enmod rewrite

# 4. Cấu hình Apache DocumentRoot chỉ hướng vào thư mục public/ của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 5. Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Thiết lập thư mục làm việc và copy code
WORKDIR /var/www/html
COPY . .

# 7. Chạy composer install
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 8. Phân quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Copy file entrypoint và cấp quyền thực thi (nếu có file docker/entrypoint.sh)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]