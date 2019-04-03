#!/usr/bin/env bash

docker-compose exec -u root nr_php72 npm --prefix /home/www/naturalremedy/src/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/ $@
