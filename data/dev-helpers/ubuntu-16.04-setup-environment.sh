#!/bin/bash
if [ "$(whoami)" = "root" ]; then
    clear	
	echo 'Do not execute this script as superuser.'
	exit
	else 
	clear
	
fi

echo 'Before installing CatsSys please run "sudo apt-get update && sudo apt-get dist-upgrade" to keep your system up-to-date.'
echo
echo 'Do you wish to update your system (y/n) ?'
read answer
if echo "$answer" | grep -iq "^y" ; then
	echo 'Updating system ...';
	sudo apt-get update && sudo apt-get dist-upgrade
else clear
fi

clear
echo 'Do you wish to install CATSSys (y/n)?'
read answer
if echo "$answer" | grep -iq "^y" ;then
    clear
    echo 'Starting script ...';
    
    # Read user http server preference
	while :
	do
		echo
		echo 'Which http server do you want to install (apache/nginx)?'
		read serverPicked
		if [ "$serverPicked" = "apache" ] || [ "$serverPicked" = "nginx" ]; then
			echo
			echo "Picked $serverPicked server"
			break;
		else 
			echo
			echo "Server not identified. Please try again!"
		fi
	done
	
	# Read user repository
	while :
	do
		echo
        echo "***Warning!***
CATSSys uses a branch named 'develop'!
Make sure that your forked repository has one!
"
		read -p "Please insert the link of your forked repository
(Example: https://github.com/marciodojr/CatsSys.git)
Make sure that your forked repository is updated!: 
" repository;
        echo
		echo "Insert the link again to confirm: "
		read repositoryagain
		if [ "$repository" = "$repositoryagain" ]; then	
			echo
			break;
		else
			echo
			echo "Repositories do not match, please retype"
		fi
	done
	
	# Read mysql user and password
	echo "Remenber your username and password!
	You will be asked again if you don't have MySQL installed yet"
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
else
	echo "Selected 'no'

Exiting...
"

    exit;
fi
echo
echo 'Installing Required Packages: PHP, Composer Apache, MySql, Git';
sudo apt-get install php mysql-server php-mysql php-gd php-apcu php-intl php-dom composer npm git

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

case "$serverPicked" in
apache)
    echo "installing Apache"
    sudo service nginx stop # try to stop any nginx daemon
    sudo apt-get install apache2 libapache2-mod-php

    echo 'Creating apache2 virtual host configuration'
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
    echo 'Changing php.ini max_post_size to 20MB and upload_max_filesize to 15MB'
    sudo sed -i 's/.*post_max_size.*/post_max_size = 20M/' /etc/php/7.0/apache2/php.ini
    sudo sed -i 's/.*upload_max_filesize.*/upload_max_filesize = 15M/' /etc/php/7.0/apache2/php.ini
    ;;
nginx)
    echo "installing Nginx"
    sudo service apache2 stop # try to stop any apache daemon
    sudo apt-get install nginx-full
    sudo tee /etc/nginx/sites-enabled/cats-lab.conf <<EOF
server {
    listen 127.1.1.100:80;
    server_name cats-lab.lan;
    root $HOME/vhosts/cats-lab/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php\$is_args\$args;
    }

    location ~ \.php\$ {
        # Pass the PHP requests to FastCGI server (php-fpm)
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
	fastcgi_param APP_ENV development;
        include fastcgi_params;
    }

    location ~ \.htaccess {
        deny all;
    }

    access_log $HOME/vhosts/cats-lab/access.log;
    error_log $HOME/vhosts/cats-lab/error.log;
}
EOF

    sudo sed -i 's/.*post_max_size.*/post_max_size = 20M/' /etc/php/7.0/fpm/php.ini
    sudo sed -i 's/.*upload_max_filesize.*/upload_max_filesize = 15M/' /etc/php/7.0/fpm/php.ini

    sudo service nginx restart;
    ;;
esac

echo 'Binding domain http://cats-lab.lan to 127.1.1.100'
sudo sed -i '/cats-lab.lan/d' /etc/hosts
sudo tee -a  /etc/hosts << EOF
127.1.1.100   cats-lab.lan # bind domain http://cats-lab.lan to 127.1.1.100
EOF

echo 'Removing previous cats-lab project'
sudo rm -rf $HOME/vhosts/cats-lab

echo 'Starting git clone'
mkdir $HOME/vhosts

git clone $repository $HOME/vhosts/cats-lab

git checkout develop

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

case "$serverPicked" in
apache)
    echo 'Enabling rewrite mode'
    sudo a2enmod rewrite

    echo 'Enabling cats-lab virtual host'
    sudo a2ensite cats-lab.conf
    echo 'Restarting Apache server'
    sudo service apache2 restart
    ;;
nginx)
    echo 'Restarting Nginx server'
    sudo service nginx restart;
    ;;
esac

echo 'Starting browser'
firefox http://cats-lab.lan &
