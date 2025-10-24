
FROM php:8.3-apache


ENV DEBIAN_FRONTEND=noninteractive


RUN apt-get update && apt-get install -y \
    sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


RUN a2enmod rewrite


COPY . /var/www/html/


EXPOSE 80