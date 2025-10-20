.PHONY: start-main start-internal

start-main:
	@echo "Starting up the development environment..."
	composer install -o
	composer run-script post-install-cmd
	sleep 3
	./symfony_linux_386 serve

start-internal:
	@echo "Starting internal sources ...."
	@command -v parallel >/dev/null 2>&1 && \
		parallel -j4 'php -S 127.0.0.1:{} -t public > /dev/null 2>&1 & echo $$! > .pid-{}' ::: 8015 8016 8017 8018 || \
		(php -S localhost:8015 -t public > /dev/null 2>&1 & echo $$! > .pid-8015 && \
		 php -S localhost:8016 -t public > /dev/null 2>&1 & echo $$! > .pid-8016 && \
		 php -S localhost:8017 -t public > /dev/null 2>&1 & echo $$! > .pid-8017 && \
		 php -S localhost:8018 -t public > /dev/null 2>&1 & echo $$! > .pid-8018)
	@echo "All sources started"