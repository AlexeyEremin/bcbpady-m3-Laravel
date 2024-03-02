#!/bin/bash
set -e

if ! [ -d "./vendor" ]; then
    composer install --optimize-autoloader
	
	php artisan storage:link
    php artisan migrate --force
    php artisan key:generate --force
    php artisan passport:install --force
	npm install
    chown -R 1000:1000 .

fi

exec "$@"