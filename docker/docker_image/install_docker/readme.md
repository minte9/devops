### Install Docker on the VPS (official method)

SSH into your VPS as your normal user.

    ssh catalin@72.62.152.27

Install required packages.

    sudo apt upate
    sudo apt upgrade -y
    sudo apt install -y ca-certificates curl gnupg lsb-release

Add Docker's official GPG key

    sudo mkdir -p /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

Add Docker repository

    echo \
    "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
    https://download.docker.com/linux/ubuntu \
    $(lsb_release -cs) stable" \
    | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null


Install Docker Engine

    sudo apt update
    sudo apt install -y docker-ce docker-ce-cli containerd.io
        Reading package lists... Done
        Building dependency tree... Done
        Reading state information... Done
        containerd.io is already the newest version (2.2.1-1~ubuntu.22.04~jammy).
        docker-ce-cli is already the newest version (5:29.1.3-1~ubuntu.22.04~jammy).
        docker-ce is already the newest version (5:29.1.3-1~ubuntu.22.04~jammy).
        0 upgraded, 0 newly installed, 0 to remove and 0 not upgraded.
    
    docker --version
        Docker version 29.1.3, build f52814d

    sudo docker run hello-world
        Hello from Docker!
        This message shows that your installation appears to be working correctly.

Allow your user to run Docker without sudo (reccomended).

    sudo usermod -aG docker $USER

    exit
    ssh catalin@72.62.152.27

    docker run hello-world