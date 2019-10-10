ARG PHP_VER
FROM devilster/apache2-php-for-dev:${PHP_VER}
ARG PHP_VER

MAINTAINER FB

RUN DEBIAN_FRONTEND=noninteractive

# START PHP Modules Install
RUN apt-get update && apt-get install -y --no-install-recommends \
        --allow-downgrades --allow-remove-essential --allow-change-held-packages \
        php$PHP_VER-sqlite3 \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
# END PHP Modules Install

WORKDIR /var/www

COPY sites /etc/apache2/sites-available

RUN a2ensite docksite

RUN a2dissite default-ssl
RUN a2dissite 000-default
