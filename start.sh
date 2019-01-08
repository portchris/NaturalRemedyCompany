#!/usr/bin/env bash

docker-compose up -d --remove-orphans
docker-compose exec -d webapp bash -c "/scripts/start-php-fpm.sh"