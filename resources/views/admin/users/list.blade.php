@extends('layouts.app')

@section('content')
<div class="user-list-container">
    <div class="user-list-card">
        <div class="user-list-table-container">
            <table class="user-list-table">
                <thead>
    <tr>
        <th style="text-align: center;">ID</th>
        <th style="text-align: center;">Username</th>
        <th style="text-align: center;">Last Login</th>
        <th style="text-align: center;">Action</th>
    </tr>
</thead>
<tbody>
    @foreach($users as $user)
        <tr>
            <td style="text-align: center;">{{ $user->id }}</td>
            <td style="text-align: center;">{{ $user->username }}</td>
            <td style="text-align: center;">{{ $user->last_login ? $user->last_login->format('Y-m-d H:i:s') : 'N/A' }}</td>
            <td style="text-align: center;">
                <a href="{{ route('admin.users.details', $user->id) }}" class="user-list-btn user-list-btn-details">User Details</a>
            </td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>
        <div class="user-list-pagination">
            {{ $users->links() }}
        </div>
    </div>
</div>

@endsection
