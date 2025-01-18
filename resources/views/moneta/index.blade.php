@extends('layouts.app')
@section('content')
<div class="moneta-index-container">
    <div class="moneta-index-card">
        <div class="moneta-index-card-header">
            <h1 class="moneta-index-title">Moneta Block Hash Game</h1>
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
                    <li class="moneta-index-list-item">Choose either the first 32 or last 32 characters of the upcoming Monero block hash for your bet.</li>
                    <li class="moneta-index-list-item">When the block is mined, all letters are removed from your chosen half, and the remaining numbers are added together digit by digit.</li>
                    <li class="moneta-index-list-item">The same process happens with the other half of the block hash.</li>
                    <li class="moneta-index-list-item">You win if your chosen half has the higher sum!</li>
                </ul>
            </div>
            <div class="moneta-index-section">
                <h2 class="moneta-index-section-title text-center">Game Rules & Payouts</h2>
                <ul class="moneta-index-list">
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">Winning Payout:</span> 1.95 times your bet amount</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">Tie Game:</span> Your entire bet is returned</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">Block Selection:</span> Your game is always played on the block that comes after your bet is included in the blockchain</li>
                    <li class="moneta-index-list-item"><span class="moneta-index-highlight">Game Duration:</span> Approximately 2 minutes (average Monero block time)</li>
                </ul>
            </div>
            <div class="moneta-index-section">
                <h2 class="moneta-index-section-title text-center">Fair Game Guarantee</h2>
                <p class="moneta-index-alert-text">
                    This game is powered by the Monero blockchain's random block hashes, making it completely fair and unriggable. Every game can be verified using simple mathematics, and the outcomes are determined by decentralized miners who have no control over the generated block hashes.
                </p>
            </div>
            <div class="moneta-index-action">
                <a href="#" class="moneta-index-button">Place Your Bet</a>
                <a href="#" class="moneta-index-button">View Game History</a>
            </div>
        </div>
    </div>
</div>
@endsection
