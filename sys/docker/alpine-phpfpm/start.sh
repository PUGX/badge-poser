#!/bin/sh

IS_DEV=$(ping -c 1 -t 5 169.254.169.253 > /dev/null; echo $?)

# Only for production
if [ "$IS_DEV" == "0" ]; then
    /opt/aws/amazon-cloudwatch-agent/bin/start-amazon-cloudwatch-agent
    cd /application
    /usr/local/bin/composer dump-env $APP_ENV
fi

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
