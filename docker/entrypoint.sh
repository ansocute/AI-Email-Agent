#!/bin/sh
set -e

php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec apache2-foreground