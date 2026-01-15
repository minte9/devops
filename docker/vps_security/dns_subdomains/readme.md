### Folder structure

Directories should represent projects, not domains.  
Domains belong to:

- DNS
- Traefik routing

Not to filesystem layout.


### Think in three layers:

    DOMAIN (Traefik)
    ↓
    PROJECT (folder)
    ↓
    CONTAINER (runtime)


- Folders = projects
- Traefik = domain mapping
- Docker = execution


### Structure (recommended)

    docker/
    ├── minte9/
    │   ├── docker-compose.yml
    │   ├── app/
    │   │   └── src/
    │   └── traefik/
    │       └── dynamic/sites/minte9.yml
    │
    ├── books/
    │   ├── docker-compose.yml
    │   ├── app/
    │   │   └── src/
    │   └── traefik/
    │       └── dynamic/sites/books.yml
    │
    └── shared/
        └── traefik/        # optional later (single Traefik for all)


### Domains live only here:

/etc/hosts (local)

    127.0.0.1 minte9.local
    127.0.0.1 books.minte9.local

DNS (prod)

    minte9.com        → VPS_IP
    books.minte9.com  → VPS_IP

