@extends('layouts.app')
@section('content')

<div class="logs-show-container">
    <div class="logs-show-header">
        <a href="{{ route('admin.logs') }}" class="logs-show-back-link">Return to Logs</a>
        <h2 class="logs-show-title">{{ ucfirst($type) }} Logs</h2>
    </div>
    
    <div class="logs-show-search-form">
        <form action="{{ route('admin.logs.show', $type) }}" method="GET">
            <div class="logs-show-search-container">
                <input type="text" name="search" placeholder="Search in logs..." value="{{ $searchQuery ?? '' }}" class="logs-show-search-input">
                <button type="submit" class="logs-show-search-button">Search</button>
                @if(isset($searchQuery) && $searchQuery)
                    <a href="{{ route('admin.logs.show', $type) }}" class="logs-show-clear-link">Clear</a>
                @endif
            </div>
        </form>
    </div>
    
    <form action="{{ route('admin.logs.delete-selected', $type) }}" method="POST" id="logsDeleteForm" class="logs-show-delete-form">
        @csrf
        @method('DELETE')
        <div>
            <button type="submit" class="logs-show-delete-button">
                Delete Selected
            </button>
        </div>
        <div class="logs-show-list">
        @forelse ($logs as $log)
            <div class="logs-show-item">
                <div class="logs-show-item-header">
                    <div class="logs-show-checkbox-container">
                        <input type="checkbox" name="selected_logs[]" value="{{ $log['id'] }}" class="logs-show-checkbox">
                    </div>
                    <div class="logs-show-meta">
                        <span class="logs-show-datetime">{{ $log['datetime'] }}</span>
                        <span class="logs-show-type logs-show-type-{{ strtolower($log['type']) }}">{{ $log['type'] }}</span>
                    </div>
                </div>
                <div class="logs-show-message-container">
                    <pre class="logs-show-message">{{ $log['message'] }}</pre>
                </div>
            </div>
        @empty
            <div class="logs-show-empty">
                <p>
                    @switch($type)
                        @case('error')
                            No error logs found.
                            @break
                        @case('warning')
                            No warning logs found.
                            @break
                        @case('info')
                            No information logs found.
                            @break
                    @endswitch
                    @if(isset($searchQuery) && $searchQuery)
                        matching "{{ $searchQuery }}".
                    @endif
                </p>
            </div>
        @endforelse
        </div>
    </form>
</div>
@endsection
