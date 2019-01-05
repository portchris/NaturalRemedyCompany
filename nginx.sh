#!/usr/bin/env bash

docker-compose exec nginx-proxy bash -c 'cd /etc/nginx; exec "${SHELL:-sh}"'
