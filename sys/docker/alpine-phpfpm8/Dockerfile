FROM node:14.14.0-alpine3.12 AS jsbuilder

WORKDIR /application

COPY . /application

RUN apk add --no-cache --virtual frontenddeps \
      python3 \
      yarn \
   && npm install -g --unsafe-perm node-sass@4.14.1 \
   && yarn config set ignore-engines true \
   && yarn install --production \
   && yarn add @symfony/webpack-encore --production \
   && yarn add sass-loader@^8.0.0 node-sass@4.14.1 --dev \
   && npm run build

FROM php:8.0.0RC3-fpm-alpine3.12

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
    # && pickle install msgpack@2.1.1 --defaults --no-interaction \
    && ln -nfs /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY sys/php/php.ini /usr/local/etc/php/php.ini
COPY sys/php/docker-php-ext-opcache8.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY sys/php/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY sys/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY sys/php/zz-docker8.conf /usr/local/etc/php-fpm.d/zz-docker8.conf

WORKDIR /application

COPY . /application
COPY --from=jsbuilder /application/public/build /application/public/build

# REF: https://github.com/api-platform/api-platform/issues/1532
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

COPY --from=composer:1 /usr/bin/composer /usr/local/bin/composer

RUN mv composer8.json composer.json \
    && mv composer8.lock composer.lock \
    && mv symfony8.lock symfony.lock \
    && /usr/local/bin/composer install --no-plugins --no-scripts --optimize-autoloader --no-ansi --no-interaction --no-progress --no-suggest --ignore-platform-reqs \
    && apk del .builddeps \
    && php-fpm -t \
    && php-fpm -tt

CMD ["/application/sys/docker/alpine-phpfpm/start.sh"]
