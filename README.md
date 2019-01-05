### Natural Remedy Company

## Nginx
# Environment Set Up

*Nginx*
Create Nginx environment file: _./env/nginx-proxy/.env_ - here is an example of available variables:
```
NGINX_HOST=naturalremedy.portchris
NGINX_WEBROOT=/home/www/naturalremedy
NGINX_PORT=80
VIRTUAL_HOST=naturalremedy.portchris
LETSENCRYPT_EMAIL=chris@portchris.co.uk
LETSENCRYPT_HOST=naturalremedy.portchris
LETSENCRYPT_TEST=true
ENABLE_IPV6=true
SSL_CERTIFICATE_PATH=/etc/nginx/certs/default.crt
SSL_KEY_PATH=/etc/nginx/certs/default.key
SSL_DHPARAM_PATH=/etc/nginx/certs/dhparam.pem
```

# Virtual Hosts
It is important to set up an Nginx template configuration file in ./env/nginx-proxy/webserver/vhost.d in order to create your virtual host and thus bring the site up.
There is an example of one available within ./env/nginx-proxy/webserver/vhost.d/naturalremedy.portchris.template - the name of the .template file takes that from the NINX_HOST environment variable from above.

# Image
This docker image is taken from https://github.com/jwilder/nginx-proxy - see here for more information.

## PHP
# Environment Set Up
Create PHP environment file: _./env/webapp/.env_ - here is an example of available variables:
```
# None as of yet
```

# Magento CLI
n98-magerun.phar is available within this container also.


## MySQL
# Environment Set Up
*MySQL*
Create MySQL environment file: _./env/mysql/.env_ - here is an example of available variables:
```
MYSQL_ROOT_PASSWORD=password
```

## LetsEncrypt
# Environment Set Up
This is the SSL Certification Companion container that will generate the certs.
To use it with original nginx-proxy container you must declare 3 writable volumes from the nginx-proxy container:
```
    /etc/nginx/certs to create/renew Let's Encrypt certificates
    /etc/nginx/vhost.d to change the configuration of vhosts (needed by Let's Encrypt)
    /usr/share/nginx/html to write challenge files.
```

# Image
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