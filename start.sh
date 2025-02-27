#!/bin/bash

# Install Composer dependencies via Docker
docker run --rm -v $(pwd):/opt -w /opt laravelsail/php84-composer:latest composer install

# Copy .env.example to .env if it doesn't exist
[ ! -f ".env" ] && cp .env.example .env

# Install npm dependencies and build assets via Sail
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# Start Sail services
./vendor/bin/sail up