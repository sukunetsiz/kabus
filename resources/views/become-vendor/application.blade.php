@extends('layouts.app')

@section('content')
<div class="become-vendor-application-container">
    <div class="become-vendor-application-card">
        <h1>Vendor Application</h1>
        
        <form action="{{ route('become.vendor.submit-application') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="application_text">Application Details</label>
                <textarea 
                    id="application_text" 
                    name="application_text" 
                    rows="10" 
                    required
                    placeholder="Please provide:
- Tell us about yourself
- What products will you sell?
- Your communication address
- Previous references from other websites"
                >{{ old('application_text') }}</textarea>
            </div>

            <div class="form-group">
                <label>Product Images (Maximum 4 images)</label>
                <div class="image-upload-container">
                    @for($i = 0; $i < 4; $i++)
                        <div class="image-upload-slot">
                            <input 
                                type="file" 
                                name="product_images[]" 
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="image-upload-input"
                            >
                            <div class="image-upload-preview"></div>
                        </div>
                    @endfor
                </div>
                <small class="form-text text-muted">
                    Supported formats: JPEG, PNG, GIF, WebP. Maximum size: 800KB per image.
                </small>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Submit Application</button>
            </div>
        </form>
    </div>
</div>
@endsection