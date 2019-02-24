ARG PHP_VERSION=7.2
FROM php:${PHP_VERSION}-fpm-alpine

# Remove default html folder
RUN rm -rf /var/www/html

# Istall git and curl
RUN apk add --no-cache curl git

# Create ini file from defaults
ARG PHP_INI_ENVIRONMENT=development
RUN cp "/usr/local/etc/php/php.ini-${PHP_INI_ENVIRONMENT}" /usr/local/etc/php/php.ini

##########################################################################################
## PHP DEPENDENCIES
##########################################################################################

# Add phpize system depencencies (they will be removed at the end)
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS

# APCU
ARG INSTALL_APCU=false
RUN if [ ${INSTALL_APCU} = true ]; then \
    pecl install apcu \
    && docker-php-ext-enable apcu \
;fi

# bcmath
ARG INSTALL_BCMATH=false
RUN if [ ${INSTALL_BCMATH} = true ]; then \
    docker-php-ext-install bcmath \
;fi

# GD
ARG INSTALL_GD=false
RUN if [ ${INSTALL_GD} = true ]; then \
    apk add --no-cache \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
;fi

# intl
ARG INSTALL_INTL=false
RUN if [ ${INSTALL_INTL} = true ]; then \
    apk add --no-cache icu-dev \
    && docker-php-ext-install intl \
;fi

# memcached
ARG INSTALL_MEMCACHED=false
RUN if [ ${INSTALL_MEMCACHED} = true ]; then \
    apk add --no-cache libmemcached-libs zlib \
    && apk add --no-cache --virtual .memcached-ext-build-deps zlib-dev libmemcached-dev cyrus-sasl-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && apk del .memcached-ext-build-deps \
;fi

# mongo
ARG INSTALL_MONGO=false
RUN if [ ${INSTALL_MONGO} = true ]; then \
    apk add --no-cache --virtual .mongodb-ext-build-deps openssl-dev pcre-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del .mongodb-ext-build-deps \
;fi

# mysql
ARG INSTALL_MYSQL=false
RUN if [ ${INSTALL_MYSQL} = true ]; then \
    docker-php-ext-install mysqli pdo_mysql \
;fi

# opcache (this extension is important for php-fpm)
ARG INSTALL_OPCACHE=false
RUN if [ ${INSTALL_OPCACHE} = true ]; then \
    docker-php-ext-install opcache \
;fi

# postgresql
ARG INSTALL_PGSQL=false
RUN if [ ${INSTALL_PGSQL} = true ]; then \
    apk add --no-cache postgresql-dev \
    && docker-php-ext-install pgsql pdo_pgsql \
;fi

# redis
ARG INSTALL_PHPREDIS=false
RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
    pecl install redis \
    && docker-php-ext-enable redis \
;fi

# SOAP
ARG INSTALL_SOAP=false
RUN if [ ${INSTALL_SOAP} = true ]; then \
    apk add --no-cache libxml2-dev \
    && docker-php-ext-install soap \
;fi

# SQLITE
ARG INSTALL_SQLITE=false
RUN if [ ${INSTALL_SQLITE} = true ]; then \
    apk add --no-cache sqlite-dev \
    && docker-php-ext-install pdo_sqlite \
;fi

# tokenizer
ARG INSTALL_TOKENIZER=false
RUN if [ ${INSTALL_TOKENIZER} = true ]; then \
    docker-php-ext-install tokenizer \
;fi

# xdebug
# @todo For php 7.3 there is no stable xdebug yet. Replace "xdebug" with "xdebug-beta" if you want it anyways
ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug \
    && docker-php-ext-enable xdebug \
;fi

# zip
ARG INSTALL_ZIP=false
RUN if [ ${INSTALL_ZIP} = true ]; then \
    apk add --no-cache libzip-dev \
    && docker-php-ext-install zip \
;fi

# Delete phpize system dependencies (not needed any more)
RUN apk del .phpize-deps

##########################################################################################
## PHALCON FRAMEWORK
##########################################################################################

# @todo this might not be working for php 7.3 yet
ARG INSTALL_PHALCON_FRAMEWORK=true
ARG PHALCON_FRAMEWORK_VERSION=3.4.2
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

##########################################################################################
## MACHINE CONFIGURATION
##########################################################################################

ARG PGID=1000
ARG PUID=1000

# Create a user for composer
RUN addgroup -g ${PGID} docker \
    && adduser -D -G docker -u ${PUID} -s /bin/sh docker

# Change folder owner to docker user
RUN chown docker:docker -R /var/www

# Makefile support
RUN apk add --no-cache make

# Copy additional ini configuration
COPY ./ini/*.ini /usr/local/etc/php/conf.d/

# Change user running php-fpm to docker
RUN sed -i "s/user = www-data/user = docker/" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = docker/" /usr/local/etc/php-fpm.d/www.conf

##########################################################################################
## COMPOSER
##########################################################################################

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PATH="${PATH}:/home/docker/.composer/vendor/bin"

# Switch to user docker to install composer packages
USER docker

# Faster composer install inside the container
RUN composer global require hirak/prestissimo \
    && composer global require phpunit/phpunit \
    && composer global require phalcon/devtools

# Switch back to root
USER root

##########################################################################################
## CLEANUP
##########################################################################################

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

##########################################################################################
## PHP-FPM
##########################################################################################

CMD ["php-fpm"]

# Change workdir
WORKDIR /var/www

EXPOSE 9000