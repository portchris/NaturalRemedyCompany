#!/usr/bin/env bash

docker-compose exec --user www php72 n98-magerun --root-dir=/home/www/naturalremedy/src $@
