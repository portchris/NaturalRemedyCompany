# Any extra start up scripts here to run on container instantiation
#!/bin/bash

CONF="/etc/nginx/conf.d/naturalremedy.template"
CONF_DEFAULT="/etc/nginx/conf.d/default.conf"
ENV="/etc/nginx/.env"

# Update Nginx config depending on environment files
if [ -f $ENV ]; then
	export $(grep -v '^#' $ENV | xargs)
	
	if [ -z ${NGINX_HOST+x} ]; then
		echo "NGINX_HOST not set!"
		exit 1
	fi

	if [ -z ${NGINX_PORT+x} ]; then
		echo "NGINX_PORT not set!"
		exit 1
	fi

	if [ -z ${NGINX_WEBROOT+x} ]; then
		echo "NGINX_WEBROOT not set!"
		exit 1
	fi

	if [ -f $CONF_DEFAULT ]; then
		rm $CONF_DEFAULT
	fi

	cp $CONF $CONF_DEFAULT
	sed -i -e "s/{NGINX_HOST}/${NGINX_HOST}/g" $CONF_DEFAULT
	sed -i -e "s/{NGINX_PORT}/${NGINX_PORT}/g" $CONF_DEFAULT
	sed -i -e "s/{NGINX_WEBROOT}/${NGINX_WEBROOT}/g" $CONF_DEFAULT
	echo # \n
	cat $CONF_DEFAULT
	echo # \n

	# Certbot LetsEncrypt certificate
	if [[ $NGINX_HOST == *"portchris.co.uk"* ]]; then
		echo "Generating self-signed localhost dev certificate"
		# if [ -f /etc/ssl/private/$NGINX_HOST.key ]; then
		# 	rm /etc/ssl/private/$NGINX_HOST.key
		# fi
		# if [ -f /etc/ssl/certs/$NGINX_HOST.crt ]; then
		# 	rm /etc/ssl/certs/$NGINX_HOST.crt
		# fi
		# openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/$NGINX_HOST.key -out /etc/ssl/certs/$NGINX_HOST.crt \
		# 	-subj "/C=UK/ST=Somerset/L=Taunton/O=Portchris/OU=IT Department/CN=172.17.0.1"
		# openssl dhparam -out /etc/ssl/certs/private/dhparam.pem 2048
	else
		echo "Generating LetsEncrypt certificate for production domain $NGINX_HOST"
		certbot --nginx -d $NGINX_HOST --agree-tos -n -m chris@portchris.co.uk
	fi

	# Restart web server in Docker mode
	nginx -g "daemon off;"
else
	echo "Error: no /etc/nginx/.env environment variables file found!"
	exit 1
fi
