#!/usr/bin/env bash

docker-compose up -d --remove-orphans
docker-compose exec -d nr_php72 bash -c "/scripts/start.sh"