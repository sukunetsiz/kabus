## Connecting Monero Wallet RPC to Kabus Marketplace

This guide will walk you through connecting a Monero wallet RPC to your Kabus marketplace installation.

## Prerequisites
Before we begin, ensure you're in your Downloads folder where we'll store the Monero CLI tools:
```bash
cd Downloads
```

## Downloading and Extracting Monero CLI
First, let's download the Monero CLI package:
```bash
wget https://downloads.getmonero.org/cli/linux64
```

Extract the downloaded package:
```bash
bunzip2 linux64
tar xvf linux64.out
```

Navigate to the extracted directory (your version number might differ):
```bash
cd monero-x86_64-linux-gnu-v0.18.3.4
```

## Creating Your Marketplace Wallet
Now we'll create a dedicated wallet for your marketplace:
```bash
./monero-wallet-cli
```

When prompted:
1. Enter "kabus-wallet" as your wallet name
2. Type "Y" to confirm
3. Password is optional - press Enter to skip
4. Select language by typing "1" for English
5. **Important**: Securely store the 25-word seed phrase that appears
6. Choose whether to enable background mining (type "Yes" or "No")

To exit the wallet CLI, press CTRL+C.

## Connecting to a Monero Node
You have two options for connecting to the Monero network:
- Set up your own node (better privacy but requires more time and storage)
- Connect to a remote node (faster setup but less private)

For this guide, we'll use a remote node. You can find trusted remote nodes at [xmr.ditatompel.com/remote-nodes/](https://xmr.ditatompel.com/remote-nodes/).

First, let's get your wallet's exact location:
```bash
realpath kabus-wallet
```

## Starting the Wallet RPC Server
Use the following command to start the RPC server (replace the wallet-file location with your actual path):
```bash
./monero-wallet-rpc \
--rpc-bind-port 18082 \
--daemon-host xmr.surveillance.monster:443 \
--wallet-file /root/Downloads/monero-x86_64-linux-gnu-v0.18.3.4/kabus-wallet \
--prompt-for-password \
--trusted-daemon \
--daemon-ssl-allow-any-cert \
--log-file logs/monero-wallet-rpc.log \
--log-level 1 \
--disable-rpc-login
```

## Optional: Using Screen Sessions
If you're using a CLI-based system, you can run the wallet RPC in a screen session:

Create a new screen session:
```bash
screen -S kabus_session
```

Run the wallet RPC command in this session, then detach by pressing CTRL+A+D.

To reattach to the session later:
```bash
screen -r kabus_session
```

## Important Notes
- Never share your wallet's seed phrase with anyone
- Always properly close the wallet RPC using CTRL+C when needed
- Consider using Tor or I2P networks for enhanced privacy when connecting to remote nodes

Your Monero wallet RPC should now be properly configured and ready to use with your Kabus marketplace installation.
