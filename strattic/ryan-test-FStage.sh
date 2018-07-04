#!/bin/bash
mkdir -p /var/www/html/wp-content/mu-plugins
cp /usr/local/bin/strattic-wordpress-client/* /var/www/html/wp-content/mu-plugins/ -R

cp /usr/local/bin/fuse.min.js /tmp
cp /usr/local/bin/search.js /tmp
cp /usr/local/bin/search.html /tmp
cp /usr/local/bin/suffix /tmp

chown -R www-data:www-data /var/www/html
chown -R www-data:www-data /tmp/*
