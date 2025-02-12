@extends('layouts.app')

@section('content')
<div class="user-list-container">
    <div class="user-list-card">
        <h2 class="user-list-title">User List</h2>
        <div class="user-list-table-container">
            <table class="user-list-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->last_login ? $user->last_login->format('Y-m-d / H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.users.details', $user->id) }}" class="user-list-btn">User Details</a>
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
