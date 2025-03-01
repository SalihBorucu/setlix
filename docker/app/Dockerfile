FROM ubuntu:22.04

LABEL maintainer="Kodeas"

WORKDIR /var/www/setlix-dev

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y curl zip unzip git supervisor sqlite3 software-properties-common \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y nginx \
       php8.2-fpm php8.2-cli php8.2-gd php8.2-mysql \
       php8.2-mbstring php8.2-xml php8.2-curl php8.2-sqlite3 \
       php8.2-pdo php8.2-pdo-mysql php8.2-mysqli \
       php8.2-bcmath \
       php-redis \
    && update-alternatives --set php /usr/bin/php8.2 \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    # Remove Lines
    && apt-get remove -y --purge software-properties-common \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    # Configure Nginx
    && echo "daemon off;" >> /etc/nginx/nginx.conf \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Create directory for PHP-FPM socket
RUN mkdir -p /var/run/php && \
    chown www-data:www-data /var/run/php

COPY nginx/default /etc/nginx/sites-available/default
COPY fpm/php-fpm.conf /etc/php/8.2/fpm/php-fpm.conf

EXPOSE 80

COPY supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
