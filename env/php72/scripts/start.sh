#!/bin/bash

# Set Magento file permissions and ownership
SRC=/home/www/naturalremedy/src
chown -R www:www $SRC
find $SRC -type f -exec chmod 600 {} +
find $SRC -type d -exec chmod 700 {} +
find $SRC/var/ -type f -exec chmod 600 {} +
find $SRC/media/ -type f -exec chmod 600 {} +
find $SRC/var/ -type d -exec chmod 700 {} +
find $SRC/media/ -type d -exec chmod 700 {} +
chmod 700 $SRC/includes

# NPM setup
chmod 600 $SRC/includes/config.php
if [ ! -d /home/www/.npm ]; then
	mkdir /home/www/.npm
fi
if [ ! -d /home/www/.config ]; then
	mkdir /home/www/.config
fi
if [ -d $SRC/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/node_modules ]; then
	chmod -R 755 $SRC/app/design/frontend/rwd_faceandfigure/default/faceandfiguresalon/node_modules
fi
chown -R www:www /home/www/.npm
chown -R www:www /home/www/.config

# Start CRON service
service cron start

# Start PHP service
php-fpm7.2