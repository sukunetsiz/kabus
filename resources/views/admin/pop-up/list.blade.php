@extends('layouts.app')

@section('content')

<div class="admin-pop-up-index-container">
    <div class="admin-pop-up-index-alerts">
        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
         @endif
    </div>
    <div class="admin-pop-up-index-card">
        <div class="admin-pop-up-index-header">
            <h3 class="admin-pop-up-index-title">Popup Management</h3>
            <a href="{{ route('admin.popup.create') }}" class="admin-pop-up-index-create-btn">
                Create New
            </a>
        </div>

        <div class="table-responsive">
            <table class="admin-pop-up-index-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Preview</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popups as $popup)
                        <tr>
                            <td>{{ $popup->title }}</td>
                            <td>{{ Str::limit($popup->message, 40) }}</td>
                            <td>
                                <span class="admin-pop-up-index-status-badge {{ $popup->active ? 'admin-pop-up-index-status-active' : 'admin-pop-up-index-status-inactive' }}">
                                    {{ $popup->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $popup->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="admin-pop-up-index-actions">
                                    @if(!$popup->active)
                                        <form action="{{ route('admin.popup.activate', $popup) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="admin-pop-up-index-action-btn admin-pop-up-index-action-activate">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.popup.destroy', $popup) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-pop-up-index-action-btn admin-pop-up-index-action-delete">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="admin-pop-up-index-empty-state">
                                <p class="mb-0">No popup messages found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($popups->hasPages())
            <div class="admin-pop-up-index-pagination">
                {{ $popups->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
