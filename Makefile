#----------------------------------------------------------
# Variables
#----------------------------------------------------------
DC:=docker-compose

#----------------------------------------------------------
# Targets
#----------------------------------------------------------

reboot: stop up
start: compose yarn encore up db_create migration fixture

stop:
	$(DC) stop

up:
	$(DC) up -d

yarn:
	yarn install

encore:
	yarn run encore dev

clear:
	php bin/console cache:clear

compose:
	composer install

migration:
	$(DC) exec -T php bin/console doctrine:migration:migrate --no-interaction

db_create:
	php bin/console doctrine:database:create

fixture:
	$(DC) exec -T php bin/console doctrine:fixture:load --no-interaction
