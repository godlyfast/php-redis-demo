FROM php:7.2-fpm

RUN pecl install redis \
    && docker-php-ext-enable redis

CMD ["php-fpm"]
EXPOSE 9000
