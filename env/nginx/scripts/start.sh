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
	if [ -z ${SSL_CERTIFICATE_PATH+x} ]; then
		sed -i -e "s/{SSL_CERTIFICATE_PATH}/${SSL_CERTIFICATE_PATH}/g" $CONF_DEFAULT
	fi

	if [ -z ${SSL_KEY_PATH+x} ]; then
		sed -i -e "s/{SSL_KEY_PATH}/${SSL_KEY_PATH}/g" $CONF_DEFAULT
	fi

	if [ -z ${SSL_DHPARAM_PATH+x} ]; then
		sed -i -e "s/{SSL_DHPARAM_PATH}/${SSL_DHPARAM_PATH}/g" $CONF_DEFAULT
	fi
	echo # \n
	cat $CONF_DEFAULT
	echo # \n

	# Certbot LetsEncrypt certificate
	if [[ $NGINX_HOST == *"portchris.co.uk"* ]]; then
		echo "Generating self-signed localhost dev certificate"
		# chown root:root -R /etc/nginx/ssl
		# chmod -R 600 /etc/nginx/ssl
		if [ ! -d "/usr/local/share/ca-certificates/localhost" ]; then
			mkdir /usr/local/share/ca-certificates/localhost
		fi
		cp /etc/nginx/ssl/certs/private/$NGINX_HOST.cert /usr/local/share/ca-certificates/localhost/
		# certbot certonly --standalone -d $NGINX_HOST --agree-tos -n -m chris@portchris.co.uk
		update-ca-certificates
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
