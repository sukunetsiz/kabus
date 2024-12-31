#!/bin/bash

# Go to your Laravel project directory
cd /var/www/Kabus

# Bring Laravel back up
php artisan up

# Remove the flag file
rm -f storage/framework/down

# Reload nginx to ensure changes take effect
sudo systemctl reload nginx
