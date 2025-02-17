@extends('layouts.app')

@section('content')

<div class="admin-statistics-container">
    <div class="admin-statistics-header">
        <h1 class="admin-statistics-title">Marketplace Statistics</h1>
    </div>
    <div class="admin-statistics-grid">
        <!-- User Statistics Card -->
        <div class="admin-statistics-card">
            <h2>User Statistics</h2>
            <p><strong>Total Users:</strong> {{ number_format($totalUsers) }}</p>
            <ul>
                @foreach($usersByRole as $role)
                    <li>
                        <strong>{{ ucfirst($role->name) }}s:</strong> {{ number_format($role->users_count) }} 
                        @if($totalUsers > 0)
                            ({{ number_format(($role->users_count / $totalUsers) * 100, 1) }}%)
                        @endif
                    </li>
                @endforeach
            </ul>
            <p><strong>Currently Banned Users:</strong> {{ number_format($bannedUsersCount) }}
                @if($totalUsers > 0)
                    ({{ number_format(($bannedUsersCount / $totalUsers) * 100, 1) }}%)
                @endif
            </p>
        </div>
        <!-- Security Statistics Card -->
        <div class="admin-statistics-card">
            <h2>Security Statistics</h2>
            <ul>
                <li>
                    <strong>Total PGP Keys:</strong> {{ number_format($totalPgpKeys) }}
                    @if($totalUsers > 0)
                        ({{ number_format(($totalPgpKeys / $totalUsers) * 100, 1) }}% of users)
                    @endif
                </li>
                <li>
                    <strong>Verified PGP Keys:</strong> {{ number_format($verifiedPgpKeys) }}
                    @if($totalPgpKeys > 0)
                        ({{ number_format($pgpVerificationRate, 1) }}% verification rate)
                    @endif
                </li>
                <li>
                    <strong>Non-Verified PGP Keys:</strong> {{ number_format($totalPgpKeys - $verifiedPgpKeys) }}
                    @if($totalPgpKeys > 0)
                        ({{ number_format(100 - $pgpVerificationRate, 1) }}% of total)
                    @endif
                </li>
            </ul>
            <p><strong>2FA Enabled Users:</strong> {{ number_format($twoFaEnabled) }}
                @if($totalUsers > 0)
                    ({{ number_format($twoFaAdoptionRate, 1) }}% adoption rate)
                @endif
            </p>
        </div>
        <!-- Product Statistics Card -->
        <div class="admin-statistics-card">
            <h2>Product Statistics</h2>
            <p><strong>Total Products:</strong> {{ number_format($totalProducts) }}</p>
            <ul>
                <li>
                    <strong>Digital Products:</strong> {{ number_format($productsByType['digital']) }}
                    @if($totalProducts > 0)
                        ({{ number_format(($productsByType['digital'] / $totalProducts) * 100, 1) }}%)
                    @endif
                </li>
                <li>
                    <strong>Cargo Products:</strong> {{ number_format($productsByType['cargo']) }}
                    @if($totalProducts > 0)
                        ({{ number_format(($productsByType['cargo'] / $totalProducts) * 100, 1) }}%)
                    @endif
                </li>
                <li>
                    <strong>Dead Drop Products:</strong> {{ number_format($productsByType['deaddrop']) }}
                    @if($totalProducts > 0)
                        ({{ number_format(($productsByType['deaddrop'] / $totalProducts) * 100, 1) }}%)
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
