FROM php:8.3-fpm

ARG XDEBUG_ON=true
ARG XDEBUG_MODE="debug"
ARG XDEBUG_PORT=9003
ARG XDEBUG_IDEKEY="PHPSTORM"

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j$(nproc) pdo_mysql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

RUN if [ "${XDEBUG_ON}" = "true" ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug && \
        echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.client_port=${XDEBUG_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.idekey=${XDEBUG_IDEKEY}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini ; \
    fi

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/public

EXPOSE 9000

CMD ["php-fpm"]
