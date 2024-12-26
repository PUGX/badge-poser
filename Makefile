%:
		@:

args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`
VER:=$(shell date +%s)

.PHONY: init run start stop install install_prod build build_prod purge phpunit php_cs_fixer phpstan analyse status

help:
	@awk 'BEGIN {FS = ":.*##"; printf "Use: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ GENERIC

init: ## initialize app (For the first initialize of the app)
	- cp .env .env.local
	- make run

run: ## run app
	- make stop
	- make start
	- make install
	- make build_dev

start: ## start docker containers
	- docker compose up --build -d

stop: ## stop docker containers
	- docker compose down

dc_build_prod: ## rebuild docker compose containers
	- docker compose up --build

purge: ## cleaning
	- rm -rf node_modules vendor var/cache var/log public/build

status: ## docker containers status
	- docker compose ps

##@ DEV

install: ## install php and node dependencies
	- ./sys/nginx/certs/gen-selfsigned-cert.sh poser.local
	- docker compose exec phpfpm composer install
	- docker compose run --rm node yarn install

build_dev: ## build assets
	- docker compose run --rm node yarn dev

build_watch: ## build assets and watch
	- docker compose run --rm node yarn watch

phpunit: ## run suite of tests
	- docker compose exec phpfpm bin/phpunit -d memory_limit=-1

php_cs_fixer: ## run php-cs-fixer
	- docker compose exec phpfpm ./vendor/bin/php-cs-fixer fix -v

phpstan: ## run phpstan
	- docker compose exec phpfpm ./vendor/bin/phpstan analyse

analyse: ## run php-cs-fixer and phpstan
	- make php_cs_fixer
	- make phpstan

##@ PROD

install_prod: ## install php and node dependencies for production environment
	- docker compose exec phpfpm composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
	- docker compose run --rm node yarn install --production

build_prod: ## build assets for production environment
	- docker compose run --rm node yarn build

##@ DEPLOY

AWS_PROFILE ?= poser
AWS_REGION ?= eu-west-1
AWS_ACCOUNT_ID ?= $(shell aws sts get-caller-identity --profile=$(AWS_PROFILE) | jq -r '.Account')
PREVIOUS_TAG=$(shell git ls-remote --tags 2>&1 | awk '{print $$2}' | sort -r | head -n 1 | cut -d "/" -f3)
ECR_REGISTRY = $(AWS_ACCOUNT_ID).dkr.ecr.$(AWS_REGION).amazonaws.com

deploy_prod: build_prod_images push_prod_images ## deploy to prod
# TODO: convert to terraform apply
# TODO: modify IAM policy to allow only the creation of ECS tasks via pipelines
	terraform plan -var="ecr_image_tag_nginx=$(VER)" -var="ecr_image_tag_php=$(VER)"

build_%: export BADGE_POSER_REGISTRY = $(ECR_REGISTRY)/badge-poser
build_%: export DOCKER_BUILDKIT = 1

build_prod_images:
	DOCKER_BUILDKIT=1 docker build \
		-t $(BADGE_POSER_REGISTRY):phpfpm-$(VER) \
		-f sys/docker/alpine-phpfpm/Dockerfile .; \
	DOCKER_BUILDKIT=1 docker build \
		-t $(BADGE_POSER_REGISTRY):nginx-$(VER) \
		-f sys/docker/alpine-nginx/Dockerfile .

push_%: export BADGE_POSER_REGISTRY = $(ECR_REGISTRY)/badge-poser
push_%: export DOCKER_BUILDKIT = 1

push_prod_images:
	aws ecr get-login-password --profile $(AWS_PROFILE) | docker login --password-stdin -u AWS $(ECR_REGISTRY); \
	docker push $(BADGE_POSER_REGISTRY):phpfpm-$(VER); \
	docker push $(BADGE_POSER_REGISTRY):nginx-$(VER)
