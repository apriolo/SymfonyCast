# Projeto Symfony Cast 2X

### Docker Config
- PHP
    - Imagem php:7.1.33-apache
    - Rodando na porta 80:80
    - APACHE_DOCUMENT_ROOT
      Apontando para o diretorio /var/www/html/symfony/web, onde se encontram os arquivos index do projeto
    - Build
      context do projeto se encontra no mesmo diretorio do composer: .
    - Volumes
        * o volume do projeto copiado para /var/www/html
        * As configs do vhost usadas para indicar para o apache onde procurar os     arquivos de index estão dentro da pasta /config e são copiados para /etc/apache2/sites-available/000-default.conf
        * As configurações do php.ini ficam na pasta confPHP e são copiadas para /usr/local/etc/php/php.ini
        * Networks
            * Informamos que o container se conectara na rede "connect" para comuniação com outros containers
- MySQL
    - Imagem mysql
    - Para criar o container do MySQL utilizo alguns comandos necessarios ['--character-set-server=utf8', '--collation-server=utf8_general_ci', '--default-authentication-plugin=mysql_native_password']
    - Enviroment com as informações de criação do MySQL que podem ser alteradas de acordo com a preferência, nestas informações passamos um User e Password, alem do Root Password
        * MYSQL_DATABASE: mysqldb
        * MYSQL_USER: mysqluser
        * MYSQL_PASSWORD: mysqlpass
        * MYSQL_ROOT_PASSWORD: 1234
    - A porta utilizada foi a padrão 3306
    - Volumes
        * O volume utilizado foi o padrão copiando os dados de /mysql para dentro do container no diretorio /var/lib/mysql
    - Networks
        * Informamos que o container se conectara na rede "connect" para comuniação com outros containers
## Instalação
Após o download do projeto, é necessario acessar o path do projeto e realizar um build do compose
```sh
docker-compose build
```
O docker vai realizar o build dos containers e eles já estão prontos para subir.
```sh
docker-compose up -d
```
O parametro -d serve para realizar o run no modo quiet.
Para saber se os containers estão sendo executados.
```sh
docker ps
```
Com o comando ps, os containers são apresentados com os seus respectivos nomes e eles são necessarios para acessar os containers utilizo o docker exec passando os parametros necessarios.
%container_name%
```sh
docker exec -ti %container_name% bash
```
# Ferramentas Utilizadas
##### Composer
Gerenciados de dependencias do PHP, o projeto ja conta com um composer.phar, responsavel pelo gerenciamento, o comando install instala todos os components require do arquivo ['composer.json']
```sh
php composer.phar install
```
O comando vai criar a pasta vendor e fazer o download de todos os componentes do projeto. Porem é possivel baixar também com o composer projetos já prontos com o create project
```sh
php composer.phar create-project symfony/framework-standard-edition starwarsevent "2.8.*"
```
Baixamos o esqueleto do projeto exemplo, especificando a versão 2.8.* (* = qualquer versão).Porem não é necessario neste projeto, pois a aplicação já esta toda configurada e instalada através do DockerFile.

## Gerar Bundles
> Definição : Um bundle é semelhante a um plug-in em outro software, mas ainda melhor. A principal diferença é que tudo é um bundle no Symfony, incluindo tanto as funcionalidades do núcleo do framework quanto o código escrito para a sua aplicação. Bundles são cidadãos de primeira classe no Symfony. Isso lhe fornece a flexibilidade para usar os recursos pré-construídos em `bundles de terceiros`_ ou para distribuir os seus próprios bundles. Isso torna mais fácil escolher quais recursos devem ser habilitados em seu aplicativo e otimizá-los da forma que quiser.

Após essa breve definição dos Bundles criamos o nosso primeiro Bundle chamado de EventBundle.
 ```sh
php console/bin generate:bundle
 ```
O comando vai pedir algumas configurações para criar o Bundle, como namespace `Yoda\EventBundle`, e o tipo de routing gerando como `YML` para aprendizagem do curso.
##### Oque o gerador faz
> Primeiro, ele criou um src/Yoda/EventBundlediretório com alguns arquivos de pacote de amostra.
> Em segundo lugar, ele conectou nosso pacote à placa-mãe adicionando uma linha na AppKernelclasse.
> Terceiro, ele adicionou uma linha ao routing.ymlarquivo que importa rotas do pacote. Contenha seu entusiasmo: estamos a cerca de 30 segundos de falar sobre essa parte.

O composer também é responsavel por carregar essas dependencias dentro do projeto, através do Auto Load, todas as classes baixadas podem ser carregadas facilmente através do autoload. Além das classes baixadas é possivel adicionar uma própria classe ao autoload, utilizando a PSR4, dentro do arquivo composer.json, basta adicionar os namespaces e diretorio dos mesmos.Exemplo do projeto
```sh
"autoload": {
        "psr-4": {
            "AppBundle\\": "src/AppBundle",
            "Yoda\\EventBundle\\": "src/Yoda/EventBundle",
            "Yoda\\UserBundle\\": "src/Yoda/UserBundle"
        },
        "classmap": [ "app/AppKernel.php", "app/AppCache.php" ]
    },
```

O gerador de Bundles já cria tudo necessario para o bundle, o arquivo em `app/config/routing.yml` faz a referencia para as rotas criadas dentro da pasta da Bundle criada em `src/Yoda/EventBundle/Resources/config/routing.yml`
 ```sh
 event:
    resource: "@EventBundle/Resources/config/routing.yml"
    prefix:   /
```
A rota `/teste/{name}` foi usada para fazer o primeiro teste, ela se encontra `DefaultController` e o `routing.yml` do Bundle aponta para esta classe
 ```sh
event_homepage:
    path:     /teste/{name}
    defaults: { _controller: EventBundle:Default:index }
```
Após essa passada sob os Controllers, criamos a entidade de Event, utilizando os comandos Doctrine.
 ```sh
php app/console doctrine:generate:entity
```
O comando exige que você passe alguns parametros como nome da entidade, no caso do projeto utilizamos EventBundle:Event, para a entidade ser criada dentro do Bundle de event.E o comando também pede algumas informaçoes sobre os campos que essa entidade vai ter, como exemplo
- name como um campo de string;
- time como um campo datetime;
- location como um campo de string;
- e details como um campo de texto.

O comando vai criar 2 novas classes, a Event.php que é a entidade da classe e o EventRepository que é onde vamos utilizar para fazer algumas functions sobre o repositorio de eventos. Dentro desta Entity já foram escritos os metodos Set and Getters da entidade, porem para usar alguns destes metodos é preciso configurar o BD em `app/config/parameters.yml`, inserindo os seus parametros de acordo.
Após essa configuração inicial do DB o doctrine conta com mais alguns comandos para auxiliar
 ```sh
 O comando create vai criar o database de acordo com os parameters
php app/console doctrine:database:create
```
 ```sh
 o schema encontra todas as entities do projeto e cria suas respectivas tabelas
 utilizando informaçoes de Annotations nas entities
php app/console doctrine:schema:create
```
 ```sh
 Quando o schema precisa sofrer alguma alteração
php app/console doctrine:schema:update --force
```
Com o schema criado ja podemos utilizar os CRUDs, o doctrine tambem disponibiliza um comando para criação dos CRUDs.O comando já cria os cruds e suas respectivas rotas dentro do controller
 ```sh
php app/console doctrine:generate:crud
```
Com os cruds em funcionamento podemos popular nosso banco, e para isso usamos a biblioteca fixtures do symfony, para podermos usar as fixtures ou qualquer outro modulo temos de adicionar ele ao Kernel em `app/AppKernel.php`
 ```sh
// app/AppKernel.php
// ...
$bundles = array(
    // ...
    new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
);
```
Feito isso as fixtures estão prontas para serem usadas `EventBundle/DataFixtures/ORM/LoadEvents.php`, e para serem carregas o doctrine conta com o comando
 ```sh
php app/console doctrine:fixtures:load
```
## Security
> Symfony vem com um componente de segurança que é realmente poderoso. Honestamente, também é muito complexo. Ele pode se conectar a outros sistemas de autenticação - como Facebook ou LDAP - ou carregar informações do usuário de qualquer lugar, como um banco de dados ou até mesmo através de uma API.O chato é que ligar tudo isso pode ser difícil. Mas como você sabe como cada peça funciona, você será capaz de fazer coisas incríveis. Há também um pouco da magia jedi que mostrarei a você mais tarde que torna os sistemas de autenticação personalizados muito mais fáceis.

Como dito na nota acima, o security é uma parte complicada então resumidamente vamos utilizar um pedaço dela para realizar os Logins e Autenticação, para começar precisamos importar o arquivo de security em `config.yml`
 ```sh
# app/config.config.yml
imports:
    # ...
    - { resource: security.yml }
```
E por fim dentro de `security.yml` podemos definir tudo referente a login e autorização, como a rota de login e logout e as regras de acesso chamades de ROLES e suas hierarquias, para acesso de rotas dentro da aplicação, alem de especificar qual nossa entidade vai fazer o trabalho de armazenar os dados do usuario.
No nosso caso criamos uma nova Bundle, chamada UserBundle, com a entity User e seus metodos responsaveis para cadastrar e realizar o login. Dentro desta entidade de usuario usamos uma configuração para encriptografar a senha do usuario chamada bycrypt. Alem de uma interface de usuario que contem alguns metodos com regras de acesso.