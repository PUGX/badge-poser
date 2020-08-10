FROM node:14-alpine AS jsbuilder

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

FROM nginx:1.19-alpine

WORKDIR /application

COPY sys/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY . /application
COPY --from=jsbuilder /application/public/build /application/public/build

RUN mkdir -p /data/nginx/cache
