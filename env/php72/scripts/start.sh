#!/bin/bash

chown -R www:www /home/www/naturalremedy

service cron start

php-fpm7.2