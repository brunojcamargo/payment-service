#!/bin/bash

if [ -d "vendor" ]; then
    rm -rf vendor
fi

composer install --no-dev --optimize-autoloader

php artisan key:generate

php artisan migrate --force

exec "$@"
