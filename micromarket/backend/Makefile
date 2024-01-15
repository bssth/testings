debug:
	DEBUG=1 php -S localhost:8080 -t public

prepare:
	cp .env.example .env
	composer install --dev --ignore-platform-reqs
	./vendor/bin/noverify-get

lint:
	./vendor/bin/noverify check ./framework ./src
