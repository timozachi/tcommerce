#!/usr/bin/env bash

cd ../

echo "Applying chmod 777 to these folders: "
echo $(find storage -type d)
chmod 777 $(find storage -type d)

composer install

read -p "Environment (prod|dev) [dev]: " environment
if [ -z "$environment" ]; then
  environment="dev"
fi
read -p "Database host [mysql]: " dbhost
if [ -z "$dbhost" ]; then
  dbhost="mysql"
fi
read -p "Database port [3306]: " dbport
if [ -z "$dbport" ]; then
  dbport="3306"
fi
read -p "Database name [tcommerce]: " dbdatabase
if [ -z "$dbdatabase" ]; then
  dbdatabase="tcommerce"
fi
read -p "Database user [root]: " dbuser
if [ -z "$dbuser" ]; then
  dbuser="root"
fi
read -p "Database password [root]: " dbpass
if [ -z "$dbpass" ]; then
  dbpass="root"
fi
