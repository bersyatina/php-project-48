install:
	composer install

validate:
	composer validate

lint:
	phpcs -- --standard=PSR12 src bin

test:
	composer exec --verbose phpunit tests
