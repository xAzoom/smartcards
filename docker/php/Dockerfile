FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libzip-dev curl git wget zlib1g-dev libxml2-dev libicu-dev \
 --no-install-recommends && apt-get clean
RUN docker-php-ext-install zip intl pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html
COPY ./docker/php/entrypoint.sh /usr/local/entrypoint.sh

RUN chmod 755 /usr/local/entrypoint.sh

ENTRYPOINT ["/usr/local/entrypoint.sh"]
CMD ["php-fpm"]