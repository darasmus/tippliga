#!/usr/bin/env bash

apt-get update
apt-get dist-upgrade -y

# Force a blank root password for mysql
echo "mysql-server mysql-server/root_password password " | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password " | debconf-set-selections

# Install mysql, nginx, php5-fpm
sudo aptitude install -q -y -f mysql-server mysql-client nginx php5-fpm php5-xdebug php5-mysql

# Install commonly used php packages
sudo aptitude install -q -y -f php5-mysql php5-curl php5-gd php5-intl php-pear php5-imagick php5-json php5-imap php5-mcrypt php5-memcached php5-ming php5-ps php5-pspell php5-recode php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl php5-xcache

#sudo aptitude install -q -y -f phpmyadmin

service nginx stop

update-rc.d nginx remove
rm /etc/init.d/nginx

cp /vagrant/vagrant/etc/nginx.upstart.conf /etc/init/nginx.conf
ln -fs /vagrant/vagrant/etc/nginx.conf /etc/nginx/sites-enabled/default

cp /vagrant/vagrant/xdebug/xdebug.ini /etc/php5/mods-available/xdebug.ini

service php5-fpm restart
service nginx start
