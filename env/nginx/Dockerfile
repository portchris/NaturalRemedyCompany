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

# Add required packages
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
        apt-transport-https \
        ca-certificates \
        gnupg2 \
        curl \
        inotify-tools

# Install nginx
RUN curl -LsS http://nginx.org/keys/nginx_signing.key | apt-key add - && \
    echo "deb http://nginx.org/packages/mainline/debian stretch nginx" | tee -a /etc/apt/sources.list.d/nginx.list && \
    echo "deb-src http://nginx.org/packages/mainline/debian stretch nginx" | tee -a /etc/apt/sources.list.d/nginx.list && \
    apt-get update -o Dir::Etc::sourcelist="sources.list.d/nginx.list" -o Dir::Etc::sourceparts="-" -o APT::Get::List-Cleanup="0"

# Install main software
RUN apt-get install -y --no-install-recommends \
    nginx

# Default nginx homepage
ADD ./default /usr/share/nginx/html

# Add config and scripts
ADD ./webserver/ /etc/nginx
ADD ./scripts /scripts

EXPOSE 80 443

# Run supervisor to start all services
CMD ["bash","/scripts/start.sh"]