FROM php:8.2-apache

# Add the PHP extension installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install PHP extensions and necessary packages
RUN install-php-extensions gd xdebug pdo_mysql \
    && apt-get update \
    && apt-get install -y mariadb-client curl git unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy application files
COPY . /var/www/

# Copy Apache configuration
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set permissions for the bin/console file and other necessary directories
RUN chmod +x /var/www/bin/console \
    && chown -R www-data:www-data /var/www

# Install Composer dependencies
RUN cd /var/www && composer install

# Set the working directory
WORKDIR /var/www/

# Expose port 80
EXPOSE 80
