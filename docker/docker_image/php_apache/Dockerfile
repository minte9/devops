FROM php:8.2-apache

# Enable useful Apache modules
RUN a2enmod rewrite

# Add custom Apache config
COPY my-site.conf /etc/apache2/conf-available/my-site.conf
RUN a2enconf my-site

# Copy site files
COPY html/ /var/www/html/
