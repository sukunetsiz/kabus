@extends('layouts.app')

@section('content')

<div class="become-vendor-application-container">
    <div class="become-vendor-application-card">
        <h1>Vendor Application</h1>
        
        <form action="{{ route('become.vendor.submit-application') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if($errors->any())
                <div class="become-vendor-application-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="become-vendor-application-form-group">
                <label for="application_text">Application Details</label>
                <textarea 
                    class="become-vendor-application-textarea"
                    id="application_text" 
                    name="application_text" 
                    required
                    minlength="80"
                    maxlength="4000"
                    placeholder="Please provide:
- Information about yourself
- What products will you sell?
- Your communication address
- Previous references from other websites"
                >{{ old('application_text') }}</textarea>
                <small class="form-text text-muted">
                    Your application must be between 80 and 4000 characters. Be detailed but concise.
                </small>
            </div>

            <div class="become-vendor-application-form-group">
                <label>Product Images (At least 1 required, maximum 4 images)</label>
                <div class="become-vendor-application-image-upload">
                    @for($i = 0; $i < 4; $i++)
                        <div class="become-vendor-application-image-slot">
                            <input 
                                type="file" 
                                name="product_images[]" 
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="become-vendor-application-image-input"
                                {{ $i === 0 ? 'required' : '' }}
                            >
                            <div class="become-vendor-application-image-preview">
                                <div class="become-vendor-application-image-hint">
                                    {{ $i === 0 ? '(Required)' : '(Optional)' }}<br>
                                    Click to upload
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <small class="form-text text-muted">
                    Supported formats: JPEG, PNG, GIF, WebP. Maximum size: 800KB per image.
                </small>
            </div>

            <div class="become-vendor-application-form-group">
                <button type="submit" class="become-vendor-application-submit-btn">Submit Application</button>
            </div>
        </form>
    </div>
</div>
@endsection
