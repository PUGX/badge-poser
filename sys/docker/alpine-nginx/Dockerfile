FROM node:14.14.0-alpine3.12 AS jsbuilder

WORKDIR /application

COPY . /application

RUN apk add --no-cache --virtual frontenddeps \
       python3 \
       yarn \
    && npm install -g --unsafe-perm node-sass \
    && yarn config set ignore-engines true \
    && yarn install --production \
    && yarn add @symfony/webpack-encore --production \
    && yarn add sass-loader@^8.0.0 node-sass --dev \
    && npm run build

FROM fabiocicerchia/nginx-lua:1.19.3-alpine3.12.0

WORKDIR /application

COPY sys/nginx/nginx.conf /etc/nginx/nginx.conf
COPY sys/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY sys/nginx/lua /etc/nginx/conf.d/lua
COPY . /application
COPY --from=jsbuilder /application/public/build /application/public/build

RUN mkdir -p /data/nginx/cache \
    && chmod -R 777 /data
