# Use the official PHP Apache image with PHP 8.1
FROM php:8.1-apache

# Set working directory in the container
WORKDIR /var/www/html

# Install PHP extensions required for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy the current directory contents into the container
COPY . /var/www/html

# Expose port 80 for web traffic
EXPOSE 80
