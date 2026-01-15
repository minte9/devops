## HSTS - HTTP Strict Transport Security

- Is a secure standard that `forces browsers` to connect to a website only via secure HTTPS.
- It prevents insecure HTTP connections, mitigating `man-in-the-middle` attacks.
- Servers communicate this policy to browsers using `Strict-Transport-Security` header.

Withoud HSTS: 


    User types http://example.com
    ↓
    Attacker can intercept and strip HTTPS
    ↓
    User stays on HTTP

With HSTS:


    User types http://example.com
    ↓
    Browser forces HTTPS before request is sent
    ↓
    Attack blocked

    
### Update traefik

Add this under middlewares:

    middlewares:
        redirect-https:
            redirectScheme:
                scheme: https

    hsts:
      headers:
        stsSeconds: 31536000        # 1 year
        stsIncludeSubdomains: true
        stsPreload: false 

Now attach it to the HTTPS router:

    routers:
        zend-https:
        rule: "Host(`minte9.cloud`,`www.minte9.cloud`)"
        entryPoints:
            - websecure
        tls:
            certResolver: letsencrypt
        middlewares:
            - hsts
        service: zend


### Full example

    http:
        routers:
            zend-http:
                rule: "Host(`minte9.cloud`,`www.minte9.cloud`, `minte9.com`, `www.minte9.com`)"
                entryPoints:
                    - web
                middlewares:
                    - redirect-https
                service: zend

            zend-https:
                rule: "Host(`minte9.cloud`,`www.minte9.cloud`, `minte9.com`, `www.minte9.com`)"
                entryPoints:
                    - websecure
                tls:
                    certResolver: letsencrypt
                middlewares:
                    - hsts
                service: zend

        middlewares:
            redirect-https:
                redirectScheme:
                    scheme: https

            hsts:
                headers:
                    stsSeconds: 31536000        # 1 year
                    stsIncludeSubdomains: true
                    stsPreload: false

        services:
            zend:
            loadBalancer:
                servers:
                - url: "http://zend-apache:80"

### How to verify HSTS

Open DevTools in browser -> Networ -> your main request.  
Check response headers:

    Strict-Transport-Security: max-age=31536000; includeSubDomains

Or CLI:

    curl -I https://minte9.cloud
    HTTP/2 200 
    cache-control: no-store, no-cache, must-revalidate
    content-type: text/html; charset=UTF-8
    date: Thu, 08 Jan 2026 10:49:11 GMT
    expires: Thu, 19 Nov 1981 08:52:00 GMT
    pragma: no-cache
    server: Apache/2.4.65 (Debian)
    set-cookie: PHPSESSID=c0f1cc18707c571c262e21198c9040a6; path=/; HttpOnly
    strict-transport-security: max-age=31536000; includeSubDomains
    x-powered-by: PHP/8.2.30


