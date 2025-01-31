@extends('layouts.app')

@section('content')

<div class="admin-pop-up-create-container">
    <div class="admin-pop-up-create-card">
        <div class="admin-pop-up-create-header">
            <h2 class="admin-pop-up-create-title">Create New Pop-up</h2>
            <a href="{{ route('admin.popup.index') }}" class="admin-pop-up-create-back-btn">
                Back To List
            </a>
        </div>

        <div class="admin-pop-up-create-body">
            {{-- Error Messages --}}
            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.popup.store') }}" method="POST">
                @csrf

                <div class="admin-pop-up-create-title-field">
                    <label class="admin-pop-up-create-label">Pop-up Title</label>
                    <input type="text" 
                           class="admin-pop-up-create-input @error('title') is-invalid @enderror" 
                           name="title" 
                           value="{{ old('title') }}"
                           placeholder="Enter pop-up title"
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="admin-pop-up-create-label">Message Content</label>
                    <textarea class="admin-pop-up-create-input admin-pop-up-create-textarea @error('message') is-invalid @enderror" 
                              name="message" 
                              placeholder="Write your pop-up message here..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="admin-pop-up-create-toggle-group">
                    <label class="admin-pop-up-create-switch">
                        <input type="checkbox" 
                               id="active" 
                               name="active" 
                               value="1" 
                               {{ old('active') ? 'checked' : '' }}>
                        <span class="admin-pop-up-create-slider"></span>
                    </label>
                    <label for="active" class="admin-pop-up-create-label">Activate This Pop-up</label>
                </div>
                <p class="admin-pop-up-create-note">
                    Only one pop-up can be active at a time. Activating this will automatically deactivate others.
                </p>

                <div class="admin-pop-up-create-btn-container">
                    <button type="submit" class="admin-pop-up-create-submit">
                        Publish Pop-up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
