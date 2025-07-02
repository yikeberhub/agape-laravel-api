# Use the official PHP image with Apache
FROM php:8.1-apache

# Install necessary extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy the application code
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install

# Set permissions (adjust if necessary)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache