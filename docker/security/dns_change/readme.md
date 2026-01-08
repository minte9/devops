## DNS CHANGE

### Get your VPS public IP

On the VPS:

    curl -4 ifconfig.me
        72.62.152.27

Edit `DNS Records` in the DNS provider:

    Log in to GoDaddy → My Products → Domains → DNS

Replace the `A` record for root domain:

    Type: A
    Name: @
    Value: 72.62.152.27
    TTL: 600 (or default)

Add / update `www` subdomain

    Type: CNAME
    Name: www
    Value: minte9.com

### WAIT for DNS propagatio

First the `dig` will respond with old IP and nameservers).

    dig mydomain.com +short
        145.14.151.246

    dig NS minte9.com +short
        ns2.dns-parking.com.
        ns1.dns-parking.com.

Check from `Google` DNS (propagated):

    dig minte9.com @1.1.1.1 +short 
        72.62.152.27
        
    dig NS minte9.com @1.1.1.1 +short 
        ns45.domaincontrol.com. 
        ns46.domaincontrol.com.

### Why it works “everywhere except my machine”

Your local resolver still has:

    NS → ns1/ns2.dns-parking.com
    A  → 145.14.151.246

Some ISPs:

- Ignore TTL for NS records
- Cache for 6–24 hours
- Cannot be flushed client-side
- Nothing is broken.

### What you can do right now
  
Temporarily edit `/etc/hosts`: