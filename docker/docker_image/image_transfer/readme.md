## Transfer image to the VPS

Simple copy the image via SSH

    docker save my-apache:1.0 | gzip | ssh catalin@72.62.152.27 'gunzip | docker load'
        Loaded image: my-apache:1.0

    ssh catalin@72.62.152.27

    docker images my-apache
        IMAGE                ID             DISK USAGE   CONTENT SIZE   EXTRA
        my-apache:1.0        846df55b3f20        250MB          120MB 

Run the Apache container on the VPS

    docker run -d --name apache -p 8081:80 --restart unless-stopped my-apache:1.0

Test from the browser:

    http://minte9.cloud:8080/


### OR: Push to Dockerhub

Push to a registry (to do LATER).
This is what you'll want later with CI/CD + Jenkins.

    docker tag my-apache:latest mydockerhubuser/my-apache:latest
    docker push mydockerhubuser/my-apache:latest

    # On the VPS
    docker pull mydockerhubuser/my-apache:latest


### Docker commands for VPS live

    docker logs apache
    docker stop apache
    docker start apache
    docker rm apache
    docker images
    docker ps -a