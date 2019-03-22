FROM launcher.gcr.io/google/debian9:latest

# Build arguments
ARG UID=${USER_ID}
ARG GID=${GROUP_ID}

# Configure build
ENV DEBIAN_FRONTEND noninteractive
RUN rm /etc/localtime && \
    ln -s /usr/share/zoneinfo/Europe/London /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

# Add the www user
RUN groupadd -g ${GID} www && \
    useradd -u ${UID} -g ${GID} -s /bin/bash www

# Add required packages
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
        apt-transport-https \
        ca-certificates \
        curl \
        git \
        gnupg2 \
        inotify-tools \
        iproute2 \
        lsb-release \
        mysql-client \
        patch \
        ssh-client \
        ssmtp \
        unzip \
        wget \
		cron \
		vim \
		procps

# Install PHP
RUN curl -fsSL https://packages.sury.org/php/apt.gpg | apt-key add - && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list && \
    apt-get update -o Dir::Etc::sourcelist="sources.list.d/php.list" -o Dir::Etc::sourceparts="-" -o APT::Get::List-Cleanup="0"


# Add scripts
ADD ./scripts /scripts
RUN chmod +x -R /scripts

# Yarn
#RUN curl -fsSL https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
#    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee -a /etc/apt/sources.list.d/yarn.list && \
#    apt-get update -o Dir::Etc::sourcelist="sources.list.d/yarn.list" -o Dir::Etc::sourceparts="-" -o APT::Get::List-Cleanup="0"

# FIX Debian Stretch Error: The following signatures couldn't be verified because the public key is not available: NO_PUBKEY B188E2B695BD4743
RUN apt-get update && \
	apt-get install -y dirmngr && \
	apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys B188E2B695BD4743

# Install php with extensions
RUN apt-get update && \
	apt-get install -y --no-install-recommends \
    php7.2 \
    php7.2-fpm \
    php7.2-curl \
    php7.2-cli \
    php7.2-mysql \
    php7.2-gd \
    php7.2-intl \
    php7.2-xsl \
    php7.2-bcmath\
    php7.2-opcache \
    php7.2-mbstring \
    php7.2-zip \
    php7.2-json \
    php7.2-intl \
    php7.2-soap \
    php7.2-sqlite3 \
    php-redis \
	php7.2-dev \
	php-pear \
	gcc \ 
	make \
	autoconf \
	libc-dev \
	pkg-config \
	libmcrypt-dev \
	software-properties-common

# Install NPM & Node.Js
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
RUN apt-get update && \
	apt-get install -y --no-install-recommends \
	nodejs

# Install mcrypt (legacy for M1) and OAuth for Magento 1.9
RUN pecl install mcrypt-1.0.1 oauth

# Fix issue with php sessions
RUN mkdir -p /var/lib/php/sessions && \
    chown -R www:www /var/lib/php/sessions

# Install yarn and node
#RUN apt-get install -y \
#    yarn

# Config
ADD ./config/php /etc/php/7.2/fpm
ADD ./config/php/conf.d /etc/php/7.2/mods-available
ADD ./config/ssmtp /etc/ssmtp
ADD ./config/index-before.php /home/www/index-before.php

# Setup and add default CRON tasks
ADD ./config/cron /etc/cron.d
RUN chmod 0644 -R /etc/cron.d/

# Install n98-magerun
RUN curl -sS -o n98-magerun.phar https://files.magerun.net/n98-magerun.phar &&  \
	curl -sS -o n98-magerun.phar.sha256 https://files.magerun.net/sha256.php?file=n98-magerun.phar && \
	shasum -a 256 -c n98-magerun.phar.sha256 && \
	chmod +x n98-magerun.phar && \
	mv n98-magerun.phar /usr/local/bin/n98-magerun.phar

EXPOSE 9000 3000

# Install Ioncube
# RUN bash /scripts/install-ioncube.sh

# Install Composer
CMD ["/bin/bash", "/scripts/install-composer.sh"]

# Run supervisor to start all services
CMD ["/bin/bash", "/scripts/start.sh"]