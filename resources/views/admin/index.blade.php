@extends('layouts.app')

@section('content')

<div class="admin-panel-container">
    <div class="admin-panel-card">
        <h1 class="admin-panel-title">Admin Panel</h1>
        <p class="admin-panel-welcome">Welcome to the Admin Panel. Here you can manage various aspects of {{ config('app.name') }}.</p>
        
        <div class="admin-panel-grid">
            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">👤 User Management 👤</h3>
                <p class="admin-panel-item-description">View and manage user accounts, roles, and permissions.</p>
                <a href="{{ route('admin.users') }}" class="admin-panel-item-link">Manage Users</a>
            </div>
            
            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">📝 Manage Products 📝</h3>
                <p class="admin-panel-item-description">Edit or remove products in the market.</p>
                <a href="{{ route('admin.all-products') }}" class="admin-panel-item-link">Product Management</a>
            </div>
            
            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">⏳ Support Requests ⏳</h3>
                <p class="admin-panel-item-description">View and respond to user support requests.</p>
                <a href="{{ route('admin.support.requests') }}" class="admin-panel-item-link">Manage Requests</a>
            </div>
            
            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">📢 Bulk Message 📢</h3>
                <p class="admin-panel-item-description">Send bulk messages to all users or specific roles.</p>
                <a href="{{ route('admin.bulk-message.list') }}" class="admin-panel-item-link">Send Message</a>
            </div>

            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">🥊 Disputes ⛔</h3>
                <p class="admin-panel-item-description">View and respond to user disputes for both sides.</p>
                <a href="#" class="admin-panel-item-link">View Disputes</a>
            </div>

            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">🗂️ Categories 🗂️</h3>
                <p class="admin-panel-item-description">Add, remove, or modify site categories.</p>
                <a href="{{ route('admin.categories') }}" class="admin-panel-item-link">View Categories</a>
            </div>
            
            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">🗄️ System Logs 🗄️</h3>
                <p class="admin-panel-item-description">Access and analyze system logs for security and performance.</p>
                <a href="{{ route('admin.logs') }}" class="admin-panel-item-link">View Logs</a>
            </div>
            
            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">📅 Canary 📅</h3>
                <p class="admin-panel-item-description">Update the current canary of {{ config('app.name') }} with a signed message.</p>
                <a href="{{ route('admin.canary') }}" class="admin-panel-item-link">Update Canary</a>
            </div>

            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">📧 Vendor Applications ⛔</h3>
                <p class="admin-panel-item-description">Review and approve/deny vendor applications requiring verification.</p>
                <a href="#" class="admin-panel-item-link">Manage Applications</a>
            </div>

            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">🚀 Ad Applications ⛔</h3>
                <p class="admin-panel-item-description">Manage advertising requests and from vendors.</p>
                <a href="#" class="admin-panel-item-link">Review Ads</a>
            </div>

            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">🌐 Web Pop-Up ⛔</h3>
                <p class="admin-panel-item-description">Create and manage website-wide pop-up notifications.</p>
                <a href="#" class="admin-panel-item-link">Configure Popups</a>
            </div>

            <div class="admin-panel-item">
                <div class="admin-panel-item-icon">
                </div>
                <h3 class="admin-panel-item-title">📊 Statistics ⛔</h3>
                <p class="admin-panel-item-description">Access platform analytics and performance metrics dashboards.</p>
                <a href="#" class="admin-panel-item-link">View Stats</a>
            </div>
        </div>
    </div>
</div>
@endsection
