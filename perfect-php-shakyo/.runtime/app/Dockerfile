FROM php:8.2-fpm-alpine
RUN apk add --update --no-cache openssl-dev pcre-dev autoconf git icu-libs zsh && \
    apk add --no-cache --virtual build-dependencies build-base icu-dev oniguruma-dev linux-headers

RUN NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} intl mysqli pdo_mysql mbstring
RUN pecl install apcu xdebug
RUN apk del --no-cache --purge build-dependencies && \
    rm -rf /tmp/pear