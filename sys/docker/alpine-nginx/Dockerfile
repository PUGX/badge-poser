# AWSBUILDER
################################################################################
FROM debian:latest as awsbuilder

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update &&  \
    apt-get install -y ca-certificates curl && \
    rm -rf /var/lib/apt/lists/*

RUN curl -O https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/$(dpkg --print-architecture)/latest/amazon-cloudwatch-agent.deb && \
    dpkg -i -E amazon-cloudwatch-agent.deb && \
    rm -rf /tmp/* && \
    rm -rf /opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-config-wizard && \
    rm -rf /opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl && \
    rm -rf /opt/aws/amazon-cloudwatch-agent/bin/config-downloader

# JSBUILDER
################################################################################
FROM node:18.20.4-alpine3.20 AS jsbuilder

WORKDIR /application

COPY package.json /application

RUN apk add --no-cache --virtual frontenddeps \
        python3 \
    && yarn config set ignore-engines true \
    && yarn install --ignore-platform

COPY . /application

RUN yarn build

# MAIN
################################################################################
FROM fabiocicerchia/nginx-lua:1.25.4-alpine3.19.1

ENV RUN_IN_CONTAINER="True"

WORKDIR /application

RUN echo "*/5 * * * * find /data/nginx/cache/ -type f -newermt '30 minutes ago' -print0 | xargs -0 /application/sys/docker/alpine-nginx/expire.sh" >> /var/spool/cron/crontabs/root

COPY sys/nginx/nginx.conf /etc/nginx/nginx.conf
COPY sys/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY sys/nginx/lua /etc/nginx/conf.d/lua
COPY --chown=nginx:nginx . /application
COPY --from=jsbuilder /application/public/build /application/public/build
COPY --from=awsbuilder /tmp /tmp
COPY --from=awsbuilder /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/ca-certificates.crt
COPY --from=awsbuilder /opt/aws/amazon-cloudwatch-agent /opt/aws/amazon-cloudwatch-agent

RUN mkdir -p /data/nginx/cache \
    && chmod -R 777 /data

ENTRYPOINT ["/application/sys/docker/alpine-nginx/start.sh"]

CMD ["nginx", "-g", "daemon off;"]
