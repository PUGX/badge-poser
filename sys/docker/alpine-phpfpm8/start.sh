#!/bin/sh

if [ "$ENABLE_CW" = "1" ]; then
    /opt/aws/amazon-cloudwatch-agent/bin/start-amazon-cloudwatch-agent
fi

if [ "$APP_ENV" = "prod" ]; then
    cd /application
    /usr/local/bin/composer dump-env $APP_ENV
fi

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
