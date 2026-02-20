PROJECT_NAME = menu
DOCKER_COMPOSE = docker compose -p $(PROJECT_NAME)
APP_CONTAINER = $(shell $(DOCKER_COMPOSE) ps -q app)

help: ## Show this help
	@egrep -h '\s##\s' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

run: ## Docker Compose Up
	$(DOCKER_COMPOSE) up -d

build: ## Docker Compose Build
	$(DOCKER_COMPOSE) build

build-nocache: ## Docker Compose Build without cache
	$(DOCKER_COMPOSE) build --no-cache

install: ## Composer install
	$(DOCKER_COMPOSE) up -d
	if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec app composer install --ignore-platform-reqs --no-scripts; \
	fi

exec:## Enter the app container
	#docker exec -it $(PROJECT_NAME)-app bash
	@if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec -it app bash; \
	fi
exec-root:## Enter the app containerdocker exec -it --user root <контейнер_id_або_ім'я> /bin/bash
	#docker exec -it $(PROJECT_NAME)-app bash
	@if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec -it --user root app bash; \
	fi

test: ## Run phpunit tests - make test GROUP=
	@if [ -n "$(APP_CONTAINER)" ]; then \
		if [ -z "$(GROUP)" ]; then \
			$(DOCKER_COMPOSE) exec app php bin/phpunit; \
		else \
			$(DOCKER_COMPOSE) exec app php bin/phpunit --group $(GROUP); \
		fi; \
	fi

install-analyze: ##Install PhpStan, CS-fixer, Rector
	@if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec app composer  require  --dev phpstan/phpstan; \
		$(DOCKER_COMPOSE) exec app composer  require  --dev friendsofphp/php-cs-fixer; \
		$(DOCKER_COMPOSE) exec app composer  require  --dev rector/rector; \
	fi

analyze: rector-check cs-fixer-check phpstan ## Static analysis

fix: rector cs-fixer ## Code fixes by Rector and CS-fixer

phpstan: ## Static analysis with PHPStan
	@$(DOCKER_COMPOSE) exec app vendor/bin/phpstan analyze src --level 4

rector: ## Static analysis with Rector
	@$(DOCKER_COMPOSE) exec app vendor/bin/rector process src
rector-check: ## Static analysis with Rector check
	@$(DOCKER_COMPOSE) exec app vendor/bin/rector process src --dry-run

cs-fixer: ## Static analysis with CS-fixer
	@$(DOCKER_COMPOSE) exec app vendor/bin/php-cs-fixer fix src
cs-fixer-check: ## Static analysis with CS-fixer check
	@$(DOCKER_COMPOSE) exec app vendor/bin/php-cs-fixer fix src --dry-run --diff




