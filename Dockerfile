# 1. Use the latest stable PHP + Apache image
FROM php:8.2-apache

# 2. Install the PostgreSQL driver dependencies
# libpq-dev is required for the pdo_pgsql extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# 3. Enable Apache mod_rewrite
# This is useful if you ever want to use pretty URLs (.htaccess)
RUN a2enmod rewrite

# 4. Set the working directory
WORKDIR /var/www/html

# 5. Copy all your files into the container
# This copies index.php, config.php, save_comment.php, style.css, and Afryl.jpg
COPY . /var/www/html/

# 6. Fix permissions so Apache can read your files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 7. Expose the standard web port
EXPOSE 80

# 8. Start Apache
CMD ["apache2-foreground"]
