%:
		@:

args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

.PHONY: init run start stop install install_prod build build_prod purge phpunit php_cs_fixer phpstan analyse status

help:
	@awk 'BEGIN {FS = ":.*##"; printf "Use: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ GENERIC

init: ## initialize app (For the first initialize of the app)
	- cp .env.dist .env
	- make run

run: ## run app
	- make stop
	- make start
	- make install
	- make build

start: ## start docker containers
	- docker-compose up -d

stop: ## stop docker containers
	- docker-compose down

dc_build: ## rebuild docker compose containers
	- make .docker_img_deps
	- docker-compose up --build

purge: ## cleaning
	- rm -rf node_modules vendor var/cache var/log public/build

status: ## docker containers status
	- docker-compose ps

##@ DEV

install: ## install php and node dependencies
	- docker-compose exec phpfpm composer install --ignore-platform-reqs
	- docker-compose run --rm node yarn install

build: ## build assets
	- docker-compose run --rm node yarn dev

build_watch: ## build assets and watch
	- docker-compose run --rm node yarn watch

phpunit: ## run suite of tests
	- docker-compose exec phpfpm ./bin/phpunit

php_cs_fixer: ## run php-cs-fixer
	- docker-compose exec phpfpm ./vendor/bin/php-cs-fixer fix -v

phpstan: ## run phpstan
	- docker-compose exec phpfpm ./vendor/bin/phpstan analyse

analyse: ## run php-cs-fixer and phpstan
	- make php_cs_fixer
	- make phpstan

##@ PROD

install_prod: ## install php and node dependencies for production environment
	- docker-compose exec phpfpm composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
	- docker-compose run --rm node yarn install --production

build_prod: ## build assets for production environment
	- docker-compose run --rm node yarn build

##@ DARK-CANARY

install_canary: ## install php and node dependencies (dark-canary)
	- docker-compose exec phpfpm-darkcanary composer install --ignore-platform-reqs
	- docker-compose run --rm node yarn install

build_canary: ## build assets (dark-canary)
	- docker-compose run --rm node yarn dev

build_watch_canary: ## build assets and watch (dark-canary)
	- docker-compose run --rm node yarn watch

phpunit_canary: ## run suite of tests (dark-canary)
	- docker-compose exec phpfpm-darkcanary ./bin/phpunit

php_cs_fixer_canary: ## run php-cs-fixer (dark-canary)
	- docker-compose exec phpfpm-darkcanary ./vendor/bin/php-cs-fixer fix -v

phpstan_canary: ## run phpstan (dark-canary)
	- docker-compose exec phpfpm-darkcanary ./vendor/bin/phpstan analyse

analyse_canary: ## run php-cs-fixer and phpstan (dark-canary)
	- make php_cs_fixer_canary
	- make phpstan_canary

##@ DEPLOY

.docker_img_deps:
	docker build -t jsbuilder -f sys/docker/common/Dockerfile.assets .
	docker build -t awsbuilder -f sys/docker/common/Dockerfile.aws-ecs-ci .

AWS_PROFILE ?= poser
AWS_ACCOUNT_ID ?= $(shell aws sts get-caller-identity --profile=$(AWS_PROFILE) | jq -r '.Account')
PREVIOUS_TAG=$(shell git ls-remote --tags 2>&1 | awk '{print $$2}' | sort -r | head -n 1 | cut -d "/" -f3)

deploy_prod: .docker_img_deps ## deploy to prod
	git fetch --all --tags > /dev/null; \
	git log --pretty=format:"- %B" ${START_LOG}..HEAD | tr '\r' '\n' | grep -Ev '^$$' > CHANGELOG; \
	sed -i 's/^*/-/' CHANGELOG; sed -i 's/^[ \t]*//' CHANGELOG; sed -i 's/^-[ \t]*//' CHANGELOG; sed -i 's/^-[ \t]*//' CHANGELOG; sed -i 's/^/ - /' CHANGELOG; \
	aws ecr get-login-password --profile $$AWS_PROFILE | docker login --password-stdin -u AWS $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com; \
	VER=$(shell date +%s); \
	docker build \
		-t $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com/badge-poser:phpfpm-$$VER \
		-f sys/docker/alpine-phpfpm/Dockerfile .; \
	docker push $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com/badge-poser:phpfpm-$$VER; \
	docker build \
		-t $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com/badge-poser:phpfpm8-$$VER \
		-f sys/docker/alpine-phpfpm8/Dockerfile .; \
	docker push $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com/badge-poser:phpfpm8-$$VER; \
	docker build \
		-t $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com/badge-poser:nginx-$$VER \
		-f sys/docker/alpine-nginx/Dockerfile .; \
	docker push $(AWS_ACCOUNT_ID).dkr.ecr.eu-west-1.amazonaws.com/badge-poser:nginx-$$VER; \
	cat sys/cloudformation/parameters.prod.json \
		| sed -e 's/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": ".+"}/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": "'$$VER'"}/' \
		      -e 's/{"ParameterKey": "EcrImageTagPhp", "ParameterValue": ".+"}/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": "'$$VER'"}/' \
		      -e 's/{"ParameterKey": "EcrImageTagPhpCanary", "ParameterValue": ".+"}/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": "'$$VER'"}/' \
		| tee sys/cloudformation/parameters.prod.json.new; \
        mv sys/cloudformation/parameters.prod.json sys/cloudformation/parameters.prod.json.bak; \
        mv sys/cloudformation/parameters.prod.json.new sys/cloudformation/parameters.prod.json; \
	cat sys/cloudformation/parameters.prod.secrets.json \
		| sed -e 's/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": ".+"}/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": "'$$VER'"}/' \
		      -e 's/{"ParameterKey": "EcrImageTagPhp", "ParameterValue": ".+"}/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": "'$$VER'"}/' \
		      -e 's/{"ParameterKey": "EcrImageTagPhpCanary", "ParameterValue": ".+"}/{"ParameterKey": "EcrImageTagNginx", "ParameterValue": "'$$VER'"}/' \
		| tee sys/cloudformation/parameters.prod.secrets.json.new; \
        mv sys/cloudformation/parameters.prod.secrets.json sys/cloudformation/parameters.prod.secrets.json.bak; \
        mv sys/cloudformation/parameters.prod.secrets.json.new sys/cloudformation/parameters.prod.secrets.json; \
	aws --profile=$$AWS_PROFILE cloudformation create-change-set \
		--stack=poser-ecs \
		--change-set-name=poser-ecs-$$VER \
		--description="$(shell cat CHANGELOG)" \
		--template-body=file://$$PWD/sys/cloudformation/stack.yaml \
		--parameters=file://$$PWD/sys/cloudformation/parameters.prod.secrets.json
