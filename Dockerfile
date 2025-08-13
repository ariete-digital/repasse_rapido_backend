FROM php:8.2-fpm-alpine

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && mkdir -p /app \
    && chown -R www-data:www-data /app

RUN apk --update add --no-cache \
	${PHPIZE_DEPS} \
    openssl-dev \
    libxml2-dev \
    fcgi \
    bash \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install \
        pdo_mysql \
        soap \
        xml \
        mysqli

COPY ./ /app

# RUN if [ "$APP_ENV" = "local" ] ; then \
#         composer install --no-autoloader; else \
#         composer install --no-autoloader --no-dev --no-interaction --optimize-autoloader ; \
#     fi

RUN composer install

EXPOSE 9000
CMD ["php-fpm"]