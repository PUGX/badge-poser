#!/bin/sh

cd /application

# Only for production
if [[ $APP_ENV == "prod" ]]; then /usr/local/bin/composer dump-env prod; fi

php-fpm
