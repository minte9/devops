## Official Apache Image / httpd:2.4

- A minimal, clean, production-ready Apache + Docker.
- Make sure you have Docker installed.  
    docker --version

### Run Apache using the official image

Start the docker process:

    docker run -d --name apache-test -p 8081:80 httpd:2.4
    docker ps
        CONTAINER ID   IMAGE       COMMAND              CREATED         STATUS         PORTS                                   NAMES
        e58dcae66624   httpd:2.4   "httpd-foreground"   4 seconds ago   Up 3 seconds   0.0.0.0:8081->80/tcp, :::8081->80/tcp   apache-test
    
Test:

    curl -I http://localhost:8081
    HHTP/:1.1 200 OK

    http://localhost:8081
    It works!

Stop, remove:

    docker stop apache-test
    docker rm apache-test


### Docker Image

A Docker Image is a read-only blueprint (like a class in programming).   
Images do nothing by themselves.   

    docker build -t my-apache:1.0 .

    This:
    - Reads you Dockerfile
    - Creates an image
    - Stores it in Docker's image cache


### Docker Container

A container is a running (or stopped) instance of an image (like an object from a class).  

    docker run -d --name web1 my-apache:1.0

    This:
    - Creates a container from the image
    - Starts Apache
    - Exposes ports
    - Uses memory & CPU

## Custom Apache Image / my-apache:1.0

### Directory structure

    apache/
    ├── Dockerfile
    ├── my-site.conf
    └── html/
        └── index.html

### Dockerfile

    FROM httpd:2.4

    # Copy custom Apache config
    COPY my-site.conf /usr/local/apache2/conf/extra/my-site.conf
    RUN echo "Include conf/extra/my-site.conf" >> /usr/local/apache2/conf/httpd.conf

    # Copy site files
    COPY html/ /usr/local/apache2/htdocs/

- This adds to the default Apache config from the base image.
- It means copy the my conf file into the image at: /usr/local/apache2/conf/extra/my-site.conf


### Build your image

    docker build -t my-apache:1.0 .
    docker image prune -f
    docker image ls
        REPOSITORY            TAG        IMAGE ID       CREATED          SIZE
        my-apache             1.0        7db1b52872ac   24 seconds ago   117MB
        httpd                 2.4        95708e927277   6 days ago       117MB
        my-flask-app          latest     e81c5af3ec77   2 years ago      138MB

### Run your image

    docker stop apache-test
    docker rm apache-test

    docker run -d --name apache-test -p 8081:80 my-apache:1.0

    docker ps
        CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
    docker ps -a
        CONTAINER ID   IMAGE           COMMAND              CREATED         STATUS                     PORTS     NAMES
        c0025961533f   my-apache:1.0   "httpd-foreground"   2 minutes ago   Exited (1) 2 minutes ago             apache-test
    docker logs apache-test
        AH00526: Syntax error on line 14 of /usr/local/apache2/conf/httpd.conf:
        Invalid command 'Require', perhaps misspelled or defined by a module not included in the server configuration

- The -d Docker flag stand for `detached mode`.
- Run the container in the `background` (terminal is free).

###  Dangling images

Old layers left behind after rebuilds or retags.
This deletes only images with no repository and no tag:

    docker image prune
    docker imge prune -f

Remove all stopped containers:

    docker ps -a
    docker container prune


### Rebuild and run cleanly

    docker rm -f apache-test
    docker build -t my-apache:1.0 .
    docker run -d --name apache-test -p 8081:80 my-apache:1.0
    docker ps
        CONTAINER ID   IMAGE           COMMAND              CREATED         STATUS        PORTS                                   NAMES
        185acec7101f   my-apache:1.0   "httpd-foreground"   2 seconds ago   Up 1 second   0.0.0.0:8081->80/tcp, :::8081->80/tcp   apache-test


### Docker Compose (to do later)

Compose automatically:

- Rebuilds the image
- Stops old containers
- Recreate new ones