<nav class="navbar">
    <div class="container">
        <div class="navbar-content">
            <div class="navbar-left">
                @auth
                    <a href="{{ route('home') }}" class="btn-nav btn-home hide-on-mobile">🏠 Home</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-register">📝 Register</a>
                @endauth
                @auth
                    <!-- Mobile Hamburger Button -->
                    <div class="mobile-hamburger">
                        <button id="hamburger-toggle" class="hamburger-btn left-icon-btn" aria-label="Toggle Menu">
                            ☰
                        </button>
                    </div>
                @endauth
            </div>

            <div class="navbar-center">
                <span class="navbar-brand">
                    @auth
                        <a href="{{ route('cart.index') }}" class="left-icon-btn hide-on-mobile">
                            <span class="left-nav-icon">🛒</span>
                            @if (auth()->user()->cartItems()->count() > 0)
                                <span class="left-icon-btn-cart-badge">{{ auth()->user()->cartItems()->count() }}</span>
                            @endif
                        </a>
                    @endauth
                    <span class="brand-gap">
                        <img src="{{ asset('images/kabus.png') }}" alt="Logo" class="brand-logo">
                    </span>
                    @auth
                        <a href="{{ route('notifications.index') }}" class="right-icon-btn hide-on-mobile">
                            <span class="right-nav-icon">🔔</span>
                            @if (auth()->user()->unread_notifications_count > 0)
                                <span
                                    class="right-icon-btn-notification-badge">{{ auth()->user()->unread_notifications_count }}</span>
                            @endif
                        </a>
                    @endauth
                </span>
            </div>

            <div class="navbar-right">
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-nav btn-logout hide-on-mobile">Logout 🚪</button>
                    </form>

                    {{-- Extra Starts --}}
                    <div class="mobile-navbar-right">
                        <a href="{{ route('notifications.index') }}" class="right-icon-btn mb-alt">
                            <span class="right-nav-icon">🔔</span>
                            @if (auth()->user()->unread_notifications_count > 0)
                                <span
                                    class="right-icon-btn-notification-badge">{{ auth()->user()->unread_notifications_count }}</span>
                            @endif
                        </a>
                        <a href="{{ route('cart.index') }}" class="left-icon-btn mb-alt">
                            <span class="left-nav-icon">🛒</span>
                            @if (auth()->user()->cartItems()->count() > 0)
                                <span class="left-icon-btn-cart-badge">{{ auth()->user()->cartItems()->count() }}</span>
                            @endif
                        </a>
                    </div>
                    {{-- Extra Ends --}}
                @else
                    <a href="{{ route('login') }}" class="btn btn-login">Login 💼</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar & Overlay -->
    <div id="mobile-sidebar-overlay" class="mobile-overlay hidden"></div>

    <div id="mobile-sidebar" class="mobile-sidebar hidden">
        @auth
            <ul class="mobile-sidebar-menu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">🏠
                        Home</a></li>

                <!-- Left bar links -->
                <li><a href="{{ route('wishlist.index') }}"
                        class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}">❤️
                        Wishlist</a></li>
                <li><a href="{{ route('products.index') }}"
                        class="{{ request()->routeIs('products.*') ? 'active' : '' }}">🛍️ Products</a></li>
                <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">📦
                        Orders</a></li>
                <li><a href="{{ route('return-addresses.index') }}"
                        class="{{ request()->routeIs('return-addresses.*') ? 'active' : '' }}">💰 Addresses</a></li>
                <li><a href="{{ route('vendors.index') }}"
                        class="{{ request()->routeIs('vendors.*') ? 'active' : '' }}">📈 Vendors</a></li>
                <li><a href="{{ route('become.vendor') }}" class="{{ request()->routeIs('become.*') ? 'active' : '' }}">🌟
                        Be a Vendor</a></li>
                <li><a href="{{ route('references.index') }}"
                        class="{{ request()->routeIs('references.*') ? 'active' : '' }}">🤝🏻 References</a></li>
                <li><a href="{{ route('disputes.index') }}"
                        class="{{ request()->routeIs('disputes.*') ? 'active' : '' }}">🎭 Disputes</a></li>
                @if (auth()->user()->isAdmin())
                    <li><a href="{{ route('admin.index') }}"
                            class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">👑 AdminPanel</a></li>
                @endif

                <!-- Right bar links -->
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">🖼️
                        Dashboard</a></li>
                <li><a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">⚙️
                        Settings</a></li>
                <li><a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">🖥️
                        Account</a></li>
                <li><a href="{{ route('support.index') }}"
                        class="{{ request()->routeIs('support.*') ? 'active' : '' }}">🛠️ Support</a></li>
                <li><a href="{{ route('messages.index') }}"
                        class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">💬 Messages</a></li>
                <li><a href="{{ route('rules') }}" class="{{ request()->routeIs('rules') ? 'active' : '' }}">⚖️ Rules</a>
                </li>
                <li><a href="{{ route('guides.index') }}" class="{{ request()->routeIs('guides.*') ? 'active' : '' }}">🧭
                        Guides</a></li>
                @if (auth()->user()->isVendor())
                    <li><a href="{{ route('vendor.index') }}"
                            class="{{ request()->routeIs('vendor.*') ? 'active' : '' }}">🏪
                            VendorShop</a></li>
                @endif

                <!-- Cart / Notifications / Logout -->
                {{-- <li><a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">🛒
                        Cart</a></li>
                <li><a href="{{ route('notifications.index') }}"
                        class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">🔔 Notifications</a></li> --}}
                <li class="mobile-logout">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-nav">Logout 🚪</button>
                    </form>
                </li>
            </ul>
        @endauth
    </div>
</nav>
