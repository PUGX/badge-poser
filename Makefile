%:
		@:

args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

.PHONY: init run start stop install install_prod build build_prod purge phpunit php_cs_fixer phpstan analyse status

help:
	@awk 'BEGIN {FS = ":.*##"; printf "Use: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

init: ## initialize app
	- make stop
	- make start
	- make install
	- make build

run: ## run app
	- make stop
	- make start
	- make install
	- make build

start: ## start docker containers
	- docker-compose up --build -d

stop: ## stop docker containers
	- docker-compose down

install: ## install php and node dependencies
	- docker-compose exec php-fpm composer install
	- docker-compose run --rm node yarn install

install_prod: ## install php and node dependencies for production environment
	- docker-compose exec php-fpm composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader
	- docker-compose run --rm node yarn install --production

build: ## build assets
	- docker-compose run --rm node yarn dev

build_watch: ## build assets and watch
	- docker-compose run --rm node yarn watch

build_prod: ## build assets for production environment
	- docker-compose run --rm node yarn build

purge: ## cleaning
	- rm -rf node_modules vendor var/cache var/log public/build

phpunit: ## run suite of tests
	- docker-compose exec php-fpm ./bin/phpunit

php_cs_fixer: ## run php-cs-fixer
	- docker-compose exec php-fpm ./vendor/bin/php-cs-fixer fix -v

phpstan: ## run phpstan
	- docker-compose exec php-fpm ./vendor/bin/phpstan analyse

analyse: ## run php-cs-fixer and phpstan
	- make php_cs_fixer
	- make phpstan

status: ## docker containers status
	- docker-compose ps
