FROM debian:8
MAINTAINER Mateusz Koszutowski <mkoszutowski@divante.pl>

ENV woocommerce_path /var/www/wordpress
ENV plugin_path wp-content/plugins/bliskapaczka-shipping-method

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    wget \
    curl \
    git \
    apt-utils \
    sudo \
    nginx \
    mysql-client \
    php5 \
    php5-fpm \
    php5-cli \
    php5-mysql \
    php5-mcrypt \
    php5-curl \
    php5-gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
RUN  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Nginx
COPY nginx.conf /etc/nginx/nginx.conf
COPY woocommerce.conf /etc/nginx/sites-available/woocommerce.conf
RUN (cd /etc/nginx/sites-enabled && ln -s ../sites-available/woocommerce.conf woocommerce.conf && rm -rf default)

ENV WORDPRESS_VERSION 4.8.2

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip wget \
    && wget https://downloads.wordpress.org/release/pl_PL/wordpress-$WORDPRESS_VERSION.tar.gz  -O /tmp/temp.tar.gz \
    && cd ${woocommerce_path}/../ \
    && tar xvf /tmp/temp.tar.gz \
    && rm /tmp/temp.tar.gz \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

ENV WOOCOMMERCE_VERSION 3.2.1

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip wget \
    && wget https://downloads.wordpress.org/plugin/woocommerce.$WOOCOMMERCE_VERSION.zip -O /tmp/temp.zip \
    && cd ${woocommerce_path}/wp-content/plugins \
    && unzip /tmp/temp.zip \
    && rm /tmp/temp.zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip wget \
    && wget https://downloads.wordpress.org/theme/storefront.2.2.7.zip -O /tmp/temp.zip \
    && cd ${woocommerce_path}/wp-content/themes \
    && unzip /tmp/temp.zip \
    && rm /tmp/temp.zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# WooCommerce Translations
COPY ${plugin_path}/dev/docker/woocommerce/ ${woocommerce_path}/wp-content/languages/plugins/

# Download WordPress CLI
RUN curl -L "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar" > /usr/bin/wp && \
    chmod +x /usr/bin/wp

# Copy wordpress configuration
COPY woocommerce_wp-config.php ${woocommerce_path}/wp-config.php

# Copy latest version of Bliskapaczka module
COPY wp-content ${woocommerce_path}/wp-content

RUN find ${woocommerce_path} -type d -exec chmod 770 {} \; && find ${woocommerce_path} -type f -exec chmod 660 {} \; \
    && chown -R :www-data ${woocommerce_path}

COPY run /opt/run

EXPOSE 80

CMD bash /opt/run
