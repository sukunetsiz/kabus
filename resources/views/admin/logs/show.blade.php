@extends('layouts.app')

@section('content')
<div class="admin-logs-container text-center">
    <a href="{{ route('admin.logs') }}" class="admin-logs-back-btn">Return to Logs</a>

    <div class="admin-logs-content">
        @forelse ($logs as $log)
            <div class="admin-logs-card">
                <div class="admin-logs-card-header">
                    <span class="admin-logs-datetime">{{ $log['datetime'] }}</span>
                    <span class="admin-logs-type admin-logs-type-{{ strtolower($log['type']) }}">{{ $log['type'] }}</span>
                </div>
                <div class="admin-logs-card-body">
                    <pre class="admin-logs-message">{{ $log['message'] }}</pre>
                </div>
            </div>
        @empty
            <div class="admin-logs-empty">
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
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection
