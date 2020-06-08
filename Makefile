install:
	composer install
lint: install
	composer run-script phpcs -- --standard=PSR12 src tests
test: install
	composer run-script phpunit -- --coverage-php tests/clover.xml tests
