<nav class="navbar">
    <div class="container">
        <div class="navbar-content">
            <div class="navbar-left">
                @auth
                    <a href="{{ route('home') }}" class="btn-nav btn-home">🏠 Home</a>
                @endauth
            </div>
            
            <div class="navbar-center">
                <span class="navbar-brand">
                    @auth
                        <a href="{{ route('cart.index') }}" class="left-icon-btn">
                            <span class="left-nav-icon">🛒</span>
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="left-icon-btn-cart-badge">{{ auth()->user()->cartItems()->count() }}</span>
                            @endif
                        </a>
                    @endauth
                    <span class="brand-gap">
                    <img src="{{ asset('images/kabus.png') }}" alt="Logo" class="brand-logo">
                    </span>
                    @auth
                        <a href="{{ route('notifications.index') }}" class="right-icon-btn">
                            <span class="right-nav-icon">🔔</span>
                            @if(auth()->user()->unread_notifications_count > 0)
                                <span class="right-icon-btn-notification-badge">{{ auth()->user()->unread_notifications_count }}</span>
                            @endif
                        </a>
                    @endauth
                </span>
            </div>
            
            <div class="navbar-right">
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-nav btn-logout">Logout 🚪</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
