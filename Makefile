.PHONY: phpstan fix composer-valid yaml twig container doctrine analyze qa stylelint phpunit

composer-valid:
	composer valid
yaml:
	php bin/console lint:yaml config --parse-tags
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

analyze: twig yaml composer-valid container doctrine phpstan stylelint phpunit phpmd

qa: fix analyze

phpmd:
	php phpmd.phar src text cleancode,design,naming --exclude *Fixtures.php
.PHONY: phpmd

fixtures:
	php bin/console doctrine:fixtures:load -n
	php bin/console doctrine:fixtures:load --env=test -n
	php bin/console doctrine:fixtures:load --env=dev -n
.PHONY: fixtures

