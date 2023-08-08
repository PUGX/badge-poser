FROM php:8.1.11-fpm-alpine

ENV RUN_IN_CONTAINER="True"

WORKDIR /application

RUN apk add --no-cache --virtual .builddeps \
        autoconf=2.71-r0 \
        g++=11.2.1_git20220219-r2 \
        gcc=11.2.1_git20220219-r2 \
        make=4.3-r0 \
        unzip=6.0-r9 \
        wget=1.21.3-r0 \
    && apk add --no-cache \
        freetype-dev=2.12.1-r0 \
        git=2.36.6-r0 \
        icu-dev=71.1-r2 \
        jpeg-dev=9d-r1 \
        libpng-dev=1.6.37-r1 \
        libzip-dev=1.8.0-r1 \
        oniguruma-dev=6.9.8-r0 \
        yaml-dev=0.2.5-r0 \
        zlib-dev=1.2.12-r3 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        zip \
    && wget https://github.com/FriendsOfPHP/pickle/releases/download/v0.7.11/pickle.phar -O /usr/local/bin/pickle \
    && chmod +x /usr/local/bin/pickle \
    && pickle install yaml@2.1.0 --defaults --no-interaction \
    && pickle install redis@5.3.7 --defaults --no-interaction \
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
