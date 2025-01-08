FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN a2enmod rewrite headers

WORKDIR /var/www/html
COPY . /var/www/html/

RUN echo '<?php header("Location: /login.php"); ?>' > /var/www/html/public/pages/index.php


RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN chown -R www-data:www-data /var/www/html

# Updated Apache configuration to include pages subdirectory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public/pages
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Updated symlink path
RUN mkdir -p /opt/render/project/src && \
    ln -s /var/www/html/public/pages /opt/render/project/src/public

EXPOSE 80
CMD ["apache2-foreground"]