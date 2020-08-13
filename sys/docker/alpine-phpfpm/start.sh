#!/bin/sh

cd /application
/usr/local/bin/composer dump-env prod

php-fpm
