echo 'Starting script.';

echo 'Installing Required Packages: PHP, Composer Apache, MySql';

sudo apt-get install php5 mysql-server php5-mysql php5-gd composer apache2 npm -y

echo 'Installing bower'
sudo npm install -g bower

echo 'Creating symbolic link for nodejs /usr/bin/nodejs ~> /usr/bin/node'
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

sudo tee -a  /etc/hosts << EOF
 echo '127.1.1.100   cats-lab.lan # nome associado ao virtual host local de desenvolvimento'
EOF

echo 'Starting git clone'
mkdir $HOME/vhosts
git clone https://github.com/marciodojr/catsSys.git $HOME/vhosts/cats-lab

echo 'Starting Composer packages installation'
cd $HOME/vhosts/cats-lab
COMPOSER_PROCESS_TIMEOUT=2000 composer install

echo 'Starting bower assets installation'
cd $HOME/vhosts/cats-lab/public/
bower install

echo 'Creating local configuration'
tee $HOME/vhosts/cats-lab/config/autoload/local.php << EOF
<?php
/*
* ./config/autoload/local.php
*
* inserir usuario, senha e nome do banco de dados que será utilizado
* localmente
*/
return array(
   'doctrine' => array(
       'connection' => array(
           'orm_default' => array(
               'params' => array(
                   'user'     => 'root',
                   'password' => 'root',
                   'dbname'   => 'catssys',
               ),
           ),
       ),
   ),
);
EOF
cp $HOME/vhosts/vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist $HOME/vhosts/cats-lab/config/autoload/zenddevelopertools.local.php

echo 'Creating database CatsSys'
mysql -u root -p -e 'create database catssys'

echo 'Creating database schema'
php $HOME/vhosts/cats-lab/public/index.php orm:validate-schema
php $HOME/vhosts/cats-lab/public/index.php orm:schema-tool:create

echo 'Importing table contents'
mysql -u root -p catssys <$HOME/vhosts/cats-lab/data/dev-helpers/catssys_data_1.sql
mysql -u root -p catssys <$HOME/vhosts/cats-lab/data/dev-helpers/catssys_data_2.sql

echo 'Creating data directories'
mkdir $HOME/vhosts/cats-lab/data/captcha
mkdir $HOME/vhosts/cats-lab/data/session

echo 'Setting permissions for data directories'
sudo chmod 777 $HOME/vhosts/cats-lab/data/DoctrineORMModule/Proxy
sudo chmod 777 $HOME/vhosts/cats-lab/data/cache
sudo chmod 777 $HOME/vhosts/cats-lab/data/edital
sudo chmod 777 $HOME/vhosts/cats-lab/data/fonts
sudo chmod 777 $HOME/vhosts/cats-lab/data/profile
sudo chmod 777 $HOME/vhosts/cats-lab/data/captcha
sudo chmod 777 $HOME/vhosts/cats-lab/data/session

echo 'Enabling rewrite mode'
sudo a2enmod rewrite

echo 'Enabling cats-lab virtual host'
sudo a2ensite cats-lab.conf

echo 'Restarting Apache server'
sudo service apache2 restart

echo 'Starting browser'
firefox http://cats-lab.lan
