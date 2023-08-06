.PHONY: phpstan fix composer-valid yaml twig container doctrine analyze qa

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

analyze: twig yaml composer-valid container doctrine phpstan

qa: fix analyze
