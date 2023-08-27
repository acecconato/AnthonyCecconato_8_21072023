.PHONY: phpstan fix composer-valid yaml twig container doctrine analyze qa twigcs stylelint phpunit

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
twigcs:
	php vendor/bin/twigcs templates
stylelint:
	npx stylelint assets/styles/*.css
phpunit:
	php vendor/bin/phpunit

analyze: twig yaml composer-valid container doctrine phpstan twigcs stylelint phpunit

qa: fix analyze
