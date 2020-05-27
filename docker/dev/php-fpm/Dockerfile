FROM bref/php-74-fpm-dev:latest

USER root

RUN mkdir -p /home/dev/.composer \
    && chown -R 1000:1000 /home/dev/

RUN mkdir -p /tmp/cache \
    && chown -R 1000:1000 /tmp/cache

ENV COMPOSER_CACHE_DIR='/home/dev/.composer'

# Install prestissimo
RUN curl -qs https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet \
    && php composer.phar global require hirak/prestissimo
