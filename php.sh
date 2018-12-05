#!/usr/bin/env bash

sudo docker-compose exec php72 bash -c 'cd /home/www/projects/magento1/NaturalRemedyCompany; exec "${SHELL:-sh}"'