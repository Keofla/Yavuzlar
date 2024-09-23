FROM php:8.4-rc-apache

RUN apt-get update && apt-get install -y \
    git \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite
COPY src /var/www/html/
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80