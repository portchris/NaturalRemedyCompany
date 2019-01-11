#!/usr/bin/env bash

docker-compose exec -u root nr_php bash -c 'cd /home/www/naturalremedy/; exec "${SHELL:-sh}"'
