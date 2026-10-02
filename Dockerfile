FROM php:8.5-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install bcmath intl opcache pcntl pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

EXPOSE 8000
CMD ["sh", "-lc", "if [ ! -f .env ]; then cp .env.example .env; fi; git config --global --add safe.directory /var/www/html; if [ ! -f vendor/autoload.php ]; then composer install --no-scripts --no-interaction; php artisan package:discover --ansi; fi; if ! grep -q '^APP_KEY=base64:' .env; then php artisan key:generate --force; fi; php artisan migrate --force; php artisan serve --host=0.0.0.0 --port=8000"]
