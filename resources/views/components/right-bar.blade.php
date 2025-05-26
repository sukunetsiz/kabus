<div class="right-bar hide-on-mobile">
    <ul>
        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard
                🖼️</a></li>
        <li><a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">Settings ⚙️</a>
        </li>
        <li><a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">Account 🖥️</a>
        </li>
        <li><a href="{{ route('support.index') }}" class="{{ request()->routeIs('support.*') ? 'active' : '' }}">Support
                🛠️</a></li>
        <li><a href="{{ route('messages.index') }}"
                class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages 💬</a></li>
        <li><a href="{{ route('rules') }}" class="{{ request()->routeIs('rules') ? 'active' : '' }}">Rules ⚖️</a></li>
        <li><a href="{{ route('guides.index') }}" class="{{ request()->routeIs('guides.*') ? 'active' : '' }}">Guides
                🧭</a></li>
        @if (auth()->user()->isVendor())
            <li><a href="{{ route('vendor.index') }}"
                    class="{{ request()->routeIs('vendor.*') ? 'active' : '' }}">VendorShop 🏪</a></li>
        @endif
    </ul>
</div>
