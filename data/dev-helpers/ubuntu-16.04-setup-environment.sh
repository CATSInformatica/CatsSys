#!/bin/bash

echo 'Before installing CatsSys please run "sudo apt-get update && sudo apt-get dist-upgrade" to keep your system up-to-date.
Do not execute this script as superuser. Do you wish to install (y/n)?'
read answer
if echo "$answer" | grep -iq "^y" ;then
    clear
    echo 'Starting script ...';
else
    exit;
fi

echo 'Installing Required Packages: PHP, Composer Apache, MySql';
sudo apt-get install php mysql-server php-mysql php-gd php-apcu php-intl php-dom composer apache2 npm libapache2-mod-php

# Read mysql user and password
echo -n 'Please insert your mysql user: '
read mysqluser
stty -echo

while :
 do
    echo -n 'Please insert your mysql password: '
    read mysqlpass
    echo
    echo -n 'Please insert your mysql password again: '
    read mysqlpassagain
    echo
    if [ "$mysqlpass" = "$mysqlpassagain" ]; then
        break;
    else
        echo "Passwords do not match, please retype"
    fi
done

stty echo

echo 'Installing php-apcu-bc';

if [ `getconf LONG_BIT` = "64" ]
then
    APCUBC="amd64"
else
    APCUBC="i386"
fi

wget "http://ftp.us.debian.org/debian/pool/main/p/php-apcu-bc/php-apcu-bc_1.0.3-2_$APCUBC.deb" -P "$HOME/Downloads/";
sudo dpkg -i "$HOME/Downloads/php-apcu-bc_1.0.3-2_$APCUBC.deb";
rm "$HOME/Downloads/php-apcu-bc_1.0.3-2_$APCUBC.deb";

echo 'Installing bower';
sudo npm install -g bower

echo 'Creating symbolic link for nodejs /usr/bin/nodejs ~> /usr/bin/node';
sudo ln -s /usr/bin/nodejs /usr/bin/node

echo 'Creating virtual host configuration'
sudo tee /etc/apache2/sites-available/cats-lab.conf << EOF
<VirtualHost 127.1.1.100:80>

        ServerName cats-lab.lan

        ServerAdmin catsinformatica@gmail.com
        DocumentRoot "$HOME/vhosts/cats-lab/public"

        <Directory "$HOME/vhosts/cats-lab/public">
                AllowOverride All
                Require all granted
        </Directory>

        SetEnv "APP_ENV" "development"

        ErrorLog $HOME/vhosts/cats-lab/error.log
        CustomLog $HOME/vhosts/cats-lab/access.log combined

</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
EOF

echo 'Binding domain http://cats-lab.lan to 127.1.1.100'
sudo sed -i '/cats-lab.lan/d' /etc/hosts
sudo tee -a  /etc/hosts << EOF
127.1.1.100   cats-lab.lan # bind domain http://cats-lab.lan to 127.1.1.100
EOF

echo 'Changing php.ini max_post_size to 20MB and upload_max_filesize to 15MB'
sudo sed -i 's/.*post_max_size.*/post_max_size = 20M/' /etc/php/7.0/apache2/php.ini
sudo sed -i 's/.*upload_max_filesize.*/upload_max_filesize = 15M/' /etc/php/7.0/apache2/php.ini

echo 'Removing previous cats-lab project'
sudo rm -rf $HOME/vhosts/cats-lab

echo 'Starting git clone'
mkdir $HOME/vhosts
read -p "Please insert the link of your forked repository
(Example: https://github.com/marciodojr/CatsSys.git):
" repository;
git clone $repository $HOME/vhosts/cats-lab

echo 'Configuring https://github.com/CATSInformatica/CatsSys as a remote for your forked repository'
cd $HOME/vhosts/cats-lab
git remote add upstream https://github.com/CATSInformatica/CatsSys

echo 'Starting Composer packages installation'
cd $HOME/vhosts/cats-lab
COMPOSER_PROCESS_TIMEOUT=2000 composer install

echo 'Starting bower assets installation'
cd $HOME/vhosts/cats-lab
bower install

echo 'Creating local configuration'
tee $HOME/vhosts/cats-lab/config/autoload/local.php > /dev/null <<EOF
<?php
/*
* ./config/autoload/local.php
*
* inserir usuario, senha e nome do banco de dados que será utilizado
* localmente
*/
return [
   'doctrine' => [
       'connection' => [
           'orm_default' => [
               'params' => [
                   'user'     => '$mysqluser',
                   'password' => '$mysqlpass',
                   'dbname'   => 'catssys',
               ],
           ],
       ],
   ],
   'email_config' => [
      'from_recruitment' => 'email@hostname',
      'from_recruitment_name' => '<nome do remetente>',
      'smtp_options' => [
         'host' => 'smtp.gmail.com',
         'connection_class' => 'login',
         'config' => [
            'username' => 'email@hostname',
            'password' => '<colocar a senha do email>',
            'ssl' => 'tls',
         ],
      ],
   ],
];
EOF

cp $HOME/vhosts/vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist $HOME/vhosts/cats-lab/config/autoload/zenddevelopertools.local.php

echo 'Enabling rewrite mode'
sudo a2enmod rewrite

echo 'Enabling cats-lab virtual host'
sudo a2ensite cats-lab.conf

echo 'Restarting Apache server'
sudo service apache2 restart

echo 'Setting permissions for data directories'
sudo chmod 777 $HOME/vhosts/cats-lab/data/DoctrineORMModule/Proxy
sudo chmod 777 $HOME/vhosts/cats-lab/data/cache
sudo chmod 777 $HOME/vhosts/cats-lab/public/docs
sudo chmod 777 $HOME/vhosts/cats-lab/data/fonts
sudo chmod 777 $HOME/vhosts/cats-lab/data/profile
sudo chmod 777 $HOME/vhosts/cats-lab/data/captcha
sudo chmod 777 $HOME/vhosts/cats-lab/data/session

echo 'Creating database CatsSys.'
mysql -u $mysqluser -p$mysqlpass -e 'drop database if exists catssys; create database catssys'

echo 'Creating database schema'
php $HOME/vhosts/cats-lab/public/index.php orm:validate-schema
php $HOME/vhosts/cats-lab/public/index.php orm:schema-tool:create
php $HOME/vhosts/cats-lab/public/index.php orm:generate-proxies

echo 'Importing table contents.'
cat $HOME/vhosts/cats-lab/data/dev-helpers/catssys_data_*.sql | mysql -u $mysqluser -p$mysqlpass catssys

echo 'Starting browser'
firefox http://cats-lab.lan &
