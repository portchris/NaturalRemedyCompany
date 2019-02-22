#!/usr/bin/env bash

docker-compose exec -u root nr_php72 bash -c 'cd /home/www/naturalremedy/; exec "${SHELL:-sh}"'
