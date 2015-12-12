# catsSys

    Sistema administrativo do CATS utilizando Zend Framework 2

    Clonar o projeto
    > git clone https://github.com/marciodojr/catsSys.git

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
    php doctrine orm:schema-tool:update
