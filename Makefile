PROJECT_NAME = menu
DOCKER_COMPOSE = docker compose -p $(PROJECT_NAME) -f docker/docker-compose.yml
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
	@if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec -it app sh; \
	fi

run-scenarios: ## Run Scenario 1 and Scenario 2
	@if [ -n "$(APP_CONTAINER)" ]; then \
       $(DOCKER_COMPOSE) exec app php src/run.php; \
    fi

test-lib: ## Run only SDK library tests
	@if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec -w /app/lib app ../vendor/bin/phpunit tests; \
	fi

test-app: ## Run only Application scenario tests
	@if [ -n "$(APP_CONTAINER)" ]; then \
		$(DOCKER_COMPOSE) exec app php vendor/bin/phpunit tests; \
	fi





