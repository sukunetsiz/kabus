@extends('layouts.app')

@section('content')
<div class="user-details-container">
    <div class="user-details-card">
        <div class="user-details-header">
            <h2 class="user-details-username">{{ $user->username }}</h2>
            <p class="user-details-id">ID: {{ $user->id }}</p>
        </div>
        <div class="user-details-body">
            <div class="user-details-grid">
                <div class="user-details-column">
                    <div class="user-details-info">
                        <p><strong>Reference ID:</strong> {{ $user->reference_id }}</p>
                        <p><strong>Used Reference Code:</strong> {{ $user->referred_by ? 'Yes' : 'No' }}</p>
                        @if($user->referred_by)
                            <p><strong>Referred By:</strong> {{ $user->referrer->username }}</p>
                        @endif
                        <p><strong>Last Login:</strong> {{ $user->last_login ? $user->last_login->format('Y-m-d H:i:s') : 'N/A' }}</p>
                        <p><strong>Account Creation Date:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    
                    <form action="{{ route('admin.users.update-roles', $user) }}" method="POST" class="user-details-form">
                        @csrf
                        @method('PUT')
                        <h3 class="user-details-subtitle">Roles</h3>
                        <div class="user-details-roles">
                            <div class="user-details-role">
                                <input type="checkbox" name="roles[]" value="admin" id="adminRole" {{ $user->hasRole('admin') ? 'checked' : '' }}>
                                <label for="adminRole">Administrator</label>
                            </div>
                            <div class="user-details-role">
                                <input type="checkbox" name="roles[]" value="vendor" id="vendorRole" {{ $user->hasRole('vendor') ? 'checked' : '' }}>
                                <label for="vendorRole">Vendor</label>
                            </div>
                        </div>
                        <button type="submit" class="user-details-btn user-details-btn-primary">Save Changes</button>
                    </form>
                </div>
                
                <div class="user-details-column">
                    @if ($user->isBanned())
                        <div class="user-details-ban-info">
                            <h3 class="user-details-subtitle">Ban Information</h3>
                            <p><strong>Banned Until:</strong> {{ $user->bannedUser->banned_until->format('Y-m-d H:i:s') }}</p>
                            <p><strong>Reason:</strong> {{ $user->bannedUser->reason }}</p>
                            <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="user-details-btn user-details-btn-success">Remove Ban</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="user-details-form">
                            @csrf
                            <h3 class="user-details-subtitle">Ban User</h3>
                            <div class="user-details-input-group">
                                <label for="reason">Ban Reason</label>
                                <input type="text" id="reason" name="reason" required>
                            </div>
                            <div class="user-details-input-group">
                                <label for="duration">Ban Duration (in Days)</label>
                                <input type="number" id="duration" name="duration" min="1" required>
                            </div>
                            <button type="submit" class="user-details-btn user-details-btn-danger">Ban User</button>
                        </form>
                    @endif
                </div>
            </div>
            
            <div class="user-details-actions">
                <a href="{{ route('dashboard', ['username' => $user->username]) }}" class="user-details-btn user-details-btn-info" target="_blank">View User Profile</a>
                <a href="{{ route('admin.users') }}" class="user-details-btn user-details-btn-secondary">Back to User List</a>
            </div>
        </div>
    </div>
</div>

@endsection
