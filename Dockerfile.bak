FROM php:8.2-apache

# Build trigger v2: 2026-01-15-00-36

# Install dependencies dan ekstensi PHP yang diperlukan
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Fix MPM conflict - remove ALL mpm configs then enable only prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.conf /etc/apache2/mods-enabled/mpm_*.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/ \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set Apache DocumentRoot ke /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Update Apache config untuk memungkinkan .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy semua file project ke container
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
