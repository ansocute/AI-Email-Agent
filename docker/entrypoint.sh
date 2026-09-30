#!/bin/sh
set -e

# 1. Chạy migration để tạo bảng cache (và các bảng khác) trên Neon DB trước
php artisan migrate --force

# 2. Làm sạch và tạo lại cache sau khi DB đã sẵn sàng
php artisan config:clear
php artisan cache:clear
php artisan route:cache
php artisan view:cache

exec "$@"