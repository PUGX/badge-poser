FROM php:8.0.16-fpm-alpine

ENV RUN_IN_CONTAINER="True"

WORKDIR /application

RUN apk add --no-cache --virtual .builddeps \
        autoconf \
        g++ \
        gcc \
        make \
        unzip \
        wget \
    && apk add --no-cache \
        freetype-dev \
        git \
        icu-dev \
        jpeg-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        yaml-dev \
        zlib-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        zip \
    && wget https://github.com/FriendsOfPHP/pickle/releases/download/v0.6.0/pickle.phar -O /usr/local/bin/pickle \
    && chmod +x /usr/local/bin/pickle \
    && pickle install yaml@2.1.0 --defaults --no-interaction \
    && pickle install redis@5.3.2 --defaults --no-interaction \
    && ln -nfs /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY sys/php/php.ini /usr/local/etc/php/php.ini
COPY sys/php/docker-php-ext-opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY sys/php/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY sys/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY sys/php/zz-docker8.conf /usr/local/etc/php-fpm.d/zz-docker8.conf
COPY --from=awsbuilder /tmp /tmp
COPY --from=awsbuilder /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/ca-certificates.crt
COPY --from=awsbuilder /opt/aws/amazon-cloudwatch-agent /opt/aws/amazon-cloudwatch-agent

COPY --chown=www-data:www-data . /application
COPY --from=jsbuilder /application/public/build /application/public/build

# REF: https://github.com/docker-library/php/issues/1121#issuecomment-886817367
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.13/community/ gnu-libiconv=1.15-r3
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN /usr/local/bin/composer install --optimize-autoloader --no-ansi --no-interaction --no-progress \
    && apk del .builddeps \
    && php-fpm -t \
    && php-fpm -tt

RUN mkdir -p /application/var/cache && chmod 777 /application/var/cache
RUN mkdir -p /application/var/log && chmod 777 /application/var/log
RUN chmod -R a+rX /application

ENTRYPOINT ["/application/sys/docker/alpine-phpfpm/start.sh"]

CMD ["php-fpm"]
