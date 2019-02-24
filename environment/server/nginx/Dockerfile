FROM nginx:alpine

ARG PGID=1000
ARG PUID=1000

RUN apk update \
    && apk upgrade \
    && apk add --no-cache openssl

RUN rm /etc/nginx/conf.d/default.conf
