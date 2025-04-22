FROM php:8.3-apache AS build-imagick

# Install mlocati/docker-php-extension-installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install imagick dependency
RUN apt update && \
    apt install -y libmagickwand-dev

# TODO: Use latest released version, after https://github.com/Imagick/imagick/issues/640 is fixed
RUN install-php-extensions pcntl bcmath opcache imagick/imagick@28f27044e435a2b203e32675e942eb8de620ee58

# Clear apt cache to save space
RUN apt clean && rm -rf /var/lib/apt/lists/*

FROM build-imagick AS php-configure

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Config
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Enable apache rewrite mod
RUN a2enmod rewrite

COPY --chown=www-data:www-data . /var/www/html

USER www-data
