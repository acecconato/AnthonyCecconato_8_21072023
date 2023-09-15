# AnthonyCecconato_8_21072023

Améliorez une application existante de ToDo & Co - Openclassrooms projet 8

## Get started

First, clone the repository and move to it
> git clone https://github.com/acecconato/AnthonyCecconato_8_21072023.git && cd AnthonyCecconato_8_21072023

Now copy the .env to a .env.local file
> cp .env .env.local

In the .env.local file you will find a `DATABASE_URL` value. We are using postgresql by default, started with
Docker in the following steps. But you can use your own like MySQL or SQLite, for example: https://symfony.com/doc/current/doctrine.html#configuring-the-database

## Dependencies

Install composer dependencies with:
> composer install

Install npm dependencies with:
> npm install

Build the assets:
> npm run dev

## Create database

(Optionally)

If you want, you can quickly start the PostgreSQL Dockerized database. It will listen on port 5432 by default but you can
change it if required
> docker-compose up -d

---

After configuring the database connection in the .env.local file, you can create the database with
> php bin/console d:d:c

Then run the migrations
> php bin/console d:m:m

Finally, you can load the fixtures:
> php bin/console d:f:l

You can also do the same for the test environment:
> php bin/console d:d:c --env=test && php bin/console d:m:m --env=test && php bin/console d:f:l --env=test 


## Start the application
> symfony serve


## Demo accounts

User :
- id: demo
- pass: demo

Administrator: 
- id: admin
- pass: demo
