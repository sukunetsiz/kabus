<div class="right-bar">
    <ul>
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
                <img src="{{ asset('icons/dashboard.png') }}" alt="Dashboard" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        <li>
            <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">
                Settings
                <img src="{{ asset('icons/settings.png') }}" alt="Settings" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        <li>
            <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                Account
                <img src="{{ asset('icons/account.png') }}" alt="Account" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        <li>
            <a href="{{ route('support.index') }}" class="{{ request()->routeIs('support.*') ? 'active' : '' }}">
                Support
                <img src="{{ asset('icons/support.png') }}" alt="Support" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        <li>
            <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                Messages
                <img src="{{ asset('icons/messages.png') }}" alt="Messages" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        <li>
            <a href="{{ route('rules') }}" class="{{ request()->routeIs('rules') ? 'active' : '' }}">
                Rules
                <img src="{{ asset('icons/rules.png') }}" alt="Rules" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        <li>
            <a href="{{ route('guides.index') }}" class="{{ request()->routeIs('guides.*') ? 'active' : '' }}">
                Guides
                <img src="{{ asset('icons/guides.png') }}" alt="Guides" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        @if(auth()->user()->isVendor())
        <li>
            <a href="{{ route('vendor.index') }}" class="{{ request()->routeIs('vendor.*') ? 'active' : '' }}">
                V-Panel
                <img src="{{ asset('icons/v-panel.png') }}" alt="Vendor Panel" class="left-bar-right-bar-icon right-bar-icon">
            </a>
        </li>
        @endif
    </ul>
</div>
