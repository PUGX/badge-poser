FROM badge-poser:base

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
