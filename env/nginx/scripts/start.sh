# Any extra start up scripts here to run on container instantiation
#!/bin/bash

CONF="/etc/nginx/conf.d/naturalremedy.template"
CONF_DEFAULT="/etc/nginx/conf.d/default.conf"
ENV="/etc/nginx/.env"

# Update Nginx config depending on environment files
if [ -f $ENV ]; then
	export $(grep -v '^#' $ENV | xargs)
	# envsubst < /etc/nginx/conf.d/naturalremedy.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'
	if [ -z ${NGINX_HOST+x} ]; then
		echo "NGINX_HOST not set!"
		exit 1
	fi

	if [ -z ${NGINX_PORT+x} ]; then
		echo "NGINX_PORT not set!"
		exit 1
	fi

	cp $CONF $CONF_DEFAULT
	sed -i -e "s/{NGINX_HOST}/${NGINX_HOST}/g" $CONF_DEFAULT
	sed -i -e "s/{NGINX_PORT}/${NGINX_PORT}/g" $CONF_DEFAULT
	echo # \n
	cat $CONF_DEFAULT
	echo # \n
	nginx -t
	service nginx restart
else
	echo "Error: no /etc/nginx/.env environment variables file found!"
	exit 1
fi
