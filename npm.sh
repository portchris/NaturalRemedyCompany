#!/usr/bin/env bash

docker-compose exec -u www nr_php72 npm --prefix /home/www/naturalremedy/src/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/ $@
