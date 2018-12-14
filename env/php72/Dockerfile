FROM launcher.gcr.io/google/debian9:latest

# Build arguments
ARG UID=1000
ARG GID=1000

# Configure build
ENV DEBIAN_FRONTEND noninteractive
RUN rm /etc/localtime && \
    ln -s /usr/share/zoneinfo/Europe/London /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

# Add the www user
RUN groupadd -g $GID www && \
    useradd -u $UID -g $GID -s /bin/bash www

# Add scripts
ADD ./scripts /scripts

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
		vim

# Install PHP
RUN curl -fsSL https://packages.sury.org/php/apt.gpg | apt-key add - && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list && \
    apt-get update -o Dir::Etc::sourcelist="sources.list.d/php.list" -o Dir::Etc::sourceparts="-" -o APT::Get::List-Cleanup="0"

# yarn
#RUN curl -fsSL https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
#    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee -a /etc/apt/sources.list.d/yarn.list && \
#    apt-get update -o Dir::Etc::sourcelist="sources.list.d/yarn.list" -o Dir::Etc::sourceparts="-" -o APT::Get::List-Cleanup="0"

# Install php with extensions
RUN apt-get install -y --no-install-recommends \
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
    php-redis

# Fix issue with php sessions
RUN mkdir -p /var/lib/php/sessions && \
    chown -R www:www /var/lib/php/sessions

# Install yarn and node
#RUN apt-get install -y \
#    yarn

# Ioncube
# RUN bash /scripts/install-ioncube.sh

# Composer
RUN bash /scripts/install-composer.sh

# Config
ADD ./config/php /etc/php/7.2/fpm
ADD ./config/ssmtp /etc/ssmtp

# Setup and add default CRON tasks
ADD ./config/cron /etc/cron.d
RUN chmod 0644 -R /etc/cron.d/

EXPOSE 9000

# Install Magerun
CMD ["bash","/scripts/install-magerun.sh"]

# Run supervisor to start all services
CMD ["bash","/scripts/start.sh"]