FROM convertdigital/base-magento1:1.9

# Uncomment where required

# Add cron jobs
# ADD ./env/common/config/cron /etc/cron.d

# Add common configuration
# ADD ./env/common/config/php-fpm /etc/php/5.6/fpm
# ADD ./env/common/config/nginx /etc/nginx

# Add production configuration
# ADD ./env/production/config/php-fpm /etc/php/5.6/fpm
# ADD ./env/production/config/nginx /etc/nginx

# Add scripts
# ADD ./env/production/scripts /scripts

# Add ioncube
# ADD ./env/common/binaries/ioncube_loader_lin_5.6.so /usr/lib/php/20131226/
# ADD ./env/common/config/php-fpm/mods-available /etc/php/5.6/mods-available
# RUN ln -s /etc/php/5.6/mods-available/ioncube.ini /etc/php/5.6/fpm/conf.d/0-ioncube.ini && \
#    ln -s /etc/php/5.6/mods-available/ioncube.ini /etc/php/5.6/cli/conf.d/0-ioncube.ini

# Add source
ADD ./src /var/www/src

