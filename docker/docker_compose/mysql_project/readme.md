# Mysql with Docker

- You do NOT need to install MySQL on the server anymore.
- MySQL will run inside its own container, completely isolated.

Your VPS will only have:

- Docker
- Docker Compose

### 📌 Project Structure

    mysql_project/
    ├── docker-compose.yml
    ├── app/
    │   ├── Dockerfile
    │   ├── my-site.conf
    │   └── html/
    │       ├── index.php
    │       └── db_test.php

### 📌 Dockerfile

    FROM php:8.2-apache

    # Enable Apache modules
    RUN a2enmod rewrite

    # Install PHP extensions needed for MySQL
    RUN docker-php-ext-install pdo pdo_mysql

    # Apache site config
    COPY my-site.conf /etc/apache2/conf-available/my-site.conf
    RUN a2enconf my-site

    # Copy site files
    COPY src/ /var/www/html/


### 📌 Docker Compose

Update docker-compose.yml

    version: "3.9"

    services:
    web:
        build: context: ./app
        container_name: my-apache-php
        ports:
            - "8081:80"
        volumes:
            - ./app/src:/var/www/html
        depends_on:
            - db
        restart: unless-stopped
    
    db:
        image: mysql:8.0
        container_name: mysql-db
        environment:
            MYSQL_ROOT_PASSWORD: rootpassword
            MYSQL_DATABASE: app_db
            MYSQL_USER: app_user
            MYSQL_PASSWORD: app_pass
        volumes:
            - mysql_data:/var/lib/mysql
        restart: unless-stopped

    volumes:
        mysql_data:


What this does:

- Add official MySQL image      | mysql:8.0
- Auto creates DB & user        | MYSQL_*
- Persistent DB storage         | mysql_data
- Start MySQL before Apach      | depends_on
- MySQL not exposed publicly    | no ports for DB

🔒 MySQL is only accessible inside Docker network


# Start everything

    docker compose up -d --build

    docker compose ps
        NAME            IMAGE               COMMAND                  SERVICE   CREATED              STATUS          PORTS
        my-apache-php   mysql_project-web   "docker-php-entrypoi…"   web       26 seconds ago       Up 23 seconds   0.0.0.0:8081->80/tcp, [::]:8081->80/tcp
        mysql-db        mysql:8.0           "docker-entrypoint.s…"   db        About a minute ago   Up 23 seconds   3306/tcp, 33060/tcp

    docker ps
        CONTAINER ID   IMAGE               COMMAND                  CREATED              STATUS              PORTS                                   NAMES
        aa84c1c1f9b1   mysql_project-web   "docker-php-entrypoi…"   About a minute ago   Up About a minute   0.0.0.0:8081->80/tcp, :::8081->80/tcp   my-apache-php
        85f70f90e144   mysql:8.0           "docker-entrypoint.s…"   2 minutes ago        Up About a minute   3306/tcp, 33060/tcp                     mysql-db

    docker logs mysql-db
        2026-01-06 14:28:35+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.0.44-1.el9 started.
        2026-01-06 14:28:35+00:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
        2026-01-06 14:28:35+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.0.44-1.el9 started.
        2026-01-06 14:28:35+00:00 [Note] [Entrypoint]: Initializing database files
        ...

### 📌 Test database connection

Create app/src/db_test.php

    <?php
    $host = 'db';
    $db   = 'app_db';
    $user = 'app_user';
    $pass = 'app_pass';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass
        );
        echo "✅ Connected to MySQL successfully!";
    } catch (PDOException $e) {
        echo "❌ Connection failed: " . $e->getMessage();
    }

Open:

    http://localhost:8081/db_test.php

    ✅ Connected to MySQL successfully!
