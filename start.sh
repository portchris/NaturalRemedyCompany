#!/usr/bin/env bash

docker-compose -f "./docker-compose-ssl.yml" up -d --remove-orphans
docker-compose up -d
docker-compose exec -d webapp bash -c "/scripts/start-php-fpm.sh"