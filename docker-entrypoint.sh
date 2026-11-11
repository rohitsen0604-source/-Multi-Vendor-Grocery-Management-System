#!/bin/bash
set -e

PORT="${PORT:-80}"

echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads || true
chmod -R 777 /var/www/html/uploads || true

exec "$@"
