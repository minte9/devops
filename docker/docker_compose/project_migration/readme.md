# Project Migration (from shared hosting)

Docker setup must emulate shared hosting:

    Shared hosting                  Docker equivalent
    --------------                  -----------------
    /public_html                    /var/www/html
    Apache + PHP                    php:8.x-apache
    MySQL service                   mysql:8.0 container
    .htaccess                       Apache mod_rewrite
    Config files                    ENV variable
    Database dump                   backup.sql import


### Project Structure

    project/
    ├── docker-compose.yml
    ├── app/
    │   ├── Dockerfile
    │   ├── vhost.conf
    │   └── src/
    │       ├── html/          <-- public document root
    │       ├── Application/
    │       ├── Zend/
    │       └── .htaccess
    ├── db/
    │   └── backup.sql

Important: 

- html/ must remain in the web root, exactly like shared hosting.

### Apache VirtualHost (Zend Needs This)

vhost.conf

    <VirtualHost *:80>
        ServerName localhost
        DocumentRoot /var/www/html/html

        <Directory /var/www/html/html>
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog /proc/self/fd/2
        CustomLog /proc/self/fd/1 combined
    </VirtualHost>

This replaces shared hosting’s hidden Apache config.


### Dockerfile

    FROM php:8.2-apache

    RUN a2enmod rewrite

    RUN docker-php-ext-install pdo pdo_mysql

    COPY vhost.conf /etc/apache2/sites-available/000-default.conf

    WORKDIR /var/www/html

- No source copied
- Uses volume insteed
- Matches hosting behavior

### Docker Compose

    version: "3.9"

    services:
        web:
            build: ./app
            container_name: zend-apache
            ports:
                - "8081:80"
            volumes:
                - ./app/src:/var/www/html
            depends_on:
                - db
            restart: unless-stopped

        db:
            image: mysql:8.0
            container_name: zend-mysql
            environment:
                MYSQL_ROOT_PASSWORD: root
                MYSQL_DATABASE: app_db
                MYSQL_USER: app_user
                MYSQL_PASSWORD: app_pass
            volumes:
                - mysql_data:/var/lib/mysql
                - ./db/backup.sql:/docker-entrypoint-initdb.d/backup.sql
            restart: unless-stopped

    volumes:
        mysql_data:


### Database config

    <?php
    // DB connect
    if (file_exists('/.dockerenv')) {
        // Docker
        define ('_DB_HOST', 'db');
        define ('_DB_DATABASE', 'app_db');
        define ('_DB_USERNAME', 'app_user');
        define ('_DB_PASSWORD', 'app_pass');
    } else {
        // Shared hosting
        define ('_DB_HOST', 'localhost');
        define ('_DB_DATABASE', 'minte9_refresh_v2');
        define ('_DB_USERNAME', 'admin');
        define ('_DB_PASSWORD', 'password');
    }


### Build and Start

    docker compose down -v
    docker compose up -d --build

### Database Debug

Verify that tables exists.   
Run this inside the MySQL container:  

    docker exec -it zend-mysql mysql -uroot -proot app_db

    mysql> SHOW TABLES;

Force a clean DB import:

    docker compose down -v
    docker compose up -d

Test:

    http://localhost:8081/


### Deploy on VPS

First check project size:

    du -sh
    251M

This immediately shows what is huge.

    du -h --max-depth=1 project
    6,7M	./db
    244M	./app
    251M	.

### Dockerignore

Create project/.dockerignore

    .git
    .gitignore
    node_modules
    vendor
    mysql_data
    .idea
    .vscode
    .DS_Store
    *.log


### Deploy with rsync

Use rsync not scp.

    rsync -avz --progress --exclude='.git' --exclude='vendor' refresh.cloud/ catalin@minte9.cloud:/srv/docker/refresh.cloud/

    ssh catalin@72.62.152.27
    cd /srv/docker/refresh.cloud

    chmod -R 777 app/src/Application/cache

    docker compose up --build -d
    [+] Building 1.5s (13/13) FINISHED  

    docker compose ps
    NAME          IMAGE              COMMAND                  SERVICE   CREATED         STATUS         PORTS
    zend-apache   refreshcloud-web   "docker-php-entrypoi…"   web       8 seconds ago   Up 7 seconds   0.0.0.0:8081->80/tcp, [::]:8081->80/tcp
    zend-mysql    mysql:8.0          "docker-entrypoint.s…"   db        8 seconds ago   Up 7 seconds   3306/tcp, 33060/tcp


Test:

    http://your-ip-vps:8081/
    
    OK!



