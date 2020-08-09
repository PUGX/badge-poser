FROM badge-poser:base

RUN apt-get update \
    && apt-get install -y --no-install-recommends nginx \
    && rm -rf /var/lib/apt/lists/*

COPY . /application

RUN composer install --no-dev --no-plugins --no-scripts --optimize-autoloader --no-ansi --no-interaction --no-progress --no-suggest \
    && yarn config set ignore-engines true \
    && yarn install --production \
    && composer require encore \
    && yarn add @symfony/webpack-encore --production \
    && yarn add sass-loader@^8.0.0 node-sass --dev \
    && npm run build
