#!/usr/bin/env bash

apt-get update

# install mysql 5.7
debconf-set-selections <<< 'mysql-server-5.7 mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server-5.7 mysql-server/root_password_again password root'
apt-get -y install mysql-server-5.7
mysql -uroot -proot -e "CREATE DATABASE easywallet_dev DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

apt-get install -y apache2
apt-get install -y php7.0 php7.0-mysql php7.0-intl php-xml php7.0-mbstring #php5-xdebug

chown -R www-data:www-data /var/www/easywallet

cat << EOF >  /etc/apache2/sites-available/easywallet_dev.conf
<VirtualHost *:80>
    ServerName dev.easywallet.it

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/easywallet/public
    SetEnv "APP_ENV" "development"

    ErrorLog /var/log/apache2/easywallet-error.log
    CustomLog /var/log/apache2/easywallet-access.log combined

    <Directory /var/www/easywallet/public>
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
EOF

a2ensite easywallet_dev
a2enmod rewrite
service apache2 restart

# create swap file 
# https://www.digitalocean.com/community/tutorials/how-to-add-swap-on-ubuntu-14-04
dd if=/dev/zero of=/swapfile bs=1M count=512
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile

if ! grep -q "swapfile" /etc/fstab; then
    echo "/swapfile none swap sw 0 0" >> /etc/fstab
fi