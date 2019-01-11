#!/usr/bin/env bash

docker-compose exec nr_nginx bash -c 'cd /etc/nginx; exec "${SHELL:-sh}"'
