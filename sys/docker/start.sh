#!/bin/bash

php-fpm -y /etc/php/7.4/fpm/pool.d/www.conf &
nginx -g "daemon off;"
