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

# -----------------------------------
# Docker Compose
# -----------------------------------
FORCE:
.PHONY: build
build: ## Build & start docker containers for development
	$(dc) build && \
	$(dc) up -d
	$(dc) run --rm php composer install && \
	$(dc) run --rm php bin/console c:c

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

.PHONY: logs
logs: ## Show logs of docker containers
	@docker-compose logs --tail=0 --follow

.PHONY: destroy
destroy: ## Destroy docker containers
	$(dc) kill && \
	$(dc) rm -f

# -----------------------------------
# CI / CD
# -----------------------------------
.PHONY: lint
lint: ## code quality analysis
	$(dc) run --rm php composer app:cs

.PHONY: test
test: ## unit and functional tests
	$(dc) run --rm php composer app:test
	#$(dc) run --rm php composer app:behat

.PHONY: audit
audit: ## security audit
	$(dc) run --rm php composer audit

.PHONY: deploy
deploy: ## Deployment tasks
	php ~/composer.phar install --optimize-autoloader
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	php bin/console cache:clear --env=prod
	php bin/console secrets:decrypt-to-local --env=prod
	php ~/composer.phar symfony:dump-env prod

# -----------------------------------
# Symfony
# -----------------------------------
.PHONY: migrate
migrate: ## Run migrations
	$(dc) run --rm php bin/console doctrine:databases:create --if-not-exists
	$(dc) run --rm php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: cache
cache: ## Clear cache
	$(dc) run --rm php bin/console cache:clear
	$(dc) run --rm php bin/console cache:warmup

# -----------------------------------
# Dependencies
# -----------------------------------
.PHONY: deps
deps: ## Install dependencies
	$(dc) run --rm php composer install
	$(dc) run --rm node yarn install --force
	$(dc) run --rm node yarn build
