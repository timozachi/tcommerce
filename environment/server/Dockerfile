FROM timozachi/nginx-php-fpm:latest

ARG PGID=1000
ARG PUID=1000

# Create a user for composer
RUN addgroup -g ${PGID} docker \
    && adduser -D -G docker -u ${PUID} -s /bin/sh docker

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Change the user for composer
USER docker

# phpunit for testing inside the container
RUN composer global require phpunit/phpunit

USER root

RUN apk add --no-cache curl git

# Instal php xdebug extension
ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .phpize-deps-configure \
;fi

# Instal php opcache extension
ARG INSTALL_OPCACHE=false
RUN if [ ${INSTALL_OPCACHE} = true ]; then \
    docker-php-ext-install opcache \
;fi

# Instal php intl extension
ARG INSTALL_INTL=false
RUN if [ ${INSTALL_INTL} = true ]; then \
    apk add --no-cache libintl icu icu-dev \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-install intl \
;fi

# Instal php SOAP extension
ARG INSTALL_SOAP=false
RUN if [ ${INSTALL_SOAP} = true ]; then \
    apk add --no-cache libxml2-dev \
    && docker-php-ext-configure soap --enable-soap \
    && docker-php-ext-install soap \
;fi

# Instal php zip extension
ARG INSTALL_ZIP=false
RUN if [ ${INSTALL_ZIP} = true ]; then \
    apk add --no-cache libxml2-dev \
    && docker-php-ext-install zip \
;fi

ARG INSTALL_TOKENIZER=false
RUN if [ ${INSTALL_TOKENIZER} = true ]; then \
    docker-php-ext-install tokenizer \
;fi

# Instal php mysqli extension
ARG INSTALL_MYSQLI=false
RUN if [ ${INSTALL_MYSQLI} = true ]; then \
    docker-php-ext-install mysqli pdo_mysql \
;fi

# Instal php postgre sql extension
ARG INSTALL_PGSQL=false
RUN if [ ${INSTALL_PGSQL} = true ]; then \
    apk add --no-cache postgresql-dev \
    && docker-php-ext-install pgsql pdo_pgsql \
;fi

# Instal php mongo extension
ARG INSTALL_MONGO=false
RUN if [ ${INSTALL_MONGO} = true ]; then \
    apk add --no-cache --virtual .mongodb-ext-build-deps $PHPIZE_DEPS openssl-dev pcre-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del .mongodb-ext-build-deps \
;fi

# Instal php redis extension
ARG INSTALL_PHPREDIS=false
RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
    apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .phpize-deps-configure \
;fi

# Instal php redis extension
ARG INSTALL_APCU=false
RUN if [ ${INSTALL_APCU} = true ]; then \
    apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && apk del .phpize-deps-configure \
;fi

# Instal php memcached extension
ARG INSTALL_MEMCACHED=false
RUN if [ ${INSTALL_MEMCACHED} = true ]; then \
    apk add --no-cache libmemcached-libs zlib \
    && apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS \
    && apk add --no-cache --virtual .memcached-deps zlib-dev libmemcached-dev cyrus-sasl-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && apk del .phpize-deps-configure .memcached-deps \
;fi

# Instal php gd extension
ARG INSTALL_GD=false
RUN if [ ${INSTALL_GD} = true ]; then \
    apk add --no-cache \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j"$(getconf _NPROCESSORS_ONLN)" gd \
;fi

# Instal php redis extension
ARG INSTALL_PHALCON_FRAMEWORK=false
ARG PHALCON_FRAMEWORK_VERSION=3.3.1
RUN if [ ${INSTALL_PHALCON_FRAMEWORK} = true ]; then \
    apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS bash \
    && cd /tmp \
    && curl -sSLO https://codeload.github.com/phalcon/cphalcon/tar.gz/v${PHALCON_FRAMEWORK_VERSION} \
    && tar xvzf v${PHALCON_FRAMEWORK_VERSION} \
    && cd cphalcon-${PHALCON_FRAMEWORK_VERSION}/build \
    && ./install --phpize /usr/local/bin/phpize --php-config /usr/local/bin/php-config \
    && cd /tmp \
    && rm -rf cphalcon-${PHALCON_FRAMEWORK_VERSION} \
    && rm -rf v${PHALCON_FRAMEWORK_VERSION} \
    && docker-php-ext-enable phalcon \
    && apk del .phpize-deps-configure \
;fi

# Copy nginx sites
COPY ./sites/*.conf /etc/nginx/conf.d/

# Copy additional ini configuration
COPY ./ini/*.ini /usr/local/etc/php/conf.d/
