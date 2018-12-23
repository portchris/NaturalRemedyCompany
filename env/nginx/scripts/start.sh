# Any extra start up scripts here to run on container instantiation
#!/bin/bash

CONF="/etc/nginx/vhost.d/naturalremedy.template"
CONF_DEFAULT="/etc/nginx/vhost.d/naturalremedy.conf"
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
	CONF="/etc/nginx/vhost.d/$VIRTUAL_HOST.template"
	CONF_DEFAULT="/etc/nginx/vhost.d/$VIRTUAL_HOST.conf"

	cp $CONF $CONF_DEFAULT
	sed -i -e "s/{VIRTUAL_HOST}/${VIRTUAL_HOST}/g" $CONF_DEFAULT
	sed -i -e "s/{WEBROOT}/${WEBROOT}/g" $CONF_DEFAULT
	echo # \n
	cat $CONF_DEFAULT
	echo # \n


	# Certbot LetsEncrypt certificate
	if [[ $VIRTUAL_HOST == *"portchris"* ]]; then
		echo "Generating self-signed localhost dev certificate"
		# chown root:root -R /etc/nginx/ssl
		# chmod -R 600 /etc/nginx/ssl
		# if [ ! -d "/usr/local/share/ca-certificates/localhost" ]; then
		# 	mkdir /usr/local/share/ca-certificates/localhost
		# fi
		# cp /etc/nginx/ssl/certs/private/$VIRTUAL_HOST.cert /usr/local/share/ca-certificates/localhost/
		# certbot certonly --standalone -d $VIRTUAL_HOST --agree-tos -n -m chris@portchris.co.uk
		# update-ca-certificates
	else
		echo "Generating LetsEncrypt certificate for production domain $VIRTUAL_HOST"
		# certbot --nginx -d $VIRTUAL_HOST --agree-tos -n -m chris@portchris.co.uk
	fi

	# Restart web server in Docker mode
	nginx -g "daemon off;"
else
	echo "Error: no /etc/nginx/.env environment variables file found!"
	exit 1
fi
