@extends('layouts.app')

@section('content')

<div class="bulk-message-create-container">
    <div class="bulk-message-create-card text-center">
        <h1 class="bulk-message-create-title">Send Bulk Message</h1>
        
        <form action="{{ route('admin.bulk-message.send') }}" method="POST" class="bulk-message-create-form">
            @csrf
            
            <div class="bulk-message-create-form-group">
                <label for="title" class="bulk-message-create-label">Message Title</label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="bulk-message-create-input"
                       required>
                @error('title')
                    <p class="bulk-message-create-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="bulk-message-create-form-group">
                <label for="message" class="bulk-message-create-label">Message Content</label>
                <textarea name="message" 
                          id="message" 
                          class="bulk-message-create-textarea"
                          required></textarea>
                @error('message')
                    <p class="bulk-message-create-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="bulk-message-create-form-group">
                <label for="target_role" class="bulk-message-create-label">Target User Group</label>
                <select name="target_role" 
                        id="target_role" 
                        class="bulk-message-create-select">
                    <option value="">All Users</option>
                    <option value="admin">Only Administrators</option>
                    <option value="vendor">Only Vendors</option>
                </select>
                @error('target_role')
                    <p class="bulk-message-create-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="bulk-message-create-actions">
                <a href="{{ route('admin.bulk-message.list') }}" 
                   class="bulk-message-create-button bulk-message-create-button-cancel">
                    Cancel
                </a>
                <button type="submit" 
                        class="bulk-message-create-button bulk-message-create-button-submit">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
