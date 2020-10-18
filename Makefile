#----------------------------------------------------------
# Variables
#----------------------------------------------------------
DOCKER_COMP = docker-compose
EXEC_PHP    = $(DOCKER_COMP) exec -T php
EXEC_NODE   = $(DOCKER_COMP) run --rm node
BASH_PHP	= $(DOCKER_COMP) exec php bash
BASH_NODE 	= $(EXEC_NODE) sh
SYMFONY     = $(EXEC_PHP) bin/console
COMPOSER    = $(DOCKER_COMP) run --rm composer
YARN        = $(EXEC_NODE) yarn

.DEFAULT_GOAL := help
help:  ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

## ------- Project ----------------------------------------
reboot: stop up ## Reboot development environment
start: install yarn encore up dmm dfl ## Initialize the project

## ------- Docker -----------------------------------------
up: ## Start the docker hub
	$(DOCKER_COMP) up -d

stop: ## Stop the docker hub
	$(DOCKER_COMP) stop

down: ## Stop and remove containers
	$(DOCKER_COMP) down --remove-orphans

## ------- Symfony ---------------------------------------
sf: ## List all Symfony commands
	$(SYMFONY)

cc: ## Clear the cache
	$(SYMFONY) cache:clear

purge: ## Purge cache and logs
	rm -rf var/cache/* var/logs/*

## ------- Composer --------------------------------------
install: ## Install vendors according to the current composer.lock file
	$(COMPOSER) install --no-progress --no-suggest --prefer-dist

update: composer.json ## Update vendors according to the composer.json file
	$(COMPOSER) update

## ------- Doctrine --------------------------------------
dmm: ## Execute migrations
	$(SYMFONY) doctrine:migration:migrate --no-interaction

dfl: ## Load fixtures
	$(SYMFONY) doctrine:fixture:load --no-interaction

dmg: ## Generate a blank migration class
	$(SYMFONY) doctrine:migrations:generate

## ------- Yarn ------------------------------------------
yarn: ## Start Yarn install
	$(YARN) install

encore: ## Compile assets once
	$(YARN) encore dev

watch: ## Recompile assets automatically when files change
	$(YARN) encore dev --watch

## ------- Tools -----------------------------------------
bp: ## Bash into php container
	$(BASH_PHP)

bn: ## Bash into node container
	$(BASH_NODE)
