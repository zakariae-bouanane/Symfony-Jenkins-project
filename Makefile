build:
	docker compose build --force-rm

down:
	docker compose down

install-env:
		docker compose exec symfony composer dump-env dev

download-empty-project:
	docker compose exec symfony composer create-project symfony/skeleton:"7.0.*" .
	docker compose exec symfony composer require webapp

up:
	docker compose up -d --force-recreate --remove-orphans

remove-sources:
	rm -Rf code && mkdir code

install: down up download-empty-project install-env

start:
	docker compose up -d --no-recreate --remove-orphans

stop:
	docker compose stop

enter: 
	docker compose exec symfony bash

restart-nginx:
	docker compose exec nginx nginx -s reload