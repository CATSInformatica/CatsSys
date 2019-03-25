## Primeira Parte - Instruções de instalação

### Dependências básicas

Instalação de dependências de base: Digite ou cole no terminal `sudo apt install nodejs php mysql-client`

### Nodejs

Configurar o npm para instalação global sem sudo. Digite ou cole no terminal:

```bash
    mkdir ~/.npm-global
    npm config set prefix '~/.npm-global'
    echo 'export PATH="$HOME/.npm-global/bin:$PATH"' >> ~/.profile
    source ~/.profile
```

Instalação do bower: Digite ou cole no terminal: `npm install -g bower`

### Instalação do Docker

Instale o Docker e Docker Compose (Incluindo a post instalation step referente a execução sem sudo).

Link para a documentação:
    1. Docker (utilize o método **Install using the repository**): https://docs.docker.com/install/linux/docker-ce/ubuntu/
    2. Docker Compose (utilize o método **Install Compose on Linux systems**): https://docs.docker.com/compose/install/
    3. Post Installation Steps (siga apenas a seção **Manage Docker as a non-root user**): https://docs.docker.com/install/linux/linux-postinstall/

## Segunda parte - Download do projeto e bibliotecas de terceiros

### Clonar o Projeto

OBS: Caso queria apenas utilizar o projeto no comando `clone` abaixo utilize a url:
https://github.com/CATSInformatica/CatsSys

Se quiser contribuir com desenvolvimento será necessário fazer um [fork](https://help.github.com/articles/fork-a-repo/) e utilizar a url dele.

```
    git clone https://github.com/catsinformatica/catssys.git
```
### Gerar arquivo de configuração local

Na pasta catssys abra o terminal e digite:

```
cp config/autoload/local.example.php config/autoload/local.php
```

O arquivo local.php possui configurações locais, por exemplo, senha do banco de dados. Edite caso seja necessário.

### Instalar as bibliotecas de frontend (Bower)

Além do composer (utilizado para o php), é utilizado um programa semelhante para js e css chamado [bower](http://bower.io/)

Na pasta `catssys` abra o terminal e digite: `bower install`

Todas as dependencias de css e js serão instaladas. Para saber quais são as dependências do projeto consulte o arquivo `bower.json` dentro da pasta `catssys`.

### Instalar bibliotecas de backend (Composer)

Entre na pasta `catssys`, abra o terminal e digite: `docker-compose up`

Em outro terminal, ainda na mesma pasta, acesse o container da aplicação

DC1:
```
    docker exec -it catssys-php bash
```

e instale as dependências do php: `composer install`

## Banco de Dados

### DDL

**Importante: Os comandos de DDL E1, E2 e E3 devem ser executandos dentro do container de php (via comando DC1).**

Toda manipulação de banco de dados feita pelo sistema será por meio de Mapeamento Objeto-Relacional, desse modo, é possível criar as tabelas do banco de dados a partir de objetos em php.

Gravar entidades (criar esquema do banco de dados) no banco de dados a partir de objetos php.

Abra o terminal na pasta `catssys` e verifique se o mapeamento está correto:

E1:
```
    php public/index.php orm:validate-schema
```

Em caso de sucesso você verá a mensagem

> [OK] The mapping files are correct.

Criar as tabelas do banco de dados (em caso de falha utilize o parâmetro `--force` ao final)

E2:
```
    php public/index.php orm:schema-tool:create
```

A medida que novos objetos que representam tabelas do banco de dados vão sendo criados é possível atualizar o esquema do banco. Primeiramente, é preciso utilizar o comando E1 para verificar se o objeto foi criado corretamente (validar o código antes de criar as tabelas) em seguida é utilizado o comando abaixo:

E3:
```
    php public/index.php orm:schema-tool:update --force
```

Em caso de sucesso você verá a mensagem:

> [OK] Database schema created successfully!

### DML

**Importante: Os comandos de DML devem ser executandos FORA do container de php (abrindo o terminal na pasta catssys apenas).**

Dentro da pasta `catssys` digite ou cole o comando:

```
cat ./data/dev-helpers/catssys_data_*.sql | mysql -h 127.0.0.1 -u catssys -pcatssys catssys --port=13306
```

## <a name="step-three"></a> Terceira Parte (Recomendado)

Instalar VSCode IDE: baixe a última versão do VSCode: https://code.visualstudio.com/

Instale as extensões (Ctrl + Shift + X):

- Docker
- Apache Conf
- Markdown All in One
- nginx.conf
- PHP Inteliphense
- Prettier - Code Formatter

Intencionalmente o git foi configurado para não sincronizar alguns arquivos:
* configurações locais
* configurações de projeto.

## Quarta Parte

Abra o navegador e digite http://localhost:8080/. Será exibida uma página que representa o site. Clique em login e insira as credenciais:

```
    username: fcadmin
    password: 177598230afbg#
```

Importante: o ambiente de desenvolvimento fica disponível enquanto o comando `docker-compose up` estiver executando. Assim, sempre que quiser desenvolver é necessário acessar a pasta do projeto e executar este comando.