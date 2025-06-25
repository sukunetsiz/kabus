<div class="left-bar">
    <ul>
        <li>
            <a href="{{ route('wishlist.index') }}" class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/wishlist.svg') }}" alt="Wishlist" class="left-bar-right-bar-icon left-bar-icon">
                Wishlist
            </a>
        </li>
        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/products.svg') }}" alt="Products" class="left-bar-right-bar-icon left-bar-icon">
                Products
            </a>
        </li>
        <li>
            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/orders.svg') }}" alt="Orders" class="left-bar-right-bar-icon left-bar-icon">
                Orders
            </a>
        </li>
        <li>
            <a href="{{ route('return-addresses.index') }}" class="{{ request()->routeIs('return-addresses.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/return-addresses.svg') }}" alt="Addresses" class="left-bar-right-bar-icon left-bar-icon">
                Addresses
            </a>
        </li>
        <li>
            <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/vendors.svg') }}" alt="Vendors" class="left-bar-right-bar-icon left-bar-icon">
                Vendors
            </a>
        </li>
        <li>
            <a href="{{ route('become.vendor') }}" class="{{ request()->routeIs('become.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/become-vendor.svg') }}" alt="Become Vendor" class="left-bar-right-bar-icon left-bar-icon">
                Be a Vendor
            </a>
        </li>
        <li>
            <a href="{{ route('references.index') }}" class="{{ request()->routeIs('references.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/references.svg') }}" alt="References" class="left-bar-right-bar-icon left-bar-icon">
                References
            </a>
        </li>
        <li>
            <a href="{{ route('disputes.index') }}" class="{{ request()->routeIs('disputes.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/disputes.svg') }}" alt="Disputes" class="left-bar-right-bar-icon left-bar-icon">
                Disputes
            </a>
        </li>
        @if(auth()->user()->isAdmin())
        <li>
            <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <img src="{{ asset('icons/a-panel.svg') }}" alt="Admin Panel" class="left-bar-right-bar-icon left-bar-icon">
                A-Panel
            </a>
        </li>
        @endif
    </ul>
</div>
