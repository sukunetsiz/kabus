@extends('layouts.app')

@section('content')
<div class="products-common-create-container">
    <div class="products-common-create-card">
        <div class="products-common-create-content">
            <h2 class="products-common-create-title">
                Add New {{ ucfirst($type) }} Product
            </h2>

            <form action="{{ route('vendor.products.store', $type) }}" method="POST" class="products-common-create-form" enctype="multipart/form-data">
                @csrf

                <!-- Product Pictures Section -->
                <div class="products-common-create-section">
                    <!-- Main Product Picture -->
                    <div class="products-common-create-field">
                        <label class="products-common-create-label">
                            Main Product Picture
                        </label>
                        <div class="products-common-create-upload-wrapper">
                            <label for="product_picture" 
                                class="products-common-create-file-btn">
                                Choose Picture
                            </label>
                            <input type="file" name="product_picture" id="product_picture" 
                                class="hidden" accept="image/jpeg,image/png,image/gif,image/webp">
                            <p class="products-common-create-help-text">
                                Optional. JPEG, PNG, GIF, WebP. Max 800KB.
                            </p>
                        </div>
                        @error('product_picture')
                            <p class="products-common-create-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Photos -->
                    <div class="products-common-create-field">
                        <label class="products-common-create-label">
                            Additional Photos (Up to 3)
                        </label>
                        <div class="products-common-create-upload-wrapper">
                            <label for="additional_photos" 
                                class="products-common-create-file-btn">
                                Choose Additional Photos
                            </label>
                            <input type="file" name="additional_photos[]" id="additional_photos" 
                                class="hidden" accept="image/jpeg,image/png,image/gif,image/webp"
                                multiple>
                            <p class="products-common-create-help-text">
                                Optional. Select up to 3 additional photos. JPEG, PNG, GIF, WebP. Max 800KB each.
                            </p>
                        </div>
                        @error('additional_photos.*')
                            <p class="products-common-create-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Name -->
                <div class="products-common-create-field">
                    <label for="name" class="products-common-create-label">
                        Product Name
                    </label>
                    <input type="text" name="name" id="name" required
                        class="products-common-create-input"
                        value="{{ old('name') }}">
                    @error('name')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="products-common-create-field">
                    <label for="description" class="products-common-create-label">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4" required
                        class="products-common-create-textarea">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div class="products-common-create-field">
                    <label for="price" class="products-common-create-label">
                        Price (USD)
                    </label>
                    <div class="products-common-create-price-wrapper">
                        <div class="products-common-create-price-symbol">
                            <span>$</span>
                        </div>
                        <input type="number" name="price" id="price" required step="0.01" min="0"
                            class="products-common-create-price-input"
                            value="{{ old('price') }}">
                    </div>
                    @error('price')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="products-common-create-field">
                    <label for="category_id" class="products-common-create-label">
                        Category
                    </label>
                    <select name="category_id" id="category_id" required
                        class="products-common-create-select">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <!-- Main category -->
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            <!-- Subcategories -->
                            @foreach($category->children as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ old('category_id') == $subcategory->id ? 'selected' : '' }}>
                                    -- {{ $subcategory->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Amount -->
                <div class="products-common-create-field">
                    <label for="stock_amount" class="products-common-create-label">
                        Stock Amount
                    </label>
                    <input type="number" name="stock_amount" id="stock_amount" required min="0" max="999999"
                        class="products-common-create-input"
                        value="{{ old('stock_amount', 0) }}">
                    @error('stock_amount')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Measurement Unit -->
                <div class="products-common-create-field">
                    <label for="measurement_unit" class="products-common-create-label">
                        Measurement Unit
                    </label>
                    <select name="measurement_unit" id="measurement_unit" required
                        class="products-common-create-select">
                        <option value="">Select a measurement unit</option>
                        @foreach($measurementUnits as $value => $label)
                            <option value="{{ $value }}" {{ old('measurement_unit') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('measurement_unit')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shipping Locations -->
                <div class="products-common-create-shipping-grid">
                    <!-- Ships From -->
                    <div class="products-common-create-field">
                        <label for="ships_from" class="products-common-create-label">
                            From:
                        </label>
                        <select name="ships_from" id="ships_from" required
                            class="products-common-create-select">
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('ships_from', 'Worldwide') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('ships_from')
                            <p class="products-common-create-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ships To -->
                    <div class="products-common-create-field">
                        <label for="ships_to" class="products-common-create-label">
                            To:
                        </label>
                        <select name="ships_to" id="ships_to" required
                            class="products-common-create-select">
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('ships_to', 'Worldwide') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('ships_to')
                            <p class="products-common-create-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Delivery Options -->
                <div class="products-common-create-section">
                    <h3 class="products-common-create-section-title">
                        @if($type === 'deaddrop')
                            Pickup Options
                        @else
                            Delivery Options
                        @endif
                    </h3>
                    <p class="products-common-create-section-desc">
                        Add between 1 and 4 {{ $type === 'deaddrop' ? 'pickup' : 'delivery' }} options. At least one option is required.
                    </p>
                    
                    @for ($i = 0; $i < 4; $i++)
                        <div class="products-common-create-option-card">
                            <h4 class="products-common-create-option-title">
                                {{ $type === 'deaddrop' ? 'Pickup' : 'Delivery' }} Option {{ $i + 1 }}
                            </h4>
                            
                            <!-- Description -->
                            <div class="products-common-create-field">
                                <label for="delivery_options_{{ $i }}_description" 
                                    class="products-common-create-label">
                                    Description
                                </label>
                                <input type="text" 
                                    name="delivery_options[{{ $i }}][description]" 
                                    id="delivery_options_{{ $i }}_description"
                                    class="products-common-create-input"
                                    value="{{ old('delivery_options.'.$i.'.description') }}"
                                    placeholder="{{ $type === 'digital' 
                                        ? 'e.g., Instant download for free after purchase, Delivered within 24 hours'
                                        : ($type === 'cargo' 
                                            ? 'e.g., Standard Delivery (3-5 business days via UPS), Express Shipping (1-2 business days via FedEx)'
                                            : 'e.g., Pickup near Central Park, NYC within 24 hours, Locker #31 at Union Station') }}"
                                    {{ $i === 0 ? 'required' : '' }}>
                                @error('delivery_options.'.$i.'.description')
                                    <p class="products-common-create-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="products-common-create-field">
                                <label for="delivery_options_{{ $i }}_price" 
                                    class="products-common-create-label">
                                    Additional Price (USD)
                                </label>
                                <div class="products-common-create-price-wrapper">
                                    <div class="products-common-create-price-symbol">
                                        <span>$</span>
                                    </div>
                                    <input type="number" 
                                        name="delivery_options[{{ $i }}][price]" 
                                        id="delivery_options_{{ $i }}_price"
                                        step="0.01" 
                                        min="0"
                                        class="products-common-create-price-input"
                                        value="{{ old('delivery_options.'.$i.'.price') }}"
                                        placeholder="0.00"
                                        {{ $i === 0 ? 'required' : '' }}>
                                    @error('delivery_options.'.$i.'.price')
                                        <p class="products-common-create-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor

                    @error('delivery_options')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bulk Options -->
                <div class="products-common-create-section">
                    <h3 class="products-common-create-section-title">Bulk Options</h3>
                    <p class="products-common-create-section-desc">
                        Optionally add up to 4 bulk purchase options. Leave empty if not offering bulk pricing.
                    </p>
                    
                    @for ($i = 0; $i < 4; $i++)
                        <div class="products-common-create-option-card">
                            <h4 class="products-common-create-option-title">
                                Bulk Option {{ $i + 1 }}
                            </h4>
                            
                            <!-- Amount -->
                            <div class="products-common-create-field">
                                <label for="bulk_options_{{ $i }}_amount" 
                                    class="products-common-create-label">
                                    Bulk Amount
                                </label>
                                <input type="number" 
                                    name="bulk_options[{{ $i }}][amount]" 
                                    id="bulk_options_{{ $i }}_amount"
                                    step="0.01"
                                    min="0"
                                    class="products-common-create-input"
                                    value="{{ old('bulk_options.'.$i.'.amount') }}"
                                    placeholder="Enter bulk quantity">
                                @error('bulk_options.'.$i.'.amount')
                                    <p class="products-common-create-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="products-common-create-field">
                                <label for="bulk_options_{{ $i }}_price" 
                                    class="products-common-create-label">
                                    Bulk Price (USD)
                                </label>
                                <div class="products-common-create-price-wrapper">
                                    <div class="products-common-create-price-symbol">
                                        <span>$</span>
                                    </div>
                                    <input type="number" 
                                        name="bulk_options[{{ $i }}][price]" 
                                        id="bulk_options_{{ $i }}_price"
                                        step="0.01" 
                                        min="0"
                                        class="products-common-create-price-input"
                                        value="{{ old('bulk_options.'.$i.'.price') }}"
                                        placeholder="Enter bulk price">
                                    @error('bulk_options.'.$i.'.price')
                                        <p class="products-common-create-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor

                    @error('bulk_options')
                        <p class="products-common-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="products-common-create-submit-wrapper">
                    <button type="submit" class="products-common-create-submit-btn">
                        Create {{ ucfirst($type) }} Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection