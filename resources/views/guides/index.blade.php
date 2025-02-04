@extends('layouts.app')

@section('content')

<div class="guides-index-container">
    <div class="guides-index-card">
        <h1 class="guides-index-title">Guides</h1>
        <p class="guides-index-description">
            Welcome to {{ config('app.name') }}. You can explore our comprehensive guides that we have prepared to help you better use our platform.
        </p>

        <div class="guides-index-grid">
            <div class="guides-index-item">
                <h3 class="guides-index-item-title">🪙 Monero Guide 🪙</h3>
                <p class="guides-index-item-description">
                    Comprehensive information about creating and safely using a Monero wallet.
                </p>
                <a href="{{ route('guides.monero') }}" class="guides-index-item-link">View Guide</a>
            </div>

            <div class="guides-index-item">
                <h3 class="guides-index-item-title">🧅 Tor Guide 🧅</h3>
                <p class="guides-index-item-description">
                    Detailed information about Tor Browser usage and operational security.
                </p>
                <a href="{{ route('guides.tor') }}" class="guides-index-item-link">View Guide</a>
            </div>

            <div class="guides-index-item">
                <h3 class="guides-index-item-title">🔑 KeePassXC Guide 🔑</h3>
                <p class="guides-index-item-description">
                    Guide for using KeePassXC for secure password management.
                </p>
                <a href="{{ route('guides.keepassxc') }}" class="guides-index-item-link">View Guide</a>
            </div>

            <div class="guides-index-item">
                <h3 class="guides-index-item-title">📧 Kleopatra Guide ⛔</h3>
                <p class="guides-index-item-description">
                    Guide for using Kleopatra for PGP key management and encryption.
                </p>
                <a href="#" class="guides-index-item-link">View Guide</a>
            </div>
        </div>
    </div>
</div>
@endsection
