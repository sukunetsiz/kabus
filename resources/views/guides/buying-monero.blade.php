@extends('layouts.app')

@section('content')
<div class="main-content-inner">
    <div class="guides-general-container">
        <div class="guides-general-card">
            <div class="guides-general-header">
                <h1 class="guides-general-title">How to Buy Monero (XMR)</h1>
            </div>
            
            <div class="guides-general-content">
                <p>Monero (XMR) is the main currency used in the Kabus Marketplace Script and is used for all transactions. In this guide, I'll show you three methods to buy Monero.</p>

                <h2 class="guides-general-section-title">[----[1] BUYING THROUGH CENTRALIZED EXCHANGES (CEX) [1]----]</h2>
                
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

                <h2 class="guides-general-section-title">[----[2] BUYING THROUGH DECENTRALIZED EXCHANGES (DEX) [2]----]</h2>

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
                    <img src="{{ asset('images/guides/buying-monero/1.png') }}" alt="Trocador Exchange Screen" class="guides-general-image">
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
                    <img src="{{ asset('images/guides/buying-monero/2.png') }}" alt="Trocador Requirements Screen" class="guides-general-image">
                </div>

                <p>Click "Confirm Exchange" to create an order.</p>

                <p>Now you have created an order. You need to open your wallet software (or a custodial exchange's wallet) and send the required Litecoin amount to the shown address.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/buying-monero/3.png') }}" alt="Trocador Confirmation Screen" class="guides-general-image">
                </div>

                <div class="guides-general-note">
                    Now, all you have to do is wait until your Litecoin transaction is confirmed. This may take up to 10 minutes in total. Your Monero will be sent to your wallet address approximately in half an hour.
                </div>

                <h2 class="guides-general-section-title">[----[3] USING HAVENO (P2P EXCHANGE) [3]----]</h2>

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
