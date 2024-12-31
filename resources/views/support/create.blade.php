@extends('layouts.app')

@section('content')

<div class="support-create-container">
    <div class="support-create-card">
        <h1 class="support-create-title">Create Support Request</h1>

        <form action="{{ route('support.store') }}" method="POST">
            @csrf

            <div class="support-create-form-group">
                <label for="title" class="support-create-label">Title</label>
                <input type="text" name="title" id="title" required
                    class="support-create-input @error('title') is-invalid @enderror"
                    value="{{ old('title') }}"
                    placeholder="Enter the subject of your support request">
                @error('title')
                    <p class="support-create-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="support-create-form-group">
                <label for="message" class="support-create-label">Message</label>
                <textarea name="message" id="message" required
                    class="support-create-textarea @error('message') is-invalid @enderror"
                    placeholder="Explain your issue in detail">{{ old('message') }}</textarea>
                @error('message')
                    <p class="support-create-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="support-create-form-group">
                <div class="support-create-captcha-container">
                    <div class="support-create-captcha-label">CAPTCHA</div>
                    <img class="support-create-captcha-image" src="{{ new Mobicms\Captcha\Image($captchaCode) }}" alt="CAPTCHA">
                    <input type="text" name="captcha" id="captcha" required
                        class="support-create-captcha-input @error('captcha') is-invalid @enderror">
                </div>
                @error('captcha')
                    <p class="support-create-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="support-create-actions">
                <a href="{{ route('support.index') }}" class="support-create-cancel">
                    Cancel
                </a>
                <button type="submit" class="support-create-submit">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection