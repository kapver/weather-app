#!/bin/bash

## Install Composer dependencies via Docker
docker run --rm -v $(pwd):/opt -w /opt laravelsail/php84-composer:latest pwd && \
  cp .env.example .env && \
  composer install &&
  php artisan key:generate --ansi

# Start Sail services
./vendor/bin/sail up --build
#./vendor/bin/sail artisan migrate --seed
#./vendor/bin/sail npm install
#./vendor/bin/sail npm run build