FROM mysql:5.7

# Build arguments
ARG UID=1000
ARG GID=1000

# Replace mysql user with our uid/gid
RUN userdel mysql && \
	groupadd -g ${GID} mysql && \
    useradd -u ${UID} -g ${GID} -s /bin/bash mysql && \
	chown -R mysql:mysql /var/lib/mysql && \
	chmod -R 755 /var/lib/mysql/
