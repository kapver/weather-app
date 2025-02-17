#!/bin/sh

# Start Laravel Sail as usual
start-container &

# Run scheduler
php artisan schedule:work