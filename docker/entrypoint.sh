#!/bin/sh
set -e

# Tự động cache config & route khi container khởi động
php artisan config:clear
php artisan cache:clear
php artisan route:cache
php artisan view:cache

# Chạy migration database Neon
php artisan migrate --force

exec "$@"