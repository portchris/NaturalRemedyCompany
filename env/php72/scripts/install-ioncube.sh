#!/bin/bash

mkdir -p /tmp
curl -s https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz | tar xvzf - -C /tmp
mv /tmp/ioncube/ioncube_loader_lin_7.2.so /usr/lib/php/20160303/ioncube_loader_lin_7.2.so
echo "zend_extension=ioncube_loader_lin_7.2.so" | tee -a /etc/php/7.2/mods-available/ioncube.ini
ln -s /etc/php/7.2/mods-available/ioncube.ini /etc/php/7.2/fpm/conf.d/00-ioncube.ini
ln -s /etc/php/7.2/mods-available/ioncube.ini /etc/php/7.2/cli/conf.d/00-ioncube.ini
rm -rf /tmp/ioncube
