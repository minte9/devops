## PHP APACHE - Docker Image

What we are building:

    - Apache running in Docker
    - PHP executed by Apache (mod_php)
    - Files served from /htdocs

### Official combined image:

Instead of manually installing PHP, we switch the base image.

    php:8.2-apache

Why this is good:

    - Apache already configured
    - PHP already enabled
    - mod_php already wired
    - less config = fewer bugs


### Project 

New project structure:

    php_apache/
    ├── Dockerfile
    ├── my-site.conf
    └── html/
        ├── index.php
        └── index.html

File to test PHP works (index.php).

    <?php
    phpinfo();

Apache site config (my-site.conf).

    ServerName localhost

    DocumentRoot "/var/www/html"

    <Directory "/var/www/html">

        AllowOverride None
        Require all granted

        <LimitExcept GET POST HEAD>
            Require all denied
        </LimitExcept>

    </Directory>

    DirectoryIndex index.php index.html

    ServerTokens Prod
    ServerSignature Off

Note the document root:

    - php:*-apache uses /var/www/html
    - NOT /usr/local/apache2/htdocs


### Dockerfile

Dockerfile for PHP + Apache.

    FROM php:8.2-apache

    # Enable useful Apache modules
    RUN a2enmod rewrite

    # Add custom Apache config
    COPY my-site.conf /etc/apache2/conf-available/my-site.conf
    RUN a2enconf my-site

    # Copy site files
    COPY html/ /var/www/html/


### Docker Image

Build your PHP-enabled image.

    docker build -t my-apache-php:1.0 .

    docker rm -f apache-test 2>/dev/null

    docker run -d --name apache-test -p 8081:80 my-apache-php:1.0

Open in browser:

    http://localhost:8081

Verify PHP from inside container (optional).

    docker exec -it apache-test php -v
        PHP 8.2.30 (cli) (built: Dec 29 2025 23:32:41) (NTS)



### VPS deployment


Simple copy the image via SSH

    docker save my-apache-php:1.0 | gzip | ssh catalin@72.62.152.27 'gunzip | docker load'
        Loaded image: my-apache-php:1.0

    ssh catalin@72.62.152.27

    docker images my-apache
        IMAGE                ID             DISK USAGE   CONTENT SIZE   EXTRA
        hello-world:latest   d4aaab6242e0       25.9kB         9.52kB        
        my-apache-php:1.0    44336fb2562f       1.04GB          509MB        
        my-apache:1.0        c0ab0e66799d        250MB          120MB    U   

    docker rm -f apache 2>/dev/null

    docker run -d --name apache -p 8081:80 --restart unless-stopped my-apache-php:1.0

Test from the browser:

    http://minte9.cloud:8080/