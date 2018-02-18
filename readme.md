# PDO Project
A PHP project using PDO for Simplon Lyon 5th promotion

Instruction :
1. Install composer
`sudo apt install composer`
1. Install php-xml
`sudo apt install php-xml`
1. Install php7.0-mbstring
`sudo apt install php7.0-mbstring`
1. To install dependencies, go to the `default` folder and run
`composer install`
1. To enable twig logs run from the default folder
`chmod 777 -R logs`
1. at project root, run
`docker-compose up`
1. Go to localhost:8080 (phpmyadmin), click on database db, click on import and import the sql file database.sql
