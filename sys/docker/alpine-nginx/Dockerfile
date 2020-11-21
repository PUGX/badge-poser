FROM fabiocicerchia/nginx-lua:1.19.4-alpine3.12.1

ENV RUN_IN_CONTAINER="True"

WORKDIR /application

COPY sys/nginx/nginx.conf /etc/nginx/nginx.conf
COPY sys/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY sys/nginx/lua /etc/nginx/conf.d/lua
COPY . /application
COPY --from=jsbuilder /application/public/build /application/public/build
COPY --from=awsbuilder /tmp /tmp
COPY --from=awsbuilder /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/ca-certificates.crt
COPY --from=awsbuilder /opt/aws/amazon-cloudwatch-agent /opt/aws/amazon-cloudwatch-agent

RUN mkdir -p /data/nginx/cache \
    && chmod -R 777 /data

ENTRYPOINT ["/application/sys/docker/alpine-nginx/start.sh"]

CMD ["nginx", "-g", "daemon off;"]
