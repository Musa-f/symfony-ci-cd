FROM php:8.2-apache

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd xdebug pdo_mysql \
    && apt-get update \
    && apt-get install -y mariadb-client \
    && apt-get install -y curl git unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY . /var/www/

COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN cd /var/www/symfony-ci-cd/ && composer install --no-interaction --prefer-dist

WORKDIR /var/www/

ENTRYPOINT [ "bash", "./script/entrypoint.sh" ]

EXPOSE 80
