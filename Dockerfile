FROM php:8.2-apache

# Install required dependencies and PHP extensions (gd, mysqli, pdo, pdo_mysql)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Custom PHP settings for uploads and performance
RUN printf 'upload_max_filesize = 50M\npost_max_size = 50M\nmemory_limit = 256M\nmax_execution_time = 300\ndate.timezone = Asia/Kolkata\n' > /usr/local/etc/php/conf.d/custom.ini

# Configure Apache Directory permissions and AllowOverride for .htaccess
RUN printf '\n<Directory /var/www/html>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads

# Expose standard HTTP and Render default ports
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

