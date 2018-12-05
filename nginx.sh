#!/usr/bin/env bash

sudo docker-compose exec nginx bash -c 'cd /etc/nginx; exec "${SHELL:-sh}"'