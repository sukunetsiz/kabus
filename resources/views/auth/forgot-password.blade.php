@extends('layouts.app')

@section('content')
<div class="container">
    <div class="auth-container">
        <h2>Forgot Password</h2>
            @if ($errors->has('error'))
                <div class="alert alert-error" role="alert" style="text-align: center;">
                    {{ $errors->first('error') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.verify') }}" class="auth-form">
                @csrf
                <div class="form-group text-center">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" minlength="4" maxlength="16" required autofocus>
                    @error('username')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group text-center">
                    <label for="mnemonic">12-Word Mnemonic Phrase</label>
                    <input type="text" name="mnemonic" id="mnemonic" class="form-control" value="{{ old('mnemonic') }}" minlength="40" maxlength="512" required>
                    @error('mnemonic')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">
                    Verify Mnemonic Phrase
                </button>
            </form>
    </div>
</div>
@endsection