<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/kabus.png') }}">
    <link href="{{ asset('css/brain.css') }}" rel="stylesheet">

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
                <a href="{{ route('pgp-key') }}" class="footer-button">PGP Key</a>
                @if (config('marketplace.show_javascript_warning'))
                    <div class="javascript-warning-left js-warning-elements">
                        <span class="javascript-warning-text-left">Please Disable JavaScript</span>
                    </div>
                @endif
                <div class="xmr-price">
                    <span class="xmr-price-label">XMR/USD:</span>
                    <span
                        class="xmr-price-value {{ app(App\Http\Controllers\XmrPriceController::class)->getXmrPrice() === 'UNAVAILABLE' ? 'unavailable' : '' }}">
                        @php
                            $xmrPrice = app(App\Http\Controllers\XmrPriceController::class)->getXmrPrice();
                        @endphp
                        @if ($xmrPrice !== 'UNAVAILABLE')
                            ${{ $xmrPrice }}
                        @else
                            {{ $xmrPrice }}
                        @endif
                    </span>
                </div>

                @if (config('marketplace.show_javascript_warning'))
                    <div class="javascript-warning-right js-warning-elements">
                        <img src="{{ asset('images/javascript-logo.png') }}" alt="JavaScript Logo"
                            class="javascript-warning-icon">
                        <span class="javascript-warning-text-right">Warning</span>
                        <img src="{{ asset('images/javascript-warning.gif') }}" alt="JavaScript Warning"
                            class="javascript-warning-gif">
                    </div>
                @endif
                <a href="{{ route('canary') }}" class="footer-button">Canary</a>
            </div>
        </div>
    </footer>
    @if (config('marketplace.show_javascript_warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.js-warning-elements').forEach(function(element) {
                    element.style.display = 'flex';
                });

                const hamburgerToggle = document.getElementById('hamburger-toggle');
                const mobileSidebar = document.getElementById('mobile-sidebar');
                const overlay = document.getElementById('mobile-sidebar-overlay');

                hamburgerToggle.addEventListener('click', () => {
                    console.log('Hamburger button clicked');
                    mobileSidebar.classList.remove('hidden');
                    overlay.classList.remove('hidden');
                });

                overlay.addEventListener('click', () => {
                    console.log('Overlay clicked');
                    mobileSidebar.classList.add('hidden');
                    overlay.classList.add('hidden');
                });
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerToggle = document.getElementById('hamburger-toggle');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-sidebar-overlay');

            hamburgerToggle.addEventListener('click', () => {
                mobileSidebar.classList.remove('hidden');
                overlay.classList.remove('hidden');
            });

            overlay.addEventListener('click', () => {
                mobileSidebar.classList.add('hidden');
                overlay.classList.add('hidden');
            });
        });
    </script>
</body>

</html>
