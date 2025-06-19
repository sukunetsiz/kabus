<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <a href="{{ route('pgp-key') }}" class="footer-button">PGP Key</a>
            @if(config('marketplace.show_javascript_warning'))
                <div class="footer-javascript-warning-left js-warning-elements">
                    <span class="footer-javascript-warning-text-left">Please Disable JavaScript</span>
                </div>
            @endif
            <div class="footer-xmr-price">
                <span class="footer-xmr-price-label">XMR/USD:</span>
                @php
                    $xmrPrice = app(App\Http\Controllers\XmrPriceController::class)->getXmrPrice();
                @endphp
                <span class="footer-xmr-price-value {{ $xmrPrice === 'UNAVAILABLE' ? 'unavailable' : '' }}">
                    @if($xmrPrice !== 'UNAVAILABLE')
                        ${{ $xmrPrice }}
                    @else
                        {{ $xmrPrice }}
                    @endif
                </span>
            </div>
            @if(config('marketplace.show_javascript_warning'))
                <div class="footer-javascript-warning-right js-warning-elements">
                    <img src="{{ asset('images/javascript-logo.png') }}" alt="JavaScript Logo" class="footer-javascript-warning-icon">
                    <span class="footer-javascript-warning-text-right">Warning</span>
                    <img src="{{ asset('images/javascript-warning.gif') }}" alt="JavaScript Warning" class="footer-javascript-warning-gif">
                </div>
            @endif
            <a href="{{ route('canary') }}" class="footer-button">Canary</a>
        </div>
    </div>
</footer>

@if(config('marketplace.show_javascript_warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-warning-elements').forEach(function(element) {
                element.style.display = 'flex';
            });
        });
    </script>
@endif
