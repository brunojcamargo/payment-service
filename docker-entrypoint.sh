#!/bin/bash

set -a
. /var/www/html/.env
set +a

if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader
    php artisan key:generate
fi

php artisan cache:clear
php artisan config:cache

sleep 5

php artisan migrate --force

exec "$@"
