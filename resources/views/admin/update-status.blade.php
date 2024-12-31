@extends('layouts.app')

@section('content')
<div class="update-status-container">
    <div class="update-status-card">
        <h2 class="update-status-title">{{ __('Kabus Market Status') }}</h2>

        @if (session('success'))
            <div class="update-status-alert update-status-alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.update-status.post') }}" class="update-status-form">
            @csrf

            <div class="update-status-form-group text-center">
                <label for="status" class="update-status-label">{{ __('Current Status') }}</label>
                <textarea id="status" class="update-status-textarea @error('status') is-invalid @enderror" name="status" required rows="5">{{ old('status', $currentStatus) }}</textarea>

                @error('status')
                    <span class="update-status-invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="update-status-form-group text-center">
                <button type="submit" class="update-status-btn">
                    {{ __('Update Status') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
