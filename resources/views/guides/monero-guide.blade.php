@extends('layouts.app')

@section('content')
<div class="main-content-inner">
    <div class="guides-general-container">
        <div class="guides-general-card">
            <div class="guides-general-header">
                <h1 class="guides-general-title">Monero User Guide</h1>
            </div>
            <div class="guides-general-content">
                <p>Monero is a cryptocurrency designed for private and censorship-resistant transactions. While many other cryptocurrencies like Bitcoin and Ethereum have transparent blockchains where transactions can be tracked, Monero prioritizes user privacy. This means that the identities of senders and receivers, as well as transaction amounts, remain confidential.</p>

                <h2 class="guides-general-section-title">[----[1] CORE FEATURES OF MONERO [1]----]</h2>
                <p>Monero uses several different technologies to ensure user anonymity:</p>
                <ul class="guides-general-list">
                    <li>Stealth Addresses: These provide one-time addresses for each transaction, preventing transactions from being linked to users.</li>
                    <li>Ring Signatures: This technology mixes the sender's address with others, making it difficult to identify the real sender.</li>
                    <li>Ring Confidential Transactions: This feature adds an extra layer of privacy by hiding transaction amounts.</li>
                </ul>

                <div class="guides-general-highlight">
                    As a result, Monero transactions are private and nearly untraceable, making it a truly fungible currency. Merchants and users don't need to worry about accepting "tainted" coins because all Monero coins are treated equally and are indistinguishable from each other.
                </div>

                <h2 class="guides-general-section-title">[----ADVANTAGES OF USING MONERO----]</h2>
                <p>Monero offers fast and inexpensive payments worldwide; there are no wire transfer fees, delays, or refund processes. Its decentralized structure is not limited by legal jurisdictions and provides users security from capital controls.</p>

                <p>To use these high privacy and security features of Monero most efficiently, it's important to choose the right wallet software. Feather Wallet is a user-friendly wallet application that makes it easy for Monero users to send, receive, and securely store Monero while maintaining transaction privacy. Feather Wallet is designed with privacy as a priority and offers secure, fast, and practical usage in Windows environment. Now, let's see step by step how to download and install Feather Wallet, how to make Monero transactions, and how to manage your wallet.</p>

                <h2 class="guides-general-section-title">[----[2] DOWNLOADING AND INSTALLING FEATHER WALLET [2]----]</h2>

                <h3 class="guides-general-subtitle">[----FOR WINDOWS----]</h3>
                <ol class="guides-general-ordered-list">
                    <li>Go to the Feather Wallet website.</li>
                    <li>Click the Download button at the top of the page.</li>
                    <li>Scroll down on the new page to find the Windows installation file and click the Installer button.</li>
                    <div class="guides-general-image-container">
                        <img src="{{ asset('images/guides/monero/1.png') }}" alt="Windows Download Screen" class="guides-general-image">
                    </div>
                    <li>After the download completes, go to the Downloads folder.</li>
                    <li>Right-click on the Feather Wallet file and click Open.</li>
                    <li>If Microsoft Defender shows a warning, continue by clicking Run.</li>
                    <li>Select Yes when asked to install Feather Wallet.</li>
                    <li>Leave the installation folder at default settings and click Next.</li>
                    <li>Click Install and wait for the process to complete.</li>
                    <li>Finally, click Next and then Finish. Make sure Run Feather Wallet is active.</li>
                </ol>

                <h3 class="guides-general-subtitle">[----FOR LINUX----]</h3>
                <ol class="guides-general-ordered-list">
                    <li>Go to the Feather Wallet website.</li>
                    <li>Click the Download button at the top of the page.</li>
                    <li>Find Linux options on the opened page. Look for the x64 version and click AppImage.</li>
                    <div class="guides-general-image-container">
                        <img src="{{ asset('images/guides/monero/2.png') }}" alt="Linux Download Screen" class="guides-general-image">
                    </div>
                    <li>After downloading, go to the folder containing the file.</li>
                    <li>Right-click on the AppImage file and go to Properties > Permissions tab. Enable the Executable option.</li>
                    <li>Double-click the AppImage file to open the program.</li>
                </ol>

                <h2 class="guides-general-section-title">[----[3] CREATING A NEW MONERO WALLET [3]----]</h2>
                <p>When Feather Wallet opens, click Create new wallet.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/3.png') }}" alt="Create New Wallet Screen" class="guides-general-image">
                </div>

                <p>Continue by clicking Next.</p>

                <p>Feather Wallet will show "seed words" for your new Monero wallet. Write these words down in a secure place and don't store them digitally; write them by hand on paper in the correct order and keep them safe. These seed words:</p>

                <div class="guides-general-highlight">
                (1) Don't share with anyone.
                </div>

                <div class="guides-general-highlight">
                (2) Don't enter on any website.
                </div>

                <div class="guides-general-highlight">
                (3) Store securely.
                </div>

                <div class="guides-general-highlight">
                (4) Never lose them.
                </div>

                <p>On the next screen, give your wallet a name. You don't need to change the default folder; click Next.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/4.png') }}" alt="Wallet Name Screen" class="guides-general-image">
                </div>

                <p>Choose whether you want to add a password to your wallet. You can add a password or leave it blank and click Next. Finally, click Create/Open wallet to complete the process.</p>

                <h2 class="guides-general-section-title">[----[4] USING FEATHER WALLET [4]----]</h2>
                <p>Now, I'll show you how to use your Monero wallet with Feather Wallet on your computer. With this wallet software, you can send, receive, and store Monero.</p>

                <h3 class="guides-general-subtitle">[----BASIC SETTINGS----]</h3>
                <p>Launch Feather Wallet. You'll see a screen with "Open wallet file" option active. Click Next.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/5.png') }}" alt="Open Wallet Screen" class="guides-general-image">
                </div>

                <p>Select your Monero wallet file. You can have multiple wallet files on your computer. If you want this wallet to load automatically every time you open (not mandatory), enable the "Open on startup" option and click Open wallet.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/6.png') }}" alt="Select Wallet Screen" class="guides-general-image">
                </div>

                <p>Enter your password. Enter the password you set when setting up the wallet and click OK.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/7.png') }}" alt="Enter Password Screen" class="guides-general-image">
                </div>

                <p>Check the main window. When Feather Wallet's main window opens, make sure two indicators are present to confirm your wallet is ready for use:</p>

                <div class="guides-general-highlight">
                   "Synchronized" text in the bottom left corner,
                </div>
                <div class="guides-general-highlight">
                   A green circle symbol in the bottom right corner.
                </div>

                <p>To configure settings: Click the File button in the top left corner and select Settings from the menu. In this screen, you can open a new wallet, restore an existing wallet, lock or close the wallet. For beginners, it might be better not to change the settings section.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/8.png') }}" alt="Settings Screen" class="guides-general-image">
                </div>

                <h2 class="guides-general-section-title">[----[5] RECEIVING MONERO [5]----]</h2>

                <p>Switch to the "Receive" tab in the main screen. Feather Wallet presents your wallet addresses in this screen. Each of these addresses is linked to your Monero wallet, and payments made to these addresses go directly to your wallet.</p>

                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/9.png') }}" alt="Receive Screen" class="guides-general-image">
                </div>

                <p>Label your addresses. You can assign labels to your addresses to note which address you use for what purpose. You can copy these addresses by right-clicking and paste them somewhere.</p>

                <p>View payments in the "History" tab. Details about incoming payments are as follows:</p>

                <ul class="guides-general-list">
                   <li>Date: Payment date</li>
                   <li>Description: Description you set in your wallet</li>
                   <li>Amount: Amount of Monero</li>
                </ul>

                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/10.png') }}" alt="History Screen" class="guides-general-image">
                </div>

                <p>Wait for transactions to confirm. Incoming Monero transactions remain "unconfirmed" until confirmed by the Monero network. According to the Monero protocol, transactions need 10 confirmations. This process can take 20-30 minutes on average, during which you cannot use the coins that are waiting as "unconfirmed".</p>

                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/11.png') }}" alt="Unconfirmed Transaction" class="guides-general-image">
                </div>

                <p>Check transaction status. In the "History" tab, you can track the confirmation status of your incoming transaction. The clock symbol on the left gradually changes from red to green and becomes completely green when the transaction is confirmed.</p>

                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/12.png') }}" alt="Transaction Status 1" class="guides-general-image">
                </div>
                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/13.png') }}" alt="Transaction Status 2" class="guides-general-image">
                </div>
                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/14.png') }}" alt="Transaction Status 3" class="guides-general-image">
                </div>

                <div class="guides-general-highlight">
                   When the transaction is fully confirmed, the Monero appears in your wallet irreversibly. The green checkmark indicates the transaction is complete.
                </div>

                <h2 class="guides-general-section-title">[----[6] SENDING MONERO [6]----]</h2>

                <p>Switch to the "Send" tab in the main screen. Fill in the following fields to send Monero:</p>

                <div class="guides-general-highlight">
                   Pay to: Recipient's Monero address
                </div>
                <div class="guides-general-highlight">
                   Description: A note summarizing the purpose of the transaction (stored in your wallet)
                </div>
                <div class="guides-general-highlight">
                   Amount: Amount of Monero you want to send
                </div>

                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/15.png') }}" alt="Send Screen" class="guides-general-image">
                </div>

                <p>Enter the recipient's Monero address. You can copy this from the internet or paste it using a QR code. Two different methods can be used for the address, the first method is the most common:</p>

                <ol class="guides-general-ordered-list">
                   <li>You can manually paste it by copying from the internet or a website</li>
                   <li>If there's a QR code photo, you can scan it automatically using the computer's camera</li>
                </ol>

                <h3 class="guides-general-subtitle">[----EXAMPLE DONATION SENDING----]</h3>

                <p>To donate to the Feather Wallet developer, click Help > Donate to Feather in the top menu. Feather Wallet automatically fills in the recipient address and description fields. All that's left is to enter the amount you want to donate. You don't have to make a donation in this example; it's just provided as an example of sending Monero.</p>

                <div class="guides-general-image-container">
                   <img src="{{ asset('images/guides/monero/16.png') }}" alt="Donation Screen" class="guides-general-image">
                </div>

                <p>Get send confirmation. Check the address and send amount in the transaction summary screen. You can verify by comparing the first and last 5 characters of the address you want to send to. After reviewing the transaction fee, click Send.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/17.png') }}" alt="Confirmation Screen 1" class="guides-general-image">
                </div>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/18.png') }}" alt="Confirmation Screen 2" class="guides-general-image">
                </div>

                <div class="guides-general-highlight">
                    Wait for the transaction to complete. When Feather Wallet shows "Successfully sent 1 transaction(s)", the transaction has been sent to the Monero network. You can see this transaction in the History tab.
                </div>

                <div class="guides-general-note">
                    Just like incoming payments, your sent transaction also needs 10 confirmations. On average, you get one confirmation every 2 minutes on the Monero network.
                </div>

                <p>When the clock symbol in the History tab turns into a green checkmark, the Monero you sent has irreversibly reached the recipient's wallet. This checkmark indicates the transaction is complete.</p>

                <h2 class="guides-general-section-title">[----[7] HOW TO BUY MONERO [7]----]</h2>
                
                <p>Monero (XMR) is the main currency used in the Kabus Marketplace Script and is used for all transactions. In this guide, I'll show you three methods to buy Monero.</p>

                <h3 class="guides-general-subtitle">[----BUYING THROUGH CENTRALIZED EXCHANGES (CEX)----]</h3>
                
                <p>Our first method is buying Monero through CEX (centralized exchanges) with KYC verification. This method isn't highly recommended for privacy since these exchanges require identity verification, though buying is quite simple and straightforward for beginners or users who aren't very tech-savvy. Download the mobile apps of the CEXs I'll list below, create an account, and complete identity verification. Send your local currency (USD, GBP, EUR etc.) to your crypto exchange account and buy Monero directly from your phone. Now you can withdraw your Monero to any Monero address.</p>

                <div class="guides-general-highlight">
                    As of January 2025, here are the KYC centralized exchanges that still allow Monero trading:
                </div>

                <ul class="guides-general-list">
                    <li>Kraken (https://www.kraken.com/) (Region-specific; delisted in the EEA in 2024) (Kraken is the most reliable among KYC centralized exchanges, make this your first choice)</li>
                    <li>KuCoin (https://www.kucoin.com/)</li>
                    <li>Bybit (https://www.bybit.com/)</li>
                    <li>CoinEx (https://www.coinex.com/)</li>
                    <li>MEXC (https://www.mexc.com/)</li>
                </ul>

                <p>This is the easiest, most beginner-friendly, yet least privacy-respecting method. While your Monero transfers can't be tracked due to Monero's structure, the fact that you bought Monero may or may not be tracked by the government or the exchanges. Use them according to your threat model.</p>

                <h3 class="guides-general-subtitle">[----BUYING THROUGH DECENTRALIZED EXCHANGES (DEX)----]</h3>

                <p>Our second method is buying Monero through DEX (decentralized exchanges) without KYC. Unlike the first method, we won't be sending fiat currency directly to an exchange. Instead, we'll trade cryptocurrency you already have (how you obtained it is up to you) through the trusted websites I'll list below. Common cryptocurrencies traded for Monero include Litecoin, Bitcoin, and USDT. Among these, we recommend Litecoin (LTC). Its low transfer fees, popularity, and slight privacy features make it the most attractive option. I'll first list the most popular options and then show you how to make a simple transaction using one of them.</p>

                <div class="guides-general-highlight">
                    Here are the KYC-free decentralized exchanges:
                </div>

                <ul class="guides-general-list">
                    <li>Trocador (https://trocador.app/)</li>
                    <li>eXch (https://exch.cx/)</li>
                    <li>BitcoinVN (https://bitcoinvn.io/)</li>
                    <li>Majestic Bank (https://majesticbank.is/)</li>
                    <li>Exolix (https://exolix.com/) (Trusted but may require KYC for some suspicious transactions, be careful)</li>
                </ul>

                <p>You can find the list of all KYC and non-KYC exchanges here: https://kycnot.me/</p>

                <h3 class="guides-general-subtitle">[----USING TROCADOR WITH LITECOIN----]</h3>

                <p>Now, I will show you how to trade Monero using Litecoin on Trocador. I bought my Litecoin from a KYC CEX, but you may use other methods too.</p>

                <p>First, use the link above to go to Trocador and enter the amount you want to exchange; for example, I want to exchange 0.4 Litecoin.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/19.png') }}" alt="Trocador Exchange Screen" class="guides-general-image">
                </div>

                <p>Then, Trocador will request 3 things from you:</p>

                <div class="guides-general-highlight">
                    Your Monero address (marked in red rectangle in the image): You must provide a valid Monero subaddress to receive your XMR. This is the wallet where you'll receive your coins.
                </div>

                <div class="guides-general-highlight">
                    Return address (marked in green rectangle): This is optional. You can provide a Litecoin address in case something goes wrong with your transaction. The exchange provider will return your coins to this address if needed.
                </div>

                <div class="guides-general-highlight">
                    Exchange provider selection (marked in blue rectangle): You'll need to choose an exchange provider. Remember, Trocador itself is just an organizer that sorts prices from trusted exchanges - you DO NOT directly trade with Trocador. Choose from the providers listed on the right side of the page. FixedFloat is our default choice as it typically offers the best prices for this trade. You can also choose other reliable providers like Alfacash or eXch depending on your needs.
                </div>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/20.png') }}" alt="Trocador Requirements Screen" class="guides-general-image">
                </div>

                <p>Click "Confirm Exchange" to create an order.</p>

                <p>Now you have created an order. You need to open your wallet software (or a custodial exchange's wallet) and send the required Litecoin amount to the shown address.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/monero/21.png') }}" alt="Trocador Confirmation Screen" class="guides-general-image">
                </div>

                <div class="guides-general-note">
                    Now, all you have to do is wait until your Litecoin transaction is confirmed. This may take up to 10 minutes in total. Your Monero will be sent to your wallet address approximately in half an hour.
                </div>

                <h3 class="guides-general-subtitle">[----USING HAVENO (P2P EXCHANGE)----]</h3>

                <p>Our third method is the most privacy-friendly option, though it's not beginner-friendly. We'll use Haveno, a peer-to-peer decentralized exchange, where you can buy Monero from others using cash by mail. While I won't go into full detail in this guide, here's what you need to know:</p>

                <ul class="guides-general-list">
                    <li>First, download and install RetoSwap, which is based on Haveno. RetoSwap isn't a website or centralized service - it's a peer-to-peer trading network that everyone uses. You'll run this software on your own hardware, connecting directly to other RetoSwap users to make trades. Simply visit https://retoswap.com/ to download it.</li>
                    <li>For detailed instructions, you can follow a comprehensive guide written by a respected member of the Monero community known as "Nihilist". He has shared step-by-step instructions on his blog (.onion link): http://blog.nowherejezfoltodf4jiyl6r56jnzintap5vyjlia7fkirfsnfizflqd.onion/opsec/haveno-cashbymail/index.html</li>
                    <li>Since Nihilist has already created detailed explanations for each step, I won't duplicate that information here. His guide will walk you through the entire process.</li>
                </ul>

            </div>
        </div>
    </div>
</div>
@endsection
