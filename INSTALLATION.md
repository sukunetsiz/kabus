# Installation Guide for Kabus Monero Marketplace Script

This comprehensive guide will walk you through the installation process of the Kabus Monero Marketplace script on your system.

## Operating System Requirements
This guide is based on Ubuntu 22.04 LTS (Jammy Jellyfish). We strongly recommend using a Linux distribution for optimal performance and security. Windows is not recommended for this setup.

## System Preparation
Begin by updating your system to ensure all packages are current:
```bash
sudo apt update
sudo apt upgrade -y
```

## Installing Required Dependencies
### PHP 8.3 Installation
First, we'll install PHP 8.3 along with essential extensions required for the marketplace:
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring \
php8.3-xml php8.3-zip php8.3-bcmath php8.3-gnupg php8.3-intl php8.3-readline \
php8.3-common php8.3-cli
```

### Composer Installation
Install Composer 2, which we'll need for managing Laravel dependencies:
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
HASH="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

### Additional Dependencies
Install Git for version control:
```bash
sudo apt install -y git
```

Install Node.js and npm for frontend asset compilation:
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
sudo apt install -y npm
```

### Database Server
Install and configure MySQL server:
```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

### Web Server
Install and enable Nginx:
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

## Verification Steps
Verify all installations by checking their versions:
```bash
php -v
composer -V
git --version
node -v
npm -v
mysql --version
nginx -v
```

Expected output (versions may vary):
```
Composer version 2.8.4 2024-12-11 11:57:47
PHP version 8.3.15 (/usr/bin/php8.3)
git version 2.34.1
v12.22.9
8.5.1
mysql  Ver 8.0.40-0ubuntu0.22.04.1 for Linux on x86_64 ((Ubuntu))
nginx version: nginx/1.18.0 (Ubuntu)
```

## Database Configuration
### Secure MySQL Setup
Run the MySQL secure installation script:
```bash
sudo mysql_secure_installation
```

When prompted:
1. Enable the VALIDATE PASSWORD COMPONENT (select 'y')
2. Choose password validation level 1
3. Answer 'y' to all subsequent security questions

### Database and User Creation
Access the MySQL prompt:
```bash
sudo mysql
```

Create a new database user (replace the placeholder values with your own):
```sql
CREATE USER 'your_user'@'localhost' IDENTIFIED BY 'Y0ur_P@ssw0rd!23';
```

Create the database:
```sql
CREATE DATABASE your_database_name;
```

Grant necessary privileges:
```sql
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

Exit the MySQL prompt:
```sql
exit;
```

### Verify Database Access
Test your new database user credentials:
```bash
mysql -u your_user -p
```

After entering your password, verify database creation:
```sql
SHOW DATABASES;
```

Your database name should appear in the list. Make sure to securely store your database credentials, as they'll be required for the application's .env configuration file.

## TO DO
### Repository Setup and Laravel Configuration
Show how to:
- Download the latest release from GitHub
- Copy and configure .env.example file
- Run composer install
- Set correct file permissions
- Generate application key
- Any additional Laravel-specific configurations

### TO DO
### Tor Installation and Configuration
Show how to:
- Install Tor
- Configure Tor service
- Set up hidden service
- Configure marketplace to work with Tor

### TO DO
### Secure Nginx Configuration
Show how to:
- Configure Nginx for Laravel

These sections will be completed in future updates of this guide.
