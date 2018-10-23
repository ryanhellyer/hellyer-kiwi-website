#!/bin/bash

# Copy over the client plugin
mkdir -p /var/www/html/wp-content/mu-plugins
cp -R /usr/local/bin/strattic-publish/wordpress-plugin/mu-plugins/* /var/www/html/wp-content/mu-plugins/

# Increase maximum execution time for PHP (so that the API request for all content does not time out)
sed -i 's/max_execution_time = 30/max_execution_time = 900/g' /etc/php/7.0/fpm/php.ini && \
#service php7.0-fpm restart

# Copy over required content
cp /usr/local/bin/suffix /tmp

# Set appropriate file permissions
chown -R www-data:www-data /var/www/html
chown -R www-data:www-data /tmp/*

# Set appropriate file permissions (client plugin is set lower to stop the client editing it and the main plugin folder is higher to allow the .sh files to execute)
chmod 777 /var/www/html/* -R
chmod 644 /var/www/html/wp-content/mu-plugins/strattic.php
chmod 644 /var/www/html/wp-content/mu-plugins/strattic/* -R
chmod 755 /usr/local/bin/strattic-publish/wordpress-plugin/* -R

service nginx start
service mysql start
service php7.0-fpm start
service redis-server start
