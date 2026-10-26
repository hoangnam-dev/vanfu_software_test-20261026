FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    vim \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    python3 \
    python3-pip \
    nodejs \
    npm \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        mbstring \
        bcmath \
        gd \
        intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# entrypoint
COPY docker/php/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]