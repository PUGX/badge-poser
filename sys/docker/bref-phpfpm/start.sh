#!/bin/sh

if [ "$ENABLE_CW" = "1" ]; then
    echo "Enabling CloudWatch Agent"
    /opt/aws/amazon-cloudwatch-agent/bin/start-amazon-cloudwatch-agent
fi

if [ "$APP_ENV" = "prod" ]; then
    echo "Dumping .env for $APP_ENV"
    cd /application
    printenv | egrep $(printf "%s" "$(egrep -v "^#|^$" .env | sed "s/=.*//")" | tr "\n" "|") > .env.new && mv .env.new .env
    echo "COMPOSER_ALLOW_SUPERUSER=1 /usr/local/bin/composer dump-env $APP_ENV"
    COMPOSER_ALLOW_SUPERUSER=1 /usr/local/bin/composer dump-env $APP_ENV
fi

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
