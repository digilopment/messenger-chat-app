FROM alpine:latest

LABEL Maintainer="Tomas Doubek <thomas.doubek@gmail.com>" \
      Description="Quesenger"

RUN apk --update add ca-certificates
RUN apk --no-cache add php81 php81-fpm nginx supervisor curl

# Install EXTENSIONS
RUN apk --no-cache add \
    sqlite \
    php-session \
    php-sqlite3 \
    php-mbstring \
    redis \
    php81-pdo_mysql \
    php81-pdo_sqlite \
    php81-tokenizer \
    php81-xml \
    php81-xmlwriter \
    php81-bcmath \
    php81-curl \
    php81-exif \
    php81-fileinfo \
    php81-gd \
    php81-iconv \
    php81-intl \
    php81-json \
    php81-opcache \
    php81-pcntl \
    php81-pdo \
    php81-phar \
    php81-posix \
    php81-session \
    php81-simplexml \
    php81-sockets \
    php81-sodium \
    php81-sysvmsg \
    php81-sysvsem \
    php81-sysvshm \
    php81-xmlreader \
    php81-xsl \
    php81-zip

# Install necessary dependencies
RUN apk --no-cache add \
    $PHPIZE_DEPS \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    icu-dev \
    libmemcached-dev \
    gmp-dev

#install mariadb
RUN apk add mariadb

#configs
COPY docker/app.conf /etc/nginx/nginx.conf
COPY docker/fpm-pool.conf /etc/php81/php-fpm.d/www.conf
COPY docker/php.ini /etc/php81/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

#perms
RUN mkdir -p /var/www/html
RUN chown -R nobody.nobody /var/www/html && \
  chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

USER nobody

WORKDIR /var/www/html

#copy code
COPY --chown=nobody src/ /var/www/html/src
COPY --chown=nobody storage/ /var/www/html/storage
COPY --chown=nobody www/ /var/www/html/www

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
