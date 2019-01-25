# Natural Remedy Company

## Nginx
### Environment Set Up

*Nginx*
Create Nginx environment file: _./env/nginx-proxy/.env_ - here is an example of available variables:
```
WEBROOT=/home/www/naturalremedy
VIRTUAL_ROOT=/home/www/naturalremedy
VIRTUAL_HOST=naturalremedy.portchris.co.uk
VIRTUAL_PROTO=fastcgi
VIRTUAL_PORT=443
VIRTUAL_PROTO=https
LETSENCRYPT_EMAIL=chris@portchris.co.uk
LETSENCRYPT_HOST=naturalremedy.portchris
LETSENCRYPT_TEST=true
ENABLE_IPV6=true
```

### Virtual Hosts
It is important to set up an Nginx template configuration file in ./env/nginx-proxy/webserver/vhost.d in order to create your virtual host and thus bring the site up.
There is an example of one available within ./env/nginx-proxy/webserver/vhost.d/naturalremedy.portchris.template - the name of the .template file takes that from the NINX_HOST environment variable from above.

### Image
This docker image is taken from https://github.com/jwilder/nginx-proxy - see here for more information.

## Webapp
This container is responsible for the application and will run PHP 7.2 and Nginx
### Environment Set Up
Create the environment file: _./env/webapp/.env_ - here is an example of available variables:
```
VIRTUAL_HOST=naturalremedy.portchris.co.uk
WEBROOT=/home/www/naturalremedy
VIRTUAL_PROTO=https
VIRTUAL_PORT=443
VIRTUAL_NETWORK=naturalremedy
```

### Magento CLI
n98-magerun.phar is available within this container also.


## MySQL
### Environment Set Up
*MySQL*
Create MySQL environment file: _./env/mysql/.env_ - here is an example of available variables:
```
MYSQL_ROOT_PASSWORD=password
```

## LetsEncrypt
Let's Encrypt is handled by the certbot package installed in the nginx container

### Environment Set Up
To create a certificate, shell into the nginx container and run the following command:
```
certbot certonly -a manual --rsa-key-size 4096 --email example@email.com -d yourdomain.com -d www.yourdomain.com
```
Follow the on-screen prompts to create the acme challenge, ensure these files are available outside the container (docker cp).

Then edit the nginx file listening on port 443 linking to your new SSL certificate:
```
server {

	...
	
	listen 443 ssl http2;
	listen [::]:443 ssl http2;
	ssl_certificate /etc/letsencrypt/live/yourdomain.com/cert.pem;
	ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
	
	include /etc/nginx/global/ssl-params.conf;

	...
}
```

### Image
This docker image is taken from https://github.com/JrCs/docker-letsencrypt-nginx-proxy-companion - See here for more information,

## Docker

Start up environment
```
docker-compose build --build-arg UID=1000 --build-arg GID=1000
docker-compose up -d
```

There are also some useful scripts to help shortcut processes including:
- ./build.sh - Which will build the docker containers and environment
- ./start.sh - Which will start the docker containers and environment
- ./stop.sh - Which will stop the docker containers and environment
- ./magento.sh - Provides access to the n98-magerun.phar CLI 
- ./mysql.sh - Shortcut to MySQL database 
- ./logs.sh - Access output docker logs such as "./logs.sh nginx"
- ./nginx.sh - Shell into the nginx container at the correct directory
- ./php.sh - Shell into the PHP 7.2 container at the correct directory
- ./php.sh - Shell into the PHP 7.2 container at the correct directory