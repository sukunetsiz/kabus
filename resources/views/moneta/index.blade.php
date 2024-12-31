@extends('layouts.app')

@section('content')

<div class="moneta-index-container">
    <div class="moneta-index-card">
        <div class="moneta-index-card-header">
            <h1 class="moneta-index-title">Moneta Luck Game</h1>
        </div>

        <div class="moneta-index-card-body">
            <div class="moneta-index-alert">
                <p class="moneta-index-alert-text">
                    To play this game, you must have at least one <span class="moneta-index-highlight">Monero (XMR)</span> address registered in your account. 
                    Please make sure to add your Monero address before starting the game. 
                    If you haven't added an address yet, you can add it from the <a href="{{ route('return-addresses.index') }}"><span class="moneta-index-highlight">Addresses</span></a> tab on the left side of the screen.
                </p>
            </div>

            <div class="moneta-index-section">
                <h2 class="moneta-index-section-title text-center">How to Play</h2>
                <ul class="moneta-index-list">
                    <li class="moneta-index-list-item">To participate in the game, you need to pay any amount of Monero (within minimum and maximum limits).</li>
                    <li class="moneta-index-list-item">After making the payment, you will be shown four completely random cryptocurrency symbols.</li>
                    <li class="moneta-index-list-item">These symbols will be Monero (XMR), Ethereum (ETH), Litecoin (LTC), and Bitcoin (BTC).</li>
                </ul>
            </div>

            <div class="moneta-index-section">
                <h2 class="moneta-index-section-title text-center">Payout Table</h2>
                <ul class="moneta-index-list">
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">4 Monero symbols:</span> Win 20 times your deposit amount!</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">4 identical symbols</span> (Ethereum, Litecoin, or Bitcoin): Your entire deposit is refunded.</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">3 Monero symbols:</span> Your entire deposit is refunded.</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">2 Monero symbols:</span> Approximately 80% of your deposit is refunded.</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">Other combinations:</span> Unfortunately, you lose your deposit.</li>
                </ul>
            </div>

            <div class="moneta-index-action">
                <a href="#" class="moneta-index-button">Proceed to Betting Payment</a>
                <a href="#" class="moneta-index-button">View Game History</a>
            </div>
        </div>
    </div>
</div>
@endsection