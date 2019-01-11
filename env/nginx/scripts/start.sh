# Any extra start up scripts here to run on container instantiation
#!/bin/bash

CONF="/etc/nginx/conf.d/naturalremedy.co.uk.template"
CONF_DEFAULT="/etc/nginx/conf.d/default.conf"
ENV="/etc/nginx/.env"

# Update Nginx config depending on environment files
if [ -f $ENV ]; then
	export $(grep -v '^#' $ENV | xargs)
	
	if [ -z ${VIRTUAL_HOST+x} ]; then
		echo "VIRTUAL_HOST not set!"
		exit 1
	fi

	if [ -z ${WEBROOT+x} ]; then
		echo "WEBROOT not set!"
		exit 1
	fi

	if [ -f $CONF_DEFAULT ]; then
		rm $CONF_DEFAULT
	fi

	# Update CONF to match current domain env
	if [ ! -f $CONF ]; then
		CONF="/etc/nginx/conf.d/$VIRTUAL_HOST.template"
		# CONF_DEFAULT="/etc/nginx/vhost.d/$VIRTUAL_HOST.conf"
	fi

	cp $CONF $CONF_DEFAULT
	sed -i -e "s/{VIRTUAL_HOST}/${VIRTUAL_HOST}/g" $CONF_DEFAULT
	sed -i -e "s/{WEBROOT}/${WEBROOT}/g" $CONF_DEFAULT
	echo # \n
	cat $CONF_DEFAULT
	echo # \n

	# Restart web server in Docker mode
	nginx -g "daemon off;"
else
	echo "Error: no /etc/nginx/.env environment variables file found!"
	exit 1
fi
