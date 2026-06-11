.PHONY: up down build seed logs open

up: ## Start MongoDB + PHP app containers
	docker compose up -d

down: ## Stop and remove containers
	docker compose down

build: ## Rebuild the PHP app image
	docker compose build app

seed: ## Drop and re-seed the database with sample data
	docker compose exec app php seed.php

logs: ## Tail app logs
	docker compose logs -f app

open: ## Open the data explorer in the browser
	open http://localhost:8080

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'
