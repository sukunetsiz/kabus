<nav class="navbar">
    <div class="container">
        <div class="navbar-content">
            <div class="navbar-left">
                @auth
                    <a href="{{ route('home') }}" class="btn-nav btn-home">🏠 Home</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-register">📝 Register</a>
                @endauth
            </div>
            
            <div class="navbar-center">
                <span class="navbar-brand">
                    @auth
                        <a href="#" class="icon-btn"><span class="nav-icon">🛒</span></a>
                    @endauth
                    <span class="brand-text">
                        Kabus
                    <img src="{{ asset('favicon.ico') }}" alt="Logo" class="brand-logo">
                        Market
                    </span>
                    @auth
                        <a href="{{ route('notifications.index') }}" class="icon-btn"><span class="nav-icon">🔔</span></a>
                    @endauth
                </span>
            </div>
            
            <div class="navbar-right">
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-nav btn-logout">Logout 🚪</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-login">Login 💼</a>
                @endauth
            </div>
        </div>
    </div>
</nav>