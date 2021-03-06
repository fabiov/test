#!/usr/bin/env bash

# This setup is valid for: 
#   - Ubuntu 16.04 LTS Xenial Xerus 
#   - Ubuntu 17.04 Zesty Zapus

apt update
apt updrade

# install mysql 5.7
debconf-set-selections <<< 'mysql-server-5.7 mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server-5.7 mysql-server/root_password_again password root'
apt-get -y install mysql-server-5.7
mysql -uroot -proot -e "CREATE DATABASE moneylog DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

apt install -y git-core
apt install -y unzip
apt install -y apache2
apt install -y libapache2-mod-php7.0 php7.0 php7.0-mysql php7.0-intl php-xml php7.0-mbstring

mkdir /var/www/moneylog

chown -R www-data:www-data /var/www/moneylog

cat << EOF >  /etc/apache2/sites-available/moneylog.conf
<VirtualHost *:80>
    ServerName moneylog.it

    ServerAdmin info@moneylog.it
    DocumentRoot /var/www/moneylog/public

    ErrorLog /var/log/apache2/moneylog-error.log
    CustomLog /var/log/apache2/moneylog-access.log combined

    <Directory /var/www/moneylog/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

a2ensite moneylog
a2enmod rewrite
service apache2 restart
usermod -a -G www-data ubuntu

# create swap file 
# https://www.digitalocean.com/community/tutorials/how-to-add-swap-on-ubuntu-14-04
dd if=/dev/zero of=/swapfile bs=1M count=512
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile

if ! grep -q "swapfile" /etc/fstab; then
    echo "/swapfile none swap sw 0 0" >> /etc/fstab
fi

###############################
# FOR DEVELOPMENT ENVIRONMENT #
###############################

#apt-get install php-xdebug

#cat << EOF >> /etc/php/7.0/apache2/php.ini

# Added for xdebug
#xdebug.remote_enable=1
#xdebug.remote_host=10.0.3.1
#xdebug.remote_port=9000

#EOF
