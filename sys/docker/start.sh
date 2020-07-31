#!/bin/bash

php-fpm &
nginx -g "daemon off;"
