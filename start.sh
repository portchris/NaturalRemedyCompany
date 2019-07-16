#!/usr/bin/env bash

if [ -f ./env/php72/.env ]; then
	source ./env/php72/.env
else 
	echo "Error: no/env/php72/.env found!"
fi

if [ -z ${USER_ID+x} ]; then
	USER_ID=$(id -u)
fi

if [ -z ${GROUP_ID+x} ]; then
	GROUP_ID=$(id -g)
fi

docker-compose up -d --remove-orphans

# install Face & Figure Salon
docker-compose exec -u www nr_php72 npm --prefix /home/www/naturalremedy/src/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/ install

# Build Face & Figure Salon
docker-compose exec -u www nr_php72 npm --prefix /home/www/naturalremedy/src/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/ run build

# Export Face & Figure Salon
docker-compose exec -u www nr_php72 npm --prefix /home/www/naturalremedy/src/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/ run export
