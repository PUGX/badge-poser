FROM php:7.4-fpm-buster
ARG DEBIAN_FRONTEND=noninteractive

WORKDIR /application

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        cron \
        g++ \
        gcc \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libonig-dev \
        libpng-dev \
        libyaml-dev \
        libzip-dev \
        make \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        zip \
    && pecl install msgpack yaml redis \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:1 /usr/bin/composer /usr/local/bin/composer

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
    && apt-get -y --no-install-recommends install nodejs \
    && curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get -y --no-install-recommends install yarn \
    && npm install -g node-sass || true \
    && npm rebuild node-sass \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get -y remove \
        git \
        gcc \
        g++ \
        make \
    && apt-get -y clean \
    && apt-get -y autoremove \
    && rm -rf /var/lib/apt/lists/*

COPY sys/docker/start.sh ./

RUN apt-get update \
    && apt-get install -y --no-install-recommends nginx \
    && mkdir -p /data/nginx/cache \
    && rm -rf /var/lib/apt/lists/*

COPY ./sys/nginx/nginx.conf /etc/nginx/conf.d/default.conf

RUN echo "* * * * *", command: "find /data/nginx/cache/ -type f -newermt '30 seconds ago' | xargs rm" >> /var/spool/cron/crontabs/root \
    && echo "* * * * *", command: "( sleep 30 ; find /data/nginx/cache/ -type f -newermt '30 seconds ago' | xargs rm )" >> /var/spool/cron/crontabs/root

COPY ./docker/dev/php-fpm/php-ini-overrides.ini /etc/php/7.4/fpm/conf.d/99-overrides.ini
COPY . /application

RUN composer install --no-dev --no-plugins --no-scripts --optimize-autoloader --no-ansi --no-interaction --no-progress --no-suggest \
    && yarn config set ignore-engines true \
    && yarn install --production \
    && composer require encore \
    && yarn add @symfony/webpack-encore --production \
    && yarn add sass-loader@^8.0.0 node-sass --dev \
    && npm run build

RUN rm -rf /root/.composer /root/.npm /usr/local/share/.cache/yarn \
    && apt-get -y remove nodejs yarn

CMD ["./start.sh"]
