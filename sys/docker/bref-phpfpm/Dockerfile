FROM bref/php-80-fpm-dev:1.2.7

ENV LD_LIBRARY_PATH=/opt/bref/lib64:/opt/bref/lib

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=bref/extra-gd-php-80 /opt /opt
COPY --from=bref/extra-imagick-php-80 /opt /opt
COPY --from=bref/extra-redis-php-80 /opt /opt
COPY --from=bref/extra-yaml-php-80 /opt /opt
COPY --from=awsbuilder /tmp /tmp
COPY --from=awsbuilder /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/ca-certificates.crt
COPY --from=awsbuilder /opt/aws/amazon-cloudwatch-agent /opt/aws/amazon-cloudwatch-agent
COPY sys/php/php.ini /opt/bref/etc/php/php.ini
COPY sys/php/docker-php-ext-opcache8.ini /opt/bref/etc/php/conf.d/ext-opcache.ini
COPY sys/php/php-fpm.conf /opt/bref/etc/php-fpm.conf.default
COPY sys/php/www.conf /opt/bref/etc/php-fpm.d/www.conf.default

COPY . /var/task

WORKDIR /var/task

RUN /usr/local/bin/composer install --optimize-autoloader --no-ansi --no-interaction --no-progress \
    && php-fpm -t \
    && php-fpm -tt

ENTRYPOINT ["/var/task/sys/docker/bref-phpfpm/start.sh"]
CMD /opt/bin/php-fpm --nodaemonize --fpm-config /opt/bref/etc/php-fpm.conf -d opcache.validate_timestamps=1 --force-stderr
