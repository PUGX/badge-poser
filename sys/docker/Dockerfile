FROM bref/php-80-fpm-dev

COPY --from=bref/extra-gd-php-80 /opt /opt
COPY --from=bref/extra-imagick-php-80 /opt /opt
COPY --from=bref/extra-redis-php-80 /opt /opt
COPY --from=bref/extra-yaml-php-80 /opt /opt

ENV LD_LIBRARY_PATH=/opt/bref/lib64:/opt/bref/lib
