## Natural Remedy Company

# Environment Set Up

*Nginx*
Create Nginx environment file: _./env/nginx/.env_ - available variables:
```
NGINX_HOST=naturalremedy.portchris
NGINX_PORT=80
```

*PHP*
Create PHP environment file: _./env/php72/.env_ - available variables:
```
```

*MySQL*
Create MySQL environment file: _./env/mysql/.env_ - available variables:
```
MYSQL_ROOT_PASSWORD=password
```

*Docker*

Start up environment
```
docker-compose build --build-arg UID=1000 --build-arg GID=1000
docker-compose up -d
```