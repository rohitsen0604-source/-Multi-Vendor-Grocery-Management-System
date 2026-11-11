#!/bin/bash
set -e

PORT="${PORT:-80}"

sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads
chmod -R 777 /var/www/html/uploads

exec "$@"

