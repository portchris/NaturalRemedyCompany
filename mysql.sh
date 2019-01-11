#!/usr/bin/env bash


if [ -f ./env/mysql/.env ]; then
	source ./env/mysql/.env
	cat ./env/mysql/.env
else 
	echo "Error: no data/env found!"
fi

if [ ! -n "$MYSQL_DB" ]; then
	echo "Error: No MYSQL_DB variable set in ./env/mysql/.env"
	exit 1
fi

if [ ! -n "$MYSQL_ROOT_PASSWORD" ]; then
	echo "Error: No MYSQL_ROOT_PASSWORD variable set in ./env/mysql/.env"
	exit 1
fi

docker-compose exec nr_mysql mysql -h127.0.1.1 -p$MYSQL_ROOT_PASSWORD $MYSQL_DB $@
