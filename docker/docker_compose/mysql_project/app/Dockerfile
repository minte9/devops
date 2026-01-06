FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite

# Install PHP extensions needed for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Apache site config
COPY my-site.conf /etc/apache2/conf-available/my-site.conf
RUN a2enconf my-site

# Copy site files
COPY src/ /var/www/html/
