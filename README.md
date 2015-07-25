# catsSys

    Sistema administrativo do CATS utilizando Zend Framework 2

    clonar o projeto
    > git clone https://github.com/marciodojr/catsSys.git

    instalar as bibliotecas externas
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
               // default connection name
               'orm_default' => array(
                   'params' => array(
                       'user'     => '',
                       'password' => '',
                       'dbname'   => '',
                   )
               )
           )
       ),
    );

    Fazer a cópia do arquivo de configurações do Zend Developer Tools
    
    cp ./vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist ./config/autoload/zenddevelopertools.local.php

    IDE padrão: NETBEANS

    Rodar o servidor php local

    > php -S localhost:8000 -t public/ public/index.php

    > acessar página no navegador
    localhost:8000
