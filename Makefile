UID=$(shell id -u)
GID=$(shell id -g)
FILE=docker-compose.yml

# Docker
build: ## docker compose build
	UID=${UID} GID={GID} docker compose -f ${FILE} build

up: ## up all containers
	UID=${UID} GID=${GID} docker compose -f ${FILE} up -d

stop: ## stop all containers
	UID=${UID} GID=${GID} docker compose -f ${FILE} stop

down: ## down all containers
	UID=${UID} GID=${GID} docker compose -f ${FILE} down

bash: ## gets inside a php container
	UID=${UID} GID={GID} docker compose -f ${FILE} exec --user=${UID} php-fpm sh

ps: ## status from all containers
	docker compose -f ${FILE} ps

# Dependencies
install: ## install dependencies
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "composer install"

update: ## update dependencies
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "composer update"

# Environment
init: ## initialize environment
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console environment:init"

clear: ## cache clear
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console cache:clear"

.PHONY: migrations
migrations:
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console environment:migrations"

createdb:
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console environment:database"

.PHONY: fixtures
fixtures:
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console environment:fixtures"

# Tools
.PHONY: tests
tests: ## execute project unit tests
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "phpunit --order=random"

behat:
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "behat --colors"

stan: ## check phpstan
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "php -d memory_limit=256M bin/phpstan analyse -c phpstan.neon"

cs: ## check code style
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "phpcs --standard=phpcs.xml.dist"

grump: ## run grumphp
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "grumphp run"

# Application
tags: ## Apply predefined tags
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console deck:tag:set"

importcards:
	docker compose -f ${FILE} exec --user=${UID} php-fpm sh -c "console import:card"

filebeat_lock_config:
	sudo chmod go-w ./docker/filebeat/filebeat.yml

filebeat_unlock_config:
	sudo chmod 777 ./docker/filebeat/filebeat.yml