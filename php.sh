#!/usr/bin/env bash

docker-compose exec -u root webapp bash -c 'cd /home/www/naturalremedy/; exec "${SHELL:-sh}"'
