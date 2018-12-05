#!/usr/bin/env bash

sudo docker-compose exec --user www php72 n98-magerun --root-dir=/home/www/projects/magento1/NaturalRemedyCompany/src $@
