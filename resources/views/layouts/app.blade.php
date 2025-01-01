<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

</head>
<body class="dark-mode">
    @include('components.navbar')
    <div class="content-wrapper">
        @auth
            @include('components.left-bar')
        @endauth
        <main class="main-content">
            @yield('content')
        </main>
        @auth
            @include('components.right-bar')
        @endauth
    </div>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <a href="{{ route('kabus-pgp-key') }}" class="footer-button">PGP Key</a>
                </div>
                <div class="xmr-price">
                    <span class="xmr-price-label">XMR/USD:</span>
                    <span class="xmr-price-value {{ app(App\Http\Controllers\XmrPriceController::class)->getXmrPrice() === 'UNAVAILABLE' ? 'unavailable' : '' }}">
                        @php
                            $xmrPrice = app(App\Http\Controllers\XmrPriceController::class)->getXmrPrice();
                        @endphp
                        @if($xmrPrice !== 'UNAVAILABLE')
                            ${{ $xmrPrice }}
                        @else
                            {{ $xmrPrice }}
                        @endif
                    </span>
                </div>
                <div class="footer-right">
                    <a href="{{ route('kabus-current-status') }}" class="footer-button">Current Status</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
