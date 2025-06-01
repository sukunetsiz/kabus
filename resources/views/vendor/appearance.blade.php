@extends('layouts.app')

@section('content')

<div class="vendor-panel-appearance-container">
    <div class="vendor-panel-appearance-card">
        <h2 class="vendor-panel-appearance-title">Vendor Appearance Settings</h2>

        @if(session('success'))
            <div class="vendor-panel-appearance-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('vendor.appearance.update') }}" method="POST">
            @csrf
            
            <div class="vendor-panel-appearance-form-group">
                <label class="vendor-panel-appearance-vacation-label">
                    <span>Vacation Mode</span>
                    <label class="vendor-panel-appearance-switch">
                        <input 
                            type="hidden"
                            name="vacation_mode"
                            value="0"
                        >
                        <input 
                            type="checkbox"
                            name="vacation_mode"
                            value="1"
                            {{ $vendorProfile->vacation_mode ? 'checked' : '' }}
                        >
                        <span class="vendor-panel-appearance-slider"></span>
                    </label>
                </label>
                <p class="vendor-panel-appearance-help-text">
                    When enabled, your store will be hidden and customers will see the vacation mode message.
                </p>
                @error('vacation_mode')
                    <p class="vendor-panel-appearance-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="vendor-panel-appearance-form-group">
                <label class="vendor-panel-appearance-vacation-label">
                    <span>Private Shop Mode</span>
                    <label class="vendor-panel-appearance-switch">
                        <input 
                            type="hidden"
                            name="private_shop_mode"
                            value="0"
                        >
                        <input 
                            type="checkbox"
                            name="private_shop_mode"
                            value="1"
                            {{ $vendorProfile->private_shop_mode ? 'checked' : '' }}
                        >
                        <span class="vendor-panel-appearance-slider"></span>
                    </label>
                </label>
                <p class="vendor-panel-appearance-help-text">
                    When enabled, your store will be hidden from the public, but users who have saved your reference code will still be able to view your products.
                </p>
                @error('private_shop_mode')
                    <p class="vendor-panel-appearance-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="vendor-panel-appearance-form-group">
                <label class="vendor-panel-appearance-textarea-label">
                    Store Description
                    <span class="vendor-panel-appearance-char-limit">(8-800 characters)</span>
                </label>
                <textarea
                    name="description"
                    class="vendor-panel-appearance-textarea"
                    required
                    minlength="8"
                    maxlength="800"
                >{{ old('description', $vendorProfile->description) }}</textarea>
                
                @error('description')
                    <p class="vendor-panel-appearance-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="vendor-panel-appearance-form-group">
                <label class="vendor-panel-appearance-textarea-label">
                    Vendor Policy
                    <span class="vendor-panel-appearance-char-limit">(8-1600 characters)</span>
                </label>
                <textarea
                    name="vendor_policy"
                    class="vendor-panel-appearance-textarea"
                    minlength="8"
                    maxlength="1600"
                >{{ old('vendor_policy', $vendorProfile->vendor_policy) }}</textarea>
                
                @error('vendor_policy')
                    <p class="vendor-panel-appearance-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="vendor-panel-appearance-submit">
                <button type="submit" class="vendor-panel-appearance-submit-btn">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
