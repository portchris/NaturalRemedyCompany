#!/usr/bin/env bash

sudo docker-compose exec mysql mysql -h127.0.1.1 -u -p $@
