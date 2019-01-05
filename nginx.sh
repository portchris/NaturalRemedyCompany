#!/usr/bin/env bash

docker-compose exec nginx bash -c 'cd /etc/nginx; exec "${SHELL:-sh}"'