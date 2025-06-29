<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-content">
            <div class="navbar-left">
                @auth
                    <a href="{{ route('home') }}" class="navbar-btn navbar-btn-home">
                        <img src="{{ asset('icons/home.png') }}" alt="Home" class="navbar-btn-icon">
                        Home
                    </a>
                @endauth
            </div>
            
            <div class="navbar-center">
                <span class="navbar-brand">
                    @auth
                        <a href="{{ route('cart.index') }}" class="navbar-icon-btn {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                            <img src="{{ asset('icons/cart.png') }}" alt="Cart" class="navbar-icon-png">
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="navbar-badge navbar-badge-cart">{{ auth()->user()->cartItems()->count() }}</span>
                            @endif
                        </a>
                    @endauth
                    <span class="navbar-brand-gap">
                        <a href="{{ route('home') }}" class="navbar-logo-link">
                            <img src="{{ asset('images/kabus.png') }}" alt="Logo" class="navbar-brand-logo">
                        </a>
                    </span>
                    @auth
                        <a href="{{ route('notifications.index') }}" class="navbar-icon-btn {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <img src="{{ asset('icons/notifications.png') }}" alt="Notifications" class="navbar-icon-png">
                            @if(auth()->user()->unread_notifications_count > 0)
                                <span class="navbar-badge navbar-badge-notification">{{ auth()->user()->unread_notifications_count }}</span>
                            @endif
                        </a>
                    @endauth
                </span>
            </div>
            
            <div class="navbar-right">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="navbar-btn navbar-btn-logout">
                            Logout
                            <img src="{{ asset('icons/logout.png') }}" alt="Logout" class="navbar-btn-icon navbar-btn-icon-logout">
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
