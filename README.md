# [Installation Guide](docs/INSTALLATION.md)

# Kabus - Monero Marketplace Script

## Introduction

The purpose of creating Kabus was to contribute to the Monero ecosystem and ensure its growth. It was never created for any illegal purpose, nor does it encourage such activities. The aim of this marketplace script is to facilitate the sale of legal products online as anonymously as possible.

Built with PHP 8.3 and Laravel 11.

## Core Features

### Monero Integration
- **Vendor Registration Payment**: Monero Wallet RPC integration that generates a wallet address for vendor fee payments, with support for separate transactions and a 24-hour payment window
- **Product Advertising Payment**: Integrated payment system for vendors to advertise their products on the homepage through Monero transactions
- **Product Purchasing**: Integrated Monero payment system for secure and anonymous product transactions
- **Return Address System**: Validation for user's Monero return addresses

![Vendor Registration Payment](docs/1.png)

### Marketplace Functions
- **User Dashboard**: Comprehensive control panel for account management
- **Vendor Profiles**: Vendor pages with product listings
- **Product Management**: Search functionality and wishlist feature
- **Messaging System**: Secure communication between users
- **Admin Panel**: Complete administrative control interface
- **Vendor Panel**: Dedicated interface for vendor operations
- **Reference System**: Optional referral code requirement for registration
- **Educational Resources**: Comprehensive guides on Monero, Tor, KeePassXC and Kleopatra usage for new users
- **Support System**: Integrated help desk functionality
- **Disputes System**: Facilitates resolution of order-related issues between buyers and vendors with administrative intervention when necessary

### Security & Privacy
- **Walletless Escrow System**: No user wallets; payments are made per order and escrowed until order resolution
- **PGP Integration**: Mandatory PGP key confirmation for vendors to verify key ownership
- **Two-Factor Authentication**: Enhanced security through PGP-based 2FA
- **Mnemonic Recovery**: Built-in mnemonic phrase generation for key recovery
- **No JavaScript**: Built entirely with pure PHP and does not utilize JavaScript in any capacity

### Other Screenshots

![Products Page](docs/2.png)
---
![AdminPanel's User Page](docs/3.png)
---
![Account Page](docs/4.png)
---
![Support Page 1](docs/5.png)
---
![Support Page 2](docs/6.png)
---
![Products Page](docs/7.png)
---
![Cart Page](docs/8.png)
---
![Return Addresses Page](docs/9.png)
---
![Sales Page](docs/10.png)
---
![Advertisements Page](docs/11.png)
---
![Vendors Page](docs/12.png)
---
# [View the Roadmap](docs/ROADMAP.md)

# [Monero Wallet RPC Guide](docs/CONNECTING-MONERO-RPC.md)

### Supporting Development
The Kabus marketplace script is maintained as an open source project dedicated to improving privacy-focused commerce. If you'd like to support ongoing development and maintenance:

- **Monero Donations**: Direct support through Monero payments
- **Donation Address:** `8AfRSCLzLR7PuFjdztbxDaWyXkYXtbLfUQK8iP27bcyu6yDQYQHncfGMZjX7cee9tTU9Qu1hsax93KFQcnhApKEr5pgP1N5`
- **Code Contributions**: Pull requests and improvements are welcome
- **Bug Reports**: Help improve platform stability by reporting issues
- **Feature Suggestions**: Share ideas for enhancing marketplace functionality

*All donations are used exclusively for maintaining and improving the open source codebase.*
---

```
Privacy is a human right, and it can never be taken away from anyone, nor should it even be suggested.
```
