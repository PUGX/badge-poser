#!/bin/sh

cd /application

# Only for production
#/usr/local/bin/composer dump-env prod

php-fpm
