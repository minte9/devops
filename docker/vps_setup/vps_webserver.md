### Web server

Start a simple web server

    python3 --version
    Python 3.10.12
    
    sudo python3 -m http.server 80

    curl -I http://srv1243868.hstgr.cloud
    HTTP/1.0 200 OK
    Server: SimpleHTTP/0.6 Python/3.10.12
    Date: Sat, 03 Jan 2026 16:31:10 GMT
    Content-type: text/html; charset=utf-8
    Content-Length: 667
    
Free hosting domain: minte9.cloud
    
    curl -I minte9.cloud
    HTTP/1.0 200 OK
    Server: SimpleHTTP/0.6 Python/3.10.12
    Date: Sat, 03 Jan 2026 16:43:16 GMT
    Content-type: text/html; charset=utf-8
    Content-Length: 667
    
Disable directory listing.
    
    cd ~
    mkdir -p public_html
    echo "HTTP/1.0 200 OK" > index.html
    cd public_html
    sudo python3 -m http.server 80
    
    http://minte9.com
    HTPP/1.0 200 OK