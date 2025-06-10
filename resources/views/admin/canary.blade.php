@extends('layouts.app')

@section('content')
<div class="canary-index-container">
    <div class="canary-index-card">
        <h2 class="canary-index-title">{{ config('app.name') }} Canary</h2>

        <form method="POST" action="{{ route('admin.canary.post') }}" class="canary-index-form">
            @csrf

            <div class="canary-index-form-group text-center">
                <label for="canary" class="canary-index-label">Current Canary</label>
                <textarea id="canary" class="canary-index-textarea @error('canary') is-invalid @enderror" name="canary" required rows="5">{{ old('canary', $currentCanary) }}</textarea>

                @error('canary')
                    <span class="canary-index-invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="canary-index-form-group text-center">
                <button type="submit" class="canary-index-btn">
                    Update Canary
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
