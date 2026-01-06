## Hardening (low effort, high value)


### Logs

- Server is safe at the moment.
- We did several things exactly right.

        docker logs apache
        86.120.249.197 - - [05/Jan/2026:11:16:53 +0000] "GET / HTTP/1.1" 304 -
        86.120.249.197 - - [05/Jan/2026:11:16:58 +0000] "GET / HTTP/1.1" 200 9
        86.120.249.197 - - [05/Jan/2026:11:16:58 +0000] "GET /favicon.ico HTTP/1.1" 404 236
        17.241.219.214 - - [05/Jan/2026:11:20:49 +0000] "GET /robots.txt HTTP/1.1" 404 236
        17.241.219.214 - - [05/Jan/2026:11:20:49 +0000] "GET / HTTP/1.1" 200 9
        104.23.221.178 - - [05/Jan/2026:11:36:04 +0000] "GET /wp-admin/setup-config.php HTTP/1.1" 404 236
        104.23.221.179 - - [05/Jan/2026:11:38:39 +0000] "GET /wordpress/wp-admin/setup-config.php HTTP/1.1" 404 236
        46.226.162.179 - - [05/Jan/2026:11:39:58 +0000] "POST / HTTP/1.1" 200 9
        46.226.162.179 - - [05/Jan/2026:11:53:18 +0000] "POST / HTTP/1.1" 200 9
        104.23.223.69 - - [05/Jan/2026:11:55:10 +0000] "GET /wordpress/wp-admin/setup-config.php HTTP/1.1" 404 236
        104.23.223.69 - - [05/Jan/2026:11:55:24 +0000] "GET /wp-admin/setup-config.php HTTP/1.1" 404 236

        - SSH key-only
        - Root login disabled
        - UFW enabled
        - Minimal Apache container
        - No PHP, no CMS, no uploads
        - No secrets exposed

- That’s already better than 80% of VPSes online.


### Hide Apache version

Hide Apache version (inside container), in apache config:

    ServerTokens Prod
    ServerSignature Off

### Rate limit HTTP

Very effective against noisy bots.

    sudo ufw limit 80/tcp

By default, ufw limit means:

    - Allows up to 6 new conenctions
    - Withing 30 seconds
    - Per source IP
    - If exceeded -> that IP is temprorary blocked from connecting to port 80

Effective against:

    - Port scanners (nmap, masscan, zmap)
    - Naive bots doing rapid connect-disconnect
    - Broken crawlers

Think of `ufw limit` as noise suppression, not security.