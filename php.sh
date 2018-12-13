#!/usr/bin/env bash

sudo docker-compose exec -u www php72 bash -c 'cd /home/www/naturalremedy/; exec "${SHELL:-sh}"'