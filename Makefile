## Variables
PROJECT_NAME=tcommerce
SERVICES=nginx php-fpm redis mysql

## Commands
CD_ENVIRONMENT=cd environment/server
ENSURE_ENV=cp -n environment/server/.env.example environment/server/.env

server\:env: ## Ensures an env file exists for docker-compose
	$(ENSURE_ENV)

server\:start: ## Starts local environment server (docker)
	$(ENSURE_ENV) \
	&& $(CD_ENVIRONMENT) \
	&& docker-compose --project-name=${PROJECT_NAME} up -d ${SERVICES}

server\:restart: ## Restarts local environment server (docker)
	$(ENSURE_ENV) \
	&& $(CD_ENVIRONMENT) \
	&& docker-compose --project-name=${PROJECT_NAME} up -d --build --force-recreate ${SERVICES}

server\:enter: ## Enters local environment server (docker php-fpm service)
	$(CD_ENVIRONMENT) \
	&& docker-compose --project-name=${PROJECT_NAME} exec --user docker php-fpm /bin/sh

server\:enter\:root: ## Enters local environment server as root user (docker php-fpm service)
	$(CD_ENVIRONMENT) \
	&& docker-compose --project-name=${PROJECT_NAME} exec php-fpm /bin/sh

server\:stop: ## Stops local environment server (docker containers)
	$(CD_ENVIRONMENT) \
	&& docker-compose --project-name=${PROJECT_NAME} stop

server\:destroy: ## Stops and destroys local environment server (destroy docker containers and network)
	$(CD_ENVIRONMENT) \
	&& docker-compose --project-name=${PROJECT_NAME} down
