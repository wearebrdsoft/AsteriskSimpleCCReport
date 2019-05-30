Este projeto é um howto de configuração e algumas ferramentas para permitir que você possa ter uma pequena operação de atendimento rodando com asterisk a qual você possa ter relatorios de produtividade, não tem nenhum tipo de discador neste projeto  o único proposito é a geração de relatórios.

Você pode utilizar este procedimento com freepbx ou outro sistema mas tome cuidado para não sobrescrever arquivos, nós não nos responsabilizamos por nenhuma "cagada".

Se você executar este procedimento por completo você terá um asterisk rodando com uma fila de teste e você pode criar um tronco para receber e fazer chamadas.

# TODO

	* Detalhes de configuracao para FreePBX

# Pre-requisitos
	* Debian 9 64 bits
	


# Instalação
```
apt-get install  asterisk apache2 php-mysql php-gd php-cli php-common libapache2-mod-php git zip php-zip php-xml mysql-server php-mbstring vim unixodbc checkinstall cmake build-essential libssl1.0-dev libmariadb-dev unixodbc-dev curl python-gevent apache2-mod-wsgi
```


## Clonando o projeto 
```
cd /usr/src/
git clone https://mtesliuk@bitbucket.org/brdsoft/asteriskcallcenterreport.git
rm /var/www/html/index.html
mv asteriskcallcenterreport/* /var/www/html/
mv asteriskcallcenterreport/.* /var/www/html/
```

## Instalando o Composer
```
cd /root

EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
then
    >&2 echo 'ERROR: Invalid installer signature'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
RESULT=$?
rm composer-setup.php


cd /var/www/html/
php /root/composer.phar update
```


## Configurando o apache
```
cd /var/www/html/
cp common/apache/000-default /etc/apache2/sites-available/
a2enmod rewrite
systemctl restart apache2
chown -R www-data.www-data /var/www/html/storage
```

## Criando o banco de dados
```
mysql -e "create database asterisk"
```

*No shell do mysql execute os comandos abaixo*
```
 mysql> grant all on asterisk.* to asterisk@'localhost' identified by 'brdsoftrocks';
 mysql > flush privileges
```

*Edite o arquivo .env em /var/www/html e defina os valores abaixo*

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asterisk
DB_USERNAME=asterisk
DB_PASSWORD=brdsoftrocks
```
*Altere as credenciais em /var/www/html/config/database.php*

Agora vamos criar nossas tabelas iniciais
```
cd /var/www/html/
php artisan migrate
```

*Execute o comando abaixo para criar o primeiro usuario da aplicação*
```
php artisan tinker

$user = new App\User;
$user->name = "Admin";
$user->email = "admin@localhost";
$user->password=bcrypt('admin');
$user->save();
quit
```
Com o usuario criado seremos capazes de criar outros usuarios

### Instalando o conector maria-odbc 
```
apt-get install software-properties-common dirmngr
apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xF1656F24C74CD1D8
cd /usr/src/
git clone https://github.com/MariaDB/mariadb-connector-odbc.git
cd mariadb-connector-odbc
git checkout tags/3.0.1
cmake -G "Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/local -LH
make
echo "MariaDB ODBC client library" | tee description-pak
checkinstall --nodoc --pkgname "mariadb-connector-client-library" --pkgversion "3.0.1" --provides "mariadb-connector-client-library" --requires "libssl1.0.2" --requires "mariadb-server" --maintainer "milosz@localhost" --replaces none --conflicts none --install=no -y
dpkg -i mariadb-connector-client-library_3.0.1-1_amd64.deb
```


```
cat << EOF |  tee /etc/odbc.ini
[asterisk]
Description         = MariaDB localdb
Driver              = MariaDB
Database            = asterisk
Server              = 127.0.0.1
Uid                 = asterisk
Password            = brdsoftrocks
Port                = 3306
EOF
```

```
cat << EOF |  tee /etc/odbcinst.ini
[MariaDB]
Driver      = /usr/local/lib/libmaodbc.so
Description = MariaDB ODBC Connector
EOF
```

## Criando as tabelas e triggers para o asterisk
```
mysql -uroot asterisk < common/queue_log.sql
mysql -uroot asterisk < common/cdr.sql
```

# Instalando o NVM 
```
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.34.0/install.sh | bash
bash
nvm install v8
nvm use 8.16.0
cd /var/www/html/
npm install
```

# Instalacao do Qpanel
```
git clone https://github.com/roramirez/qpanel.git
cd qpanel
apt-get install python-pip
pip install -r requirements.txt
cp samples/config.ini-dist config.ini
```

*Edite o arquivo config.ini e altere para corresponder com as credenciais do manager de seu asterisk se for seguir este tutorial por completo utilize usuario admin e senha brdsoftrocks*
```
pybabel compile -d qpanel/translations/

cd /usr/src/
mv qpanel /opt/
cd /opt/qpanel
pip install virtualenv
./node_modules/bower/bin/bower install --allow-root
virtualenv /opt/qpanel/env/
```

### Finalizacao apache 
```
mkdir /etc/apache2/run
chmod 777 /etc/apache2/run
systemctl restart apache2
```

# Configurando o asterisk
Estes sao os arquivos base de exemplo que fornecemos junto com a configuracao apenas execute se você estiver utilizando um sistema limpo, se for um freepbx você irá sobrescrever arquivos importantes *entao cuidado*
```
cd /var/www/html
cp -rfp common/asterisk/* /etc/asterisk
```
