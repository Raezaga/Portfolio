# Use the official PHP image with Apache
FROM php:8.2-apache

# Install PostgreSQL development files and the PDO driver
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite (useful for clean URLs if needed later)
RUN a2enmod rewrite

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your project files into the container
# This includes index.php, style.css, script.js, config.php, and Afryl.jpg
COPY . /var/www/html/

# Set proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 for web traffic
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]