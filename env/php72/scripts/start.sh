#!/bin/bash

# Set Magento file permissions and ownership
SRC=/home/www/naturalremedy/src
chown -R www:www $SRC
find $SRC -type f -exec chmod 600 {} +
find $SRC -type d -exec chmod 600 {} +
find $SRC/var/ -type f -exec chmod 600 {} +
find $SRC/media/ -type f -exec chmod 600 {} +
find $SRC/var/ -type d -exec chmod 700 {} +
find $SRC/media/ -type d -exec chmod 700 {} +
chmod 700 $SRC/includes
chmod 600 $SRC/includes/config.php

# Start CRON service
service cron start

# Start PHP service
php-fpm7.2