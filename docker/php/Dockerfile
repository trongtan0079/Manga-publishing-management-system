FROM php:8.2-apache

# Install PHP extensions for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache URL rewriting
RUN a2enmod rewrite

# Set Apache web root
WORKDIR /var/www/html

# Copy Manga PMS source code
COPY . /var/www/html

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80