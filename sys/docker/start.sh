#!/bin/bash

#redis-server &
php-fpm &
nginx -g "daemon off;"
