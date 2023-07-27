# Get started

This project use the following docker images: https://github.com/sprintcube/docker-compose-lamp

Requirements:
- Docker (https://www.docker.com/get-started/)
- Docker Compose (https://docs.docker.com/compose/install/)

All is included in this repo, so you don't need to download anything else.

## How does it work? 

We will create a dev environment with:
- php 5.6
- mariadb 10
- apache2

The projects files are inside the `/www` directory and the volume is persisted. All changes to this dir will be kept.

## Install

Download the v1.0 archive (vendors are included there, so you won't need to regenerate them).

https://github.com/acecconato/AnthonyCecconato_8_21072023/releases/download/v1.0/AnthonyCecconato_8_21072023.zip

Run: 

`docker-compose up -d`

If necessary, you can:
- Enter in the container with: `docker exec -it lamp-php56 bash`
- Get the ip address of the mysql container: `docker inspect lamp-php56 | grep IPAddress` (could be necessary if you need to change the mysql host db from parameters)
