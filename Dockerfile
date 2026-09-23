FROM php:8.2-apache

# Install MySQL extension for PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files to Apache web root
COPY . /var/www/html/

# Enable Apache rewrite module
RUN a2enmod rewrite

# Expose HTTP port
EXPOSE 80