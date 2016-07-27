#!/bin/bash
debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/dbconfig-install boolean true'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/admin-user string root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/admin-pass password root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/app-pass password root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/app-password-confirm password root'

echo "BOOTSTRAP: Updating package indexes"
apt-get update
echo "BOOTSTRAP: Installing packages"
apt-get install -y apache2 mysql-server php5 php5-curl php5-mcrypt php5-mysql phpmyadmin
sudo -u vagrant wget -q https://getcomposer.org/composer.phar -O /home/vagrant/composer.phar

echo "BOOTSTRAP: Configuring apache"
cp /alive-web/vagrant/vhost.conf /etc/apache2/sites-available/000-default.conf

sudo a2enmod rewrite > /dev/null
sudo php5enmod curl > /dev/null
sudo php5enmod mcrypt > /dev/null
service apache2 restart > /dev/null

echo "BOOTSTRAP: Creating database"
mysql --user=root --password=root -e "CREATE DATABASE alive_db CHARACTER SET utf8 COLLATE utf8_general_ci"; 

echo "BOOTSTRAP: Setting up swapfile"
fallocate -l 1G /swapfile # required for composer...
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo -e "/swapfile\tnone\tswap\tsw\t0\t0" >> /etc/fstab

echo "BOOTSTRAP: Setting up laravel"
adduser vagrant www-data
su - vagrant -c "cd /alive-web; php /home/vagrant/composer.phar install"
su - vagrant -c "cd /alive-web; php artisan migrate --package=cartalyst/sentry"
su - vagrant -c "cd /alive-web; php artisan migrate"
su - vagrant -c "cd /alive-web; php artisan db:seed"

echo "BOOTSTRAP: done!"
