#!/usr/bin/env bash

docker network create proxy-network
docker-compose -f "./docker-compose-ssl.yml" build --build-arg UID=1000 --build-arg GID=1000
docker-compose build --build-arg UID=1000 --build-arg GID=1000