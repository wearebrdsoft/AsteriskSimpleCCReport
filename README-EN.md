This software is a configuration howto and tools to be able to have an asterisk running for a small callcenter with reports, there is no dialer on this application (yet) so the propose is to have reports.

You can use this procedure with your freepbx or other, but be careful with your files (do not overwrite your file)

if you execute this procedure as expected you will have an asterisk configured with a queue to test, you can trunk with other server to receive and make calls

# Pre-requisites 
	* Debian 9 64 bits
	


# Installation
```
apt-get install  asterisk apache2 php-mysql php-gd php-cli php-common libapache2-mod-php git zip php-zip php-xml mysql-server php-mbstring vim unixodbc checkinstall cmake build-essential libssl1.0-dev libmariadb-dev unixodbc-dev curl python-gevent apache2-mod-wsgi
```


## Coping git files
```
cd /usr/src/
git clone https://github.com/wearebrdsoft/AsteriskSimpleCCReport.git
rm /var/www/html/index.html
mv AsteriskSimpleCCReport/* /var/www/html/
mv AsteriskSimpleCCReport/.* /var/www/html/
```

## Installing composer
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


## Configuring apache
```
cd /var/www/html/
cp common/apache/000-default /etc/apache2/sites-available/
a2enmod rewrite
systemctl restart apache2
chown -R www-data.www-data /var/www/html/storage
```

## Creating the database
```
mysql -e "create database asterisk"
```

*Now on mysql shell execute*
```
 mysql> grant all on asterisk.* to asterisk@'localhost' identified by 'brdsoftrocks';
 mysql > flush privileges
```

*Edit the .env file no /var/wwww/html/ setting the values below * 

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asterisk
DB_USERNAME=asterisk
DB_PASSWORD=brdsoftrocks
```
*Change the credentials on /var/www/html/config/database.php*

Now lets create our database
```
cd /var/www/html/
php artisan migrate
```

*Now we need to create our very first user for this execute the commands below*
```
php artisan tinker

$user = new App\User;
$user->name = "Admin";
$user->email = "admin@localhost";
$user->password=bcrypt('admin');
$user->save();
quit
```
*With this command we have our first user able to login and register other users*

### Installing maria-odbc 
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

## Creating asterisk cdr table and triggers
```
mysql -uroot asterisk < common/queue_log.sql
mysql -uroot asterisk < common/cdr.sql
```

# Installing NVM
```
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.34.0/install.sh | bash
bash
nvm install v8
nvm use 8.16.0
cd /var/www/html/
npm install
```

# Qpanel Installation 
```
git clone https://github.com/roramirez/qpanel.git
cd qpanel
apt-get install python-pip
pip install -r requirements.txt
cp samples/config.ini-dist config.ini
```

*Edit config.ini file and perform the changes to use your asterisk manager credentials*
```
pybabel compile -d qpanel/translations/

cd /usr/src/
mv qpanel /opt/
cd /opt/qpanel
pip install virtualenv
./node_modules/bower/bin/bower install --allow-root
virtualenv /opt/qpanel/env/
```

### Configuring Apache
```
mkdir /etc/apache2/run
chmod 777 /etc/apache2/run
systemctl restart apache2
```

# Configuring Asterisk
```
cd /var/www/html
cp -rfp common/asterisk/* /etc/asterisk
```
