#!/bin/sh
set -eu

APP_PORT="${PORT:-10000}"

sed -ri "s/^Listen [0-9]+/Listen ${APP_PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${APP_PORT}>/" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

if [ "${RUN_DB_SEEDER:-false}" = "true" ]; then
    if [ -z "${ADMIN_PASSWORD:-}" ]; then
        echo "ADMIN_PASSWORD must be set when RUN_DB_SEEDER=true." >&2
        exit 1
    fi

    php artisan db:seed --force
fi

exec "$@"
