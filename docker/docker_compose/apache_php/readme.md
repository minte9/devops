## Docker Compose: Apache + PHP


### Install official Compose v2 plugin:

    sudo mkdir -p /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
        https://download.docker.com/linux/ubuntu focal stable" | \
        sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

    sudo apt update
    sudo apt install docker-compose-plugin

    docker compose version
        Docker Compose version v2.35.1


### Folder structure

    apache_php/
    ├── app/
    │   ├── Dockerfile
    │   ├── my-site.conf
    │   └── src/
    │       ├── index.php
    │       └── index.html
    └── docker-compose.yml


### Docker-compose.yml

    version: "3.9"

    services:
        web:
            build:
                context: ./app
            container_name: my-apache-php
            ports:
                - "8081:80"
            volumes:
                - ./app/src:/var/www/html
            restart: unless-stopped

### Start everything (one command)

    docker compose up -d --build
    docker ps
        CONTAINER ID   IMAGE                    COMMAND                  CREATED         STATUS         PORTS                                   NAMES
        c4071f3e0324   project_apache_php-web   "docker-php-entrypoi…"   3 seconds ago   Up 2 seconds   0.0.0.0:8081->80/tcp, :::8081->80/tcp   my-apache-php

What it does:

    - Builds your custom image
    - Starts the container
    - Mounts your code as volume
    - Runs in background

Change PHP code:

    - No rebuild
    - No restart
    - Just refresh the browser

### Stopping everythig

    docker compose down

This:

    - Stop containers
    - Removes containers
    - Keep images & volumes


## DEPLOY

With Docker Compose you deploy source + configuration, not a pre-running container.  
You transfer:

    - docker-compose.yml
    - Dockerfile
    - App source (src/)
    - .env (if used)

Then on the VPS:

    - Docker Compose rebuilds the image
    - Containers are recreated automatically

Transfer files to VPS

    ssh catalin@72.62.152.27
    sudo mkdir -p /srv/docker/apache_php
    sudo chown -R $USER:$USER /srv/docker

    exit
    rsync -avz --delete apache_php/ catalin@72.62.152.27:/srv/docker/apache_php/
        sending incremental file list
        created directory /srv/docker/apache_php
        ./
        docker-compose.yml
        readme_compose.md
        app/
        app/Dockerfile
        app/my-site.conf
        app/src/
        app/src/favicon.ico
        app/src/index.php
        sent 3,061 bytes  received 190 bytes  2,167.33 bytes/sec
        total size is 4,326  speedup is 1.33

    (or)
    scp -r apache_php catalin@72.62.152.27:/srv/docker/
        docker-compose.yml      100%  200     4.5KB/s   00:00    
        readme_compose.md       100% 3018    63.8KB/s   00:00    
        index.php               100%   60     1.3KB/s   00:00    
        favicon.ico             100%  751    16.5KB/s   00:00    
        my-site.conf            100%  297     6.7KB/s   00:00    
        Dockerfile              100%  224     5.0KB/s   00:00  


Start on VPS

    ssh catalin@72.62.152.27
    cd /srv/docker/apache_php/

    docker compose up --build -d
    docker ps
        CONTAINER ID   IMAGE            COMMAND                  CREATED         STATUS         PORTS                                     NAMES
        bfdd90799581   apache_php-web   "docker-php-entrypoi…"   4 seconds ago   Up 3 seconds   0.0.0.0:8081->80/tcp, [::]:8081->80/tcp   my-apache-php

    
Insteed of copying the image and restarting the container, we simple just transfer the files.

    (docker image)
    docker save my-apache:1.0 | gzip | ssh catalin@72.62.152.27 'gunzip | docker load'
    ssh catalin@72.62.152.27
    docker run -d --name apache -p 8081:80 --restart unless-stopped my-apache:1.0

    (docker compose - better)
    scp -r apache_php catalin@72.62.152.27:/srv/docker/