# AnthonyCecconato_8_21072023

Améliorez une application existante de ToDo & Co - Openclassrooms projet 8

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/bf6901d6bba1451195c22c5935e45356)](https://app.codacy.com/gh/acecconato/AnthonyCecconato_8_21072023/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
![Phpstan badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

## Documentation

### Use case diagram

![Main use case diagram](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/usecase/main.png?raw=true)

### Class diagram
- [Class diagram](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/class/class.md)

### Sequence diagrams

**Functionnal**
- [Add task](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/add_task.md)
- [Add user](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/add_user.md)
- [Delete task](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/delete_task.md)
- [Display completed tasks](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/display_completed_tasks.md)
- [Display to do list](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/display_todo_list.md)
- [Mark task](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/mark_task.md)

**Security**
- [Login](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/login.md)
- [Logout](https://github.com/acecconato/AnthonyCecconato_8_21072023/blob/main/docs/sequence/logout.md)

---

## Get started

Clone the repository on your environment
```console
git clone https://github.com/acecconato/AnthonyCecconato_8_21072023.git
cd AnthonyCecconato_8_21072023
```

Next it's recommanded to copy the .env to a .env.local file
```console
cp .env .env.local
```

In the .env.local file you will find a `DATABASE_URL` value. Actually we are using a postgresql database by default, which is started with a
Docker ccontainer in the following steps. But you can use your own database such as MySQL or SQLite. You will find more options here: https://symfony.com/doc/current/doctrine.html#configuring-the-database

## Dependencies

### Install composer and npm dependencies

**Composer**
```console
composer install
```

**npm**
```console
npm install
```

### Build the assets

For dev environment:
```console
npm run dev
```

For prod environment:
```console
npm run build
```

## Create the database, generate schemas and load fixtures

(Optional) If you wish, you can quickly start the Dockerized PostgreSQL database. It will listen on port 5432 by default, but you can change this to suit your needs.
```console
docker compose up -d
```

---

After configuring the database connexion in the .env.local file, you can create the database with
```console
php bin/console doctrine:database:create
# php bin/console doctrine:database:create --env=test
```

Then run the migrations
```console
php bin/console doctrine:migration:migrate
# php bin/console doctrine:migration:migrate --env=test
```

Finally, you can load the fixtures:
```console
php bin/console doctrine:fixture:load
# php bin/console doctrine:fixture:load --env=test
```

## Start the application
```console
symfony serve
```


## Demo accounts

User :
- id: demo
- pass: demo

Administrator: 
- id: admin
- pass: demo

## Makefile

Many commands are available to save time

Example usage: `make qa` 
- Will run associated commands: `fix analyze` which contains `php vendor/bin/php-cs-fixer fix` and `twig yaml composer-valid container doctrine phpstan stylelint phpunit`

```makefile
.PHONY: phpstan fix composer-valid yaml twig container doctrine analyze qa stylelint phpunit

composer-valid:
	composer valid
yaml:
	php bin/c`onsole lint:yaml config --parse-tags
twig:
	php bin/console lint:twig templates
container:
	php bin/console lint:container
doctrine:
	php bin/console doctrine:schema:valid --skip-sync
phpstan:
	php vendor/bin/phpstan analyse -c phpstan.neon
fix:
	php vendor/bin/php-cs-fixer fix
stylelint:
	npx stylelint assets/styles/*.css
phpunit:
	php vendor/bin/phpunit
phpunit-coverage:
	XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html phpunit_code_coverage

analyze: twig yaml composer-valid container doctrine phpstan stylelint phpunit

qa: fix analyze

fixtures:
	php bin/console doctrine:fixtures:load -n
	php bin/console doctrine:fixtures:load --env=test -n
	php bin/console doctrine:fixtures:load --env=dev -n
.PHONY: fixtures
```
