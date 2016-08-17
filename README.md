# CatsSys - Sistema administrativo para cursos assistenciais

# Instruções de instalação - Para Ubuntu 16.04

Obs: caso queira utilizar a configuração padrão para instalação, é possível utilizar o script [ubuntu-16.04-setup-environment.sh](https://raw.githubusercontent.com/CATSInformatica/CatsSys/master/data/dev-helpers/ubuntu-16.04-setup-environment.sh) e pular diretamente para a [terceira parte](#step-three) deste documento.

**_Importante_**: Lembre-se do usuário e senha utilizados na instalação do `mysql`.

## Primeira Parte

Instalar dependências
Digite ou cole no terminal:

```
sudo apt-get install php mysql-server php-mysql php-gd php-apcu php-intl php-dom composer npm
```

Escolher qual servidor http será utilizado (Escolha apenas um)

Para o Apache instale: `sudo apt-get install Apache2 libapache2-mod-php`
Para o Nginx instale: `sudo apt-get install nginx-full`

Instalar Apcu
Baixe e instale a versão mais nova do apcu [apcu](http://ftp.us.debian.org/debian/pool/main/p/php-apcu-bc).

Instalação do bower. Digite ou cole no terminal: `sudo npm install -g bower`
Provavelmente será necessário criar um link para que o bower funcione corretamente: `sudo ln -s /usr/bin/nodejs /usr/bin/node`

Configuração do virtual host (Apache)
Digite ou cole no terminal: `sudo gedit /etc/apache2/sites-available/cats-lab.conf`
Cole no arquivo de texto o seguinte conteúdo, substituindo `<HOME>` pelo valor retornado pelo comando `echo $HOME`:

```
<VirtualHost 127.1.1.100:80>

        ServerName cats-lab.lan

        ServerAdmin catsinformatica@gmail.com
        DocumentRoot "<HOME>/vhosts/cats-lab/public"

        <Directory "<HOME>/vhosts/cats-lab/public">
                AllowOverride All
                Require all granted
        </Directory>

        SetEnv "APP_ENV" "development"

        ErrorLog <HOME>/vhosts/cats-lab/error.log
        CustomLog <HOME>/vhosts/cats-lab/access.log combined

</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
```

Salve e feche o arquivo de texto.

Configuração do virtual host (Nginx)
Digite ou cole no terminal: `sudo gedit /etc/nginx/sites-enabled/cats-lab.conf`
Cole no arquivo de texto o seguinte conteúdo, substituindo `<HOME>` pelo valor retornado pelo comando `echo $HOME`:

```
server {
    listen 127.1.1.100:80;
    server_name cats-lab.lan;
    root <HOME>/vhosts/cats-lab/public;
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

    access_log <HOME>/vhosts/cats-lab/access.log;
    error_log <HOME>/vhosts/cats-lab/error.log;
}
```

Pelo gerenciador de arquivos ou pelo terminal crie uma pasta chamada vhosts/cats-lab na raiz do diretório home do usuário. digite ou cole no terminal:

```
    cd ~
    mkdir vhosts
    cd vhosts
    mkdir cats-lab
    mkdir cats-lab/public
```

Para que o site possa ser acessado localmente via nome ao invés de um ip,
digite ou cole no terminal: `sudo gedit /etc/hosts`

Adicione ao final do arquivo a linha:
```
127.1.1.100     cats-lab.lan # nome associado ao virtual host local de desenvolvimento
```

Habilitar modo de reescrita no (Apache)
digite ou cole no terminal: `sudo a2enmod rewrite`

Habilitar o site criado (Apache)
digite ou cole no terminal: `sudo a2ensite cats-lab.conf`

Aumentar o tamanho do conteúdo de posts e upload de arquivos

Para Apache
```
sudo sed -i 's/.*post_max_size.*/post_max_size = 20M/' /etc/php/7.0/apache2/php.ini
sudo sed -i 's/.*upload_max_filesize.*/upload_max_filesize = 15M/' /etc/php/7.0/apache2/php.ini
```

Para Nginx
```
sudo sed -i 's/.*post_max_size.*/post_max_size = 20M/' /etc/php/7.0/fpm/php.ini
sudo sed -i 's/.*upload_max_filesize.*/upload_max_filesize = 15M/' /etc/php/7.0/fpm/php.ini
```

Reiniciar o servidor (Apache): `sudo service apache2 restart`
Reiniciar o servidor (Nginx): `sudo service nginx restart`

Testar se o virtual host foi criado com sucesso.

Em `~/vhosts/cats-lab/public` crie um arquivo chamado `index.php` e cole o seguinte conteúdo:

```php
<?php
    phpinfo();
```

No navegador digite http://cats-lab.lan, você deverá ver as configurações da instalação do php

## Segunda parte - Download do projeto e bibliotecas de terceiros

OBS: Caso queria apenas utilizar o projeto no comando `clone` abaixo utilize a url:
https://github.com/CATSInformatica/CatsSys

Se quiser contribuir com desenvolvimento será necessário fazer um [fork](https://help.github.com/articles/fork-a-repo/) e utilizar a url dele.

Para clonar o projeto, primeiramente, na pasta `vhosts`, remova a pasta `cats-lab`. Em seguida, nessa mesma pasta, abra o terminal e digite o comando:

```
    git clone <https://github.com/USER/REPOSITORY.git> cats-lab
```

Instalar as bibliotecas externas

Entre na pasta `cats-lab` e abra o terminal e digite

```
    COMPOSER_PROCESS_TIMEOUT=2000 composer install
```

Todas os pacotes necessários para o projeto serão baixados para a pasta `./../vendor`
Intencionalmente a pasta vendor está configurada para ficar fora do projeto para manter o código de terceiros separado do código produzido.

Além do composer (utilizado para o php), é utilizado um programa semelhante para js e css chamado [bower](http://bower.io/)

Na pasta `cats-lab` abra o terminal e digite: `bower install`

Todas as dependencias de css e js serão instaladas. Para saber quais são as dependências do projeto consulte o arquivo `bower.json` dentro da pasta `cats-lab`.

Criar arquivo local.php em `cats-lab/config/autoload/`

```php
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
                   'user'     => '<usuario>',
                   'password' => '<senha>',
                   'dbname'   => '<banco>',
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
```

Criar um banco de dados no mysql com nome igual ao inserido no arquivo `local.php`.

Toda manipulação de banco de dados feita pelo sistema será por meio de Mapeamento Objeto-Relacional, desse modo, é possível criar as tabelas do banco de dados a partir de objetos em php.

Gravar entidades (criar esquema do banco de dados) no banco de dados a partir de objetos php.

Abra o terminal na pasta `cats-lab` e verifique se o mapeamento está correto:

```
    E1: php public/index.php orm:validate-schema
```

Criar as tabelas do banco de dados (em caso de falha utilize o parâmetro `--force` ao final)

```
    E2: php public/index.php orm:schema-tool:create
```

A medida que novos objetos que representam tabelas do banco de dados vão sendo criados é possível atualizar o esquema do banco. Primeiramente, é preciso utilizar o comando E1 para verificar se o objeto foi criado corretamente (validar o código antes de criar as tabelas) em seguida é utilizado o comando abaixo:

```
    E3: php public/index.php orm:schema-tool:update --force
```

Importar os dados

```
cat $HOME/vhosts/cats-lab/data/dev-helpers/catssys_data_*.sql | mysql -u <usuário> -p<senha> <banco>
```

Dar permissão de leitura e escrita em algumas pastas do projeto

```
sudo chmod 777 $HOME/vhosts/cats-lab/data/DoctrineORMModule/Proxy
sudo chmod 777 $HOME/vhosts/cats-lab/data/cache
sudo chmod 777 $HOME/vhosts/cats-lab/public/docs
sudo chmod 777 $HOME/vhosts/cats-lab/data/fonts
sudo chmod 777 $HOME/vhosts/cats-lab/data/profile
sudo chmod 777 $HOME/vhosts/cats-lab/data/captcha
sudo chmod 777 $HOME/vhosts/cats-lab/data/session
```

## <a name="step-three"></a> Terceira Parte (Recomendado)

Instalar Netbeans IDE: baixe a última versão do Netbeans para PHP https://netbeans.org/downloads/.

Intencionalmente o git foi configurado para não sincronizar alguns arquivos:
* configurações locais
* configurações do projeto.

Sendo assim, é preciso importar um novo projeto no Netbeans. Para importar o projeto no Netbeans siga as instruções abaixo:

```
    File > New Project > (PHP Aplication with Existing Sources) > (Selecionar a pasta clonada do github (cats-lab), escolher a versão 7 do Php)
```

## Quarta Parte

Abra o navegador e digite http://cats-lab.lan/. Será exibida uma página que representa o site. Clique em login e insira as credenciais:

```
    username: fcadmin
    password: 177598230afbg#
```
