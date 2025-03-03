#!/bin/bash
set -euo pipefail

##############################################
# Safety Check – Do Not Run as Root!         #
##############################################
if [ "$EUID" -eq 0 ]; then
    echo "Please do not run this script as root. Run it as your normal user." >&2
    exit 1
fi

##############################################
# Configurable Variables                     #
##############################################
PROJECT_DIR="/var/www/kabus"
FLAG_FILE="storage/framework/down"

##############################################
# Function: Enable Maintenance Mode          #
##############################################
enable_maintenance() {
    echo "Enabling maintenance mode..."
    cd "$PROJECT_DIR" || { echo "Error: Could not navigate to $PROJECT_DIR"; exit 1; }
    
    # Put Laravel into maintenance mode.
    php artisan down

    # Create the flag file for nginx.
    touch "$FLAG_FILE"
    
    echo "Maintenance mode enabled. Reloading nginx..."
    sudo systemctl reload nginx
    echo "Nginx reloaded."
}

##############################################
# Function: Disable Maintenance Mode         #
##############################################
disable_maintenance() {
    echo "Disabling maintenance mode..."
    cd "$PROJECT_DIR" || { echo "Error: Could not navigate to $PROJECT_DIR"; exit 1; }
    
    # Bring Laravel back up.
    php artisan up

    # Remove the flag file.
    rm -f "$FLAG_FILE"
    
    echo "Maintenance mode disabled. Reloading nginx..."
    sudo systemctl reload nginx
    echo "Nginx reloaded."
}

##############################################
# Main Menu and User Interaction             #
##############################################
echo "Maintenance Mode Manager"
echo "-------------------------"
echo "1) Enable Maintenance Mode"
echo "2) Disable Maintenance Mode"
echo "-------------------------"
read -rp "Enter your choice (1 or 2): " choice

case "$choice" in
    1)
        enable_maintenance
        ;;
    2)
        disable_maintenance
        ;;
    *)
        echo "Invalid choice. Please enter 1 or 2."
        exit 1
        ;;
esac

exit 0
