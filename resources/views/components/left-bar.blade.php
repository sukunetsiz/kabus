<div class="left-bar">
    <ul>
        <li>
            <a href="{{ route('wishlist.index') }}" class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/wishlist.png') }}" alt="Wishlist" class="left-bar-right-bar-icon left-bar-icon">
                Wishlist
            </a>
        </li>
        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/products.png') }}" alt="Products" class="left-bar-right-bar-icon left-bar-icon">
                Products
            </a>
        </li>
        <li>
            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/orders.png') }}" alt="Orders" class="left-bar-right-bar-icon left-bar-icon">
                Orders
            </a>
        </li>
        <li>
            <a href="{{ route('return-addresses.index') }}" class="{{ request()->routeIs('return-addresses.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/return-addresses.png') }}" alt="Addresses" class="left-bar-right-bar-icon left-bar-icon">
                Addresses
            </a>
        </li>
        <li>
            <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/vendors.png') }}" alt="Vendors" class="left-bar-right-bar-icon left-bar-icon">
                Vendors
            </a>
        </li>
        <li>
            <a href="{{ route('become.vendor') }}" class="{{ request()->routeIs('become.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/become-vendor.png') }}" alt="Become Vendor" class="left-bar-right-bar-icon left-bar-icon">
                Be a Vendor
            </a>
        </li>
        <li>
            <a href="{{ route('references.index') }}" class="{{ request()->routeIs('references.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/references.png') }}" alt="References" class="left-bar-right-bar-icon left-bar-icon">
                References
            </a>
        </li>
        <li>
            <a href="{{ route('disputes.index') }}" class="{{ request()->routeIs('disputes.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/disputes.png') }}" alt="Disputes" class="left-bar-right-bar-icon left-bar-icon">
                Disputes
            </a>
        </li>
        @if(auth()->user()->isAdmin())
        <li>
            <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/a-panel.png') }}" alt="Admin Panel" class="left-bar-right-bar-icon left-bar-icon">
                A-Panel
            </a>
        </li>
        @endif
    </ul>
</div>
