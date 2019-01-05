#!/usr/bin/env bash

docker-compose exec mysql mysql -h127.0.1.1 -p $@
