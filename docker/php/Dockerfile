FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk --no-cache add curl git wget bash dpkg

# Add PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions opcache iconv soap
RUN install-php-extensions zip intl fileinfo
RUN install-php-extensions pdo redis mysqli pdo_mysql
RUN install-php-extensions gd

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www
