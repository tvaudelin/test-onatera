#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$APP_ENV" != 'prod' ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

	mkdir -p var/cache var/log

	# The first time volumes are mounted, the project needs to be recreated
	if [ ! -f composer.json ]; then
		CREATION=1
		composer create-project "$SKELETON $SYMFONY_VERSION" tmp --stability="$STABILITY" --prefer-dist --no-progress --no-interaction --no-install

		cd tmp
		composer require "php:>=$PHP_VERSION"
		composer config --json extra.symfony.docker 'true'
		cp -Rp . ..
		cd -

		rm -Rf tmp/
	fi

	if [ "$APP_ENV" != 'prod' ]; then
		rm -f .env.local.php
		composer install --prefer-dist --no-progress --no-interaction
	fi

	if grep -q ^DATABASE_URL= .env; then
		if [ "$CREATION" = "1" ]; then
			echo "To finish the installation please press Ctrl+C to stop Docker Compose and run: docker-compose up --build"
			sleep infinity
		fi

		echo "Waiting for db to be ready..."
		until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
			sleep 1
		done

		if ls -A migrations/*.php >/dev/null 2>&1; then
			bin/console doctrine:migrations:migrate --no-interaction
		fi
	fi

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
