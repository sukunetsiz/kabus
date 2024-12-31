<div class="left-bar">
    <ul>
        <li><a href="{{ route('wishlist.index') }}" class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}">❤️ Wishlist</a></li>
        <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">🛍️ Products ⛔</a></li>
        <li><a href="#" class="{{ request()->routeIs('orders') ? 'active' : '' }}">📦 Orders ⛔</a></li>
        <li><a href="{{ route('return-addresses.index') }}" class="{{ request()->routeIs('return-addresses.index') ? 'active' : '' }}">💰 Addresses</a></li>
        <li><a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') ? 'active' : '' }}">📈 Vendors</a></li>
        <li><a href="{{ route('become.vendor') }}" class="{{ request()->routeIs('become.vendor') ? 'active' : '' }}">🌟 Be a Vendor</a></li>
        <li><a href="{{ route('moneta.index') }}" class="{{ request()->routeIs('moneta.index') ? 'active' : '' }}">🎲 Moneta ⛔</a></li>
        <li><a href="{{ route('references.index') }}" class="{{ request()->routeIs('references.index') ? 'active' : '' }}">🤝🏻 References</a></li>
        @if(auth()->user()->isAdmin())
        <li><a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">👑 AdminPanel</a></li>
        @endif
    </ul>
</div>