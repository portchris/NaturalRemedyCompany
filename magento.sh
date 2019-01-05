#!/usr/bin/env bash

docker-compose exec --user www webapp n98-magerun --root-dir=/home/www/naturalremedy/src $@
