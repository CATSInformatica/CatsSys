echo 'Starting script.';
echo 'Installing Required Packages: PHP, Composer Apache, MySql';
sudo apt-get install php5 mysql-server php5-mysql php5-gd php-apc php5-apcu composer apache2 npm -y
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

echo 'Binding domain http://cats-lab.lan to 127.1.1.100'
sudo sed -i '/cats-lab.lan/d' /etc/hosts
sudo tee -a  /etc/hosts << EOF
127.1.1.100   cats-lab.lan # bind domain http://cats-lab.lan to 127.1.1.100
EOF

echo 'Changing php.ini max_post_size to 20MB and upload_max_filesize to 15MB'
sudo sed -i 's/.*post_max_size.*/post_max_size = 20M/' /etc/php5/apache2/php.ini
sudo sed -i 's/.*upload_max_filesize.*/upload_max_filesize = 15M/' /etc/php5/apache2/php.ini

echo 'Removing previous cats-lab project'
sudo rm -rf $HOME/vhosts/cats-lab

echo 'Starting git clone'
mkdir $HOME/vhosts
read -p "Please insert the link of your forked repository 
(Example: https://github.com/marciodojr/CatsSys.git):
" repository;
git clone $repository $HOME/vhosts/cats-lab

echo 'Starting Composer packages installation'
cd $HOME/vhosts/cats-lab
COMPOSER_PROCESS_TIMEOUT=2000 composer install

echo 'Starting bower assets installation'
cd $HOME/vhosts/cats-lab
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

echo 'Creating data directories'
mkdir $HOME/vhosts/cats-lab/data/captcha
mkdir $HOME/vhosts/cats-lab/data/session

echo 'Enabling rewrite mode'
sudo a2enmod rewrite

echo 'Enabling cats-lab virtual host'
sudo a2ensite cats-lab.conf

echo 'Restarting Apache server'
sudo service apache2 restart

echo 'Setting permissions for data directories'
sudo chmod 777 $HOME/vhosts/cats-lab/data/DoctrineORMModule/Proxy
sudo chmod 777 $HOME/vhosts/cats-lab/data/cache
sudo chmod 777 $HOME/vhosts/cats-lab/data/edital
sudo chmod 777 $HOME/vhosts/cats-lab/data/fonts
sudo chmod 777 $HOME/vhosts/cats-lab/data/profile
sudo chmod 777 $HOME/vhosts/cats-lab/data/captcha
sudo chmod 777 $HOME/vhosts/cats-lab/data/session
sudo chmod 777 $HOME/vhosts/cats-lab/data/pre-interview

echo 'Creating database CatsSys. Please insert your mysql password: '
mysql -u root -p -e 'drop database if exists catssys; create database catssys'

echo 'Creating database schema'
php $HOME/vhosts/cats-lab/public/index.php orm:validate-schema
php $HOME/vhosts/cats-lab/public/index.php orm:schema-tool:create
php $HOME/vhosts/cats-lab/public/index.php orm:generate-proxies

echo 'Importing table contents. Please Insert your mysql password'
cat $HOME/vhosts/cats-lab/data/dev-helpers/*.sql | mysql -u root -p catssys

echo 'Starting browser'
firefox http://cats-lab.lan &