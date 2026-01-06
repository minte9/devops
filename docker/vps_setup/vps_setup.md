## VPS INITIAL SETUP


### Login

Use ssh to access you VPS:

    ssh root@72.62.152.27

    root@72.62.152.27's password: 
	Welcome to Ubuntu 22.04.5 LTS (GNU/Linux 5.15.0-163-generic x86_64)


### System upgrade

Do this at least weekly, or more often for critical servers.

    apt update
    apt upgrade -y
    reboot

### User

Create a new sudo user and stop using root for daily work.

    adduser myuser
    usermod -aG sudo myuser
		
Then log in as myuser and use sudo instead of staying as root.

	ssh myser@72.62.152.27
	catalin@srv1243868:~$


### SSH Key

On your local machine (if you don’t already have a key):

	ssh-keygen -t ed25519
	    (accept defaults, enter)
	
		Generating public/private ed25519 key pair.
		Enter file in which to save the key (/home/catalin/.ssh/id_ed25519): 
		
- The key is saved in ~/.ssh/authorized_keys
- Keys can be revoked per device.
- If your laptop is stolen, you just remove that key’s line from ~/.ssh/authorized_keys 
- You’re safe, without changing any other login credentials.
		
Then from your local machine:

	ssh-copy-id myuser@72.62.152.27
	
		/usr/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
		/usr/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
		catalin@72.62.152.27's password: 

		Number of key(s) added: 1

Now try logging into the machine, with:   

    ssh catalin@72.62.152.27
    Welcome to Ubuntu 22.04.5 LTS (GNU/Linux 5.15.0-164-generic x86_64)
    catalin@srv1243868:~$

Check to make sure that only the key(s) you wanted were added.
	
    catalin@srv1243868:~$ 
    sudo nano ~/.ssh/authorized_keys


### Disable password login

On the server, edit SSH config:

	sudo nano /etc/ssh/sshd_config

	PasswordAuthentication no
	PermitRootLogin no
	
	sudo systemctl restart ssh
	
Check the modifications:

	sudo sshd -T | grep permitrootlogin
	permitrootlogin no
	
Test from another terminal:

	ssh root@72.62.152.27
		root@72.62.152.27's password: 
		Permission denied, please try again.
	OK!


### Firewall

- Here’s a minimal, safe firewall setup conceptually.
- Allow: SSH (port 22), HTTP (80), HTTPS (443).
- Block: everything else from the internet.
- If you are using Ubuntu on the VPS (most common), the simplest is ufw:

    sudo ufw default deny incoming
    sudo ufw default allow outgoing

Allow SSH before enabling firewall, so you don't lock yourself out.

    sudo ufw allow 22/tcp

If you host websites:

    sudo ufw allow 80/tcp
    sudo ufw allow 443/tcp

Enable firewall:

    sudo ufw enable
    sudo ufw status verbose
    
    --------------
    Status: active
    Logging: on (low)
    Default: deny (incoming), allow (outgoing), deny (routed)
    New profiles: skip

    To                         Action      From
    --                         ------      ----
    22/tcp (OpenSSH)           ALLOW IN    Anywhere                  
    80                         ALLOW IN    Anywhere                  
    443                        ALLOW IN    Anywhere                  
    22/tcp                     ALLOW IN    Anywhere                  
    80/tcp                     ALLOW IN    Anywhere                  
    443/tcp                    ALLOW IN    Anywhere                  
    22/tcp (OpenSSH (v6))      ALLOW IN    Anywhere (v6)             
    80 (v6)                    ALLOW IN    Anywhere (v6)             
    443 (v6)                   ALLOW IN    Anywhere (v6)             
    22/tcp (v6)                ALLOW IN    Anywhere (v6)             
    80/tcp (v6)                ALLOW IN    Anywhere (v6)             
    443/tcp (v6)               ALLOW IN    Anywhere (v6)    