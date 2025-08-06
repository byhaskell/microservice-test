SHELL := /bin/bash
PHP_SERVICE=myshop-php
DB_NAME=myshop
DB_SERVICE=db
PROJECT_SRC=$(PWD)

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "commands:"
	@echo "======================================================="
	@echo "  start     	Create and run the docker containers"
	@echo "  destroy   	Remove docker all images and containers"
	@echo "  stop      	Stop the docker containers"
	@echo "  composer  	Start composer update"
	@echo "  cache     	Remove cache and log symfony"
	@echo "  dump       Restore database"

#####################
### DOCKER
#####################
start:
	@docker-compose up -d --build

destroy:
	@docker-compose down -v --remove-orphans --rmi all

stop:
	@docker-compose stop

#####################
### APPLICATION
#####################
composer:
	@docker-compose exec php composer update --no-interaction

cache:
	@echo "Removing var files ..."
	@sudo rm -rf ${PROJECT_SRC}/var/cache
	@sudo rm -rf ${PROJECT_SRC}/var/log

migration:
	@docker-compose exec php php bin/console make:migration

migrate:
	@docker-compose exec php php bin/console doctrine:migrations:migrate

upgrade:
	@docker-compose exec php php bin/console make:entity --regenerate App
	@docker-compose exec php php bin/console make:migration
	@docker-compose exec php php bin/console doctrine:migrations:migrate
