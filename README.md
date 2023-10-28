# IIS project Set-up

Required:
Docker https://docs.docker.com/engine/install/ && https://docs.docker.com/compose/install/
Symfony https://symfony.com/download

## Build Set-up
clone repositary (git clone https://github.com/TomJuh/BUT-IIS)
Start docker service
run: docker compose build --no-cache
run makefile (make)
make up (starts up docker files, detached)
open https://localhost in browser

## Using symfony
# both symfony and composer are accesible through makefile
make sf runs symfony console inside docker 
-you can pass commands with c=[COMMAND] (example: make sf c= make:user)

same for composer 
make composer runs composer console
passing arguments with c=[COMMAND] (example: make composer c='req symfony/orm-pack')

## Database
# Connecting
You can connect to database in docker with following authetication username: app, password: !ChangeMe!, (eventually I will), db name: app

## Disconnecting 
make down

## Docs
Stolen guides to makefile and docker
1. [Build options](docs/build.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using a Makefile](docs/makefile.md)
8. [Troubleshooting](docs/troubleshooting.md)
