DOCKER_COMPOSE = docker compose -f ops/docker/docker-compose.yml
PHP = $(DOCKER_COMPOSE) exec php

.PHONY: start prepare unit behat migration run-migrations

start: ## Build and start all Docker containers
	$(DOCKER_COMPOSE) up -d --build

prepare: ## Install Composer dependencies
	$(PHP) composer install --no-interaction --prefer-dist

unit: ## Run PHPUnit unit tests
	$(PHP) php bin/phpunit --testsuite Unit

behat: ## Run Behat acceptance tests
	$(PHP) php vendor/bin/behat

migration: ## Generate a new Doctrine migration
	$(PHP) php bin/console doctrine:migrations:diff

run-migrations: ## Run pending Doctrine migrations
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction
