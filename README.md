# catsSys

    Sistema administrativo do CATS utilizando Zend Framework 2

    Instalar php5, apache2, mysql
    Digite ou cole no terminal: sudo apt-get install php5 mysql-server php5-mysql composer apache2 -y
    OBS: lembre-se do usuário e senha inseridos na instalação do mysql. Eles serão necessários para a manipulação dos bancos de dados.

    Verificar se o apache está funcionando adequadamente

    vá no navegador e digite http://localhost você deverá ver a página padrão de instalação do apache

    Configuração do virtual host
    Digite ou cole no terminal: sudo gedit /etc/apache2/sites-available/cats-lab.conf

    cole no arquivo de texto o seguinte conteúdo

    ###########################################################################
    <VirtualHost 127.1.1.100:80>

            ServerName cats-lab.lan

            ServerAdmin marciojr91@gmail.com
            DocumentRoot "/home/marcio/vhosts/cats-lab/public"

            <Directory "/home/marcio/vhosts/cats-lab/public">
                    AllowOverride All
                    Require all granted
            </Directory>

            SetEnv "APP_ENV" "development"

            ErrorLog /home/marcio/vhosts/cats-lab/error.log
            CustomLog /home/marcio/vhosts/cats-lab/access.log combined

    </VirtualHost>

    # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
    ###########################################################################

    Salve e feche o arquivo de texto

    Pelo gerenciador de arquivos ou pelo terminal crie uma pasta chamada vhosts/cats-lab na raiz do diretório home do usuário (por exemplo: /home/marcio/vhosts/cats-lab)
    digite ou cole no terminal:
	cd ~
	mkdir vhosts
	cd vhosts
	mkdir cats-lab


    para que o site possa ser acessado localmente via nome ao invés de um ip

    digite ou cole no terminal: sudo gedit /etc/hosts

    Adicione ao final do arquivo a linha:
	127.1.1.100	cats-lab.lan # nome associado ao virtual host local de desenvolvimento


    Habilitar modo de reescrita no apache2
    digite ou cole no terminal: sudo a2enmode rewrite

    Habilitar o site criado
    digite ou cole no terminal: sudo a2ensite cats-lab.conf

    Reiniciar o apache: sudo service apache2 restart

    Testar se o virtual host para o cats foi criado com sucesso

    Em ~/vhosts/cats-lab crie uma pasta chamada public e dentro dela um arquivo chamado index.php

    no arquivo index.php cole o seguinte conteúdo

    <?php phpinfo(); ?>

    No navegador digite http://cats-lab.lan, você deverá ver as configurações da instalação do php
    

______________________________

Segunda etapa
______________________________

   Instalar git
	> No terminal digite: sudo apt-get install git

   Instalar Netbeans IDE
	> acesse o link <https://netbeans.org/downloads/> e baixe a última versão do Netbeans para PHP.

    Clonar o projeto do CATS. vá na pasta vhosts e delete a pasta cats-lab, em seguida abra o terminal e digite o comando
    > git clone https://github.com/marciodojr/catsSys.git
    Renomeie a pasta clonada do github para cats-lab

    Instalar as bibliotecas externas
 
    > COMPOSER_PROCESS_TIMEOUT=2000 composer install

    Criar arquivo local.php em ./config/autoload/

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

    Fazer a cópia do arquivo de configurações do Zend Developer Tools

    cp ./vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist ./config/autoload/zenddevelopertools.local.php

    Criar um banco de dados Mysql com o usuário, senha e banco iguais aos valores inseridos no arquivo local.php

    IDE padrão: NETBEANS 8.*

    Para importar o projeto no Netbeans

    File > New Project
    > (PHP Aplication with Existing Sources)
    > (Selecionar a pasta clonada do github, escolher a versão 5.5 do PHP)

    Gravar entidades no banco de dados a partir de objetos PHP
    
    php public/index.php orm:validate-schema
    php public/index.php orm:schema-tool:create
    php public/index.php orm:schema-tool:update --force

    mysqldump --no-create-info -u root -p catssys > catssys_data.sql

    Instalação do bower:
        > sudo apt-get install npm
        > sudo npm install -g bower
        > sudo sudo ln -s /usr/bin/nodejs /usr/bin/node
