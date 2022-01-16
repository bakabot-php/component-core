ci: cs-diff psalm phpunit readme cleanup

cleanup:
	docker-compose down -v

composer:
	docker-compose run --rm composer validate
	docker-compose run --rm composer install --quiet --no-cache --ignore-platform-reqs

cs-diff: composer
	docker-compose run --rm php vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

cs-fix: composer
	docker-compose run --rm php vendor/bin/php-cs-fixer fix

normalize:
	docker-compose run --rm composer normalize --quiet
	docker-compose run --rm php vendor/bin/php-cs-fixer fix

phpunit: composer
	docker-compose run --rm php -dxdebug.mode=coverage vendor/bin/phpunit

psalm: composer
	docker-compose run --rm php vendor/bin/psalm --show-info=true

readme:
	docker-compose run --rm php vendor/bin/update-readme --dry-run
