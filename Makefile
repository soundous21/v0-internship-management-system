bin/console:
	bin/console

assets:
	npm install
	npm run build

install: vendor

vendor: composer.json
	composer install

db:
	bin/console doctrine:database:drop --force || true
	bin/console doctrine:database:create
	bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	bin/console doctrine:fixtures:load --no-interaction

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

docker-logs:
	docker-compose logs -f

.PHONY: assets install db fixtures docker-up docker-down docker-logs
