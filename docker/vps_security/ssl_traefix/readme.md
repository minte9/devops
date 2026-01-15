## SSL with Traefix


### 📌 Apache vs Traefix

The classic, correct, old-school Apache SSL workes like that:

    Browser → HTTPS → Apache (SSL) → PHP → App

The Apache was responsible for:

    - Key generation (openssl)
    - Certificate storage
    - VirstualHost SSL config
    - Renewal (manual)
    - Port 443 binding
    - App routing

Traefik changes (needed in Docker context):

    Browser → HTTPS → Traefik → HTTP → Apache → Zend

Traefix is the right choise when:

    - App runs in containers
    - You are using Docker Compose
    - You wnat painless SSL
    - You plan future services
    - You wnat clean separation

💡 Important Clarification:

    - Traefix is not replacing Apache
    - Traefix is replacing Apache's SSL responsibility


What we're building

    Internet (HTTPS)
            ↓
    Traefik (SSL, ports 80/443)
            ↓
    Apache + Zend (HTTP only, internal)
            ↓
    MySQL (internal)

SLL with Traefix + Let's Encrypt is the cleanest and safest Docker solution.

    - Automatic SSL (Let's Encrypt)
    - Auto-renew
    - No certbot hacks
    - Apache stays simple
    - Zend unchanged

### 📌 1) DNS (mandatory)

SSL will NOT work without DNS pointing to your VPS.

    example.com      → VPS_IP
    www.example.com  → VPS_IP


## 📌 2) Project Structure

Add Traefix:

    project/
    ├── docker-compose.yml
    ├── traefik/
    │   ├── traefik.yml
    │   └── acme.json
    ├── app/
    │   ├── Dockerfile
    │   ├── vhost.conf
    │   └── src/
    │       └── html/
    ├── db/
    │   └── backup.sql


### 📌 3) Traefik Static Config

traefik/traefik.yml

    entryPoints:
    web:
        address: ":80"
    websecure:
        address: ":443"

    certificatesResolvers:
        letsencrypt:
            acme:
            email: you@example.com
            storage: /acme.json
            httpChallenge:
                entryPoint: web

    providers:
        docker:
            exposedByDefault: false

Important acme.json permissions.

    chmod 600 traefik/acme.json


### 📌 4) Docker compose (THIS IS THE MAGIC)

- Remove all ports: from the web service.  
- Traefik is now the only entry point.  
- For dev/prod separation use `compose overrides`.

docker-compose.yml

    services:
        web:
            build: ./app
            container_name: my-apache-ssl
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


docker-compose.dev.yml

    services:
        traefik:
            image: traefik:v3.0
            container_name: traefik
            ports:
                - "8082:80"
                - "443:443"
            volumes:
                - /var/run/docker.sock:/var/run/docker.sock:ro
                - ./traefik/traefik.yml:/traefik.yml:ro
                - ./traefik/acme.json:/acme.json
            command:
                - "--configFile=/traefik.yml"
            restart: unless-stopped

    web:
        labels:
            - "traefik.enable=true"
            - "traefik.http.routers.zend.rule=Host(`minte9.cloud`,`www.minte9.cloud`)"
            - "traefik.http.routers.zend.entrypoints=websecure"
            - "traefik.http.routers.zend.tls.certresolver=letsencrypt"


### 📌 5) Start Everything

Stop apache if running.

    sudo lsof -i :80
        COMMAND   PID     USER   FD   TYPE DEVICE SIZE/OFF NODE NAME
        apache2  2120     root    4u  IPv6  48651      0t0  TCP *:http (LISTEN)

    sudo systemctl stop apache2
    sudo systemctl disable apache2
    sudo lsof -i :80

Start container.

    #docker compose down
    #docker compose up -d

    docker compose -f docker-compose.yml -f docker-compose.dev.yml down
    docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

If traefix not stopped.

    docker stop traefik
    docker rm traefik
