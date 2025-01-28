#!/bin/bash

# Go to your Laravel project directory
cd /var/www/kabus

# Put Laravel into maintenance mode
php artisan down

# Create a flag file for nginx
touch storage/framework/down

# Reload nginx to ensure changes take effect
sudo systemctl reload nginx
