#!/usr/bin/env bash

docker-compose -f "./docker-compose-ssl.yml" stop -t0
docker-compose -f "./docker-compose-ssl.yml" rm -f
docker-compose stop -t0
docker-compose rm -f