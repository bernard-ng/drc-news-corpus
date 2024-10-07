.PHONY: default
default: help

# -----------------------------------
# Variables
# -----------------------------------
user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker compose

.PHONY: help
help:
	@echo Tasks:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

FORCE:

.PHONY: build
build: ## Build & start docker containers for development
	$(dc) build && \
	$(dc) up -d
	$(dc) run --rm php composer install && \
	$(dc) run --rm php bin/console c:c

.PHONY: lint
lint: vendor/autoload.php ## code quality analysis
	$(dc) run --rm php composer juro:code-quality

.PHONY: test
test: vendor/autoload.php ## unit and functional tests
	$(dc) run --rm php bin/console doctrine:database:create --if-not-exists --env=test
	$(dc) run --rm php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration --env=test
	$(dc) run --rm php vendor/bin/phpunit
	#$(dc) run --rm php vendor/bin/behat --format=progress --no-interaction

.PHONY: ssh
ssh: ## SSH into container
	$(dc) exec php bash

.PHONY: start
start: ## Start docker containers
	$(dc) up -d

.PHONY: restart
restart: ## Restart docker containers
	$(dc) restart

.PHONY: stop
stop: ## Stop docker containers
	$(dc) stop

.PHONY: destroy
destroy: ## Destroy docker containers
	$(dc) kill && \
	$(dc) rm -f

# -----------------------------------
# Dependencies
# -----------------------------------
vendor/autoload.php: composer.json
	$(dc) run --rm php composer install

composer.lock: composer.json
	$(dc) run --rm php composer update