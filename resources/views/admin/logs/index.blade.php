@extends('layouts.app')

@section('content')
<div class="logs-index-container">
    <h1 class="logs-index-title">System Logs</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="logs-index-grid text-center">
        <div class="logs-index-card logs-index-card-error">
            <h2 class="logs-index-card-title">Error Logs</h2>
            <p class="logs-index-card-description">View Error, Critical, Alert, Emergency logs</p>
            <div class="logs-index-card-actions">
                <a href="{{ route('admin.logs.error') }}" class="logs-index-btn logs-index-btn-view">View Logs</a>
                <form action="{{ route('admin.logs.delete', ['type' => 'error']) }}" method="POST" class="logs-index-delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="logs-index-btn logs-index-btn-delete">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        <div class="logs-index-card logs-index-card-warning">
            <h2 class="logs-index-card-title">Warning Logs</h2>
            <p class="logs-index-card-description">View Warning and Notice logs</p>
            <div class="logs-index-card-actions">
                <a href="{{ route('admin.logs.warning') }}" class="logs-index-btn logs-index-btn-view">View Logs</a>
                <form action="{{ route('admin.logs.delete', ['type' => 'warning']) }}" method="POST" class="logs-index-delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="logs-index-btn logs-index-btn-delete">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        <div class="logs-index-card logs-index-card-info">
            <h2 class="logs-index-card-title">Information Logs</h2>
            <p class="logs-index-card-description">View Info and Debug logs</p>
            <div class="logs-index-card-actions">
                <a href="{{ route('admin.logs.info') }}" class="logs-index-btn logs-index-btn-view">View Logs</a>
                <form action="{{ route('admin.logs.delete', ['type' => 'info']) }}" method="POST" class="logs-index-delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="logs-index-btn logs-index-btn-delete">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
