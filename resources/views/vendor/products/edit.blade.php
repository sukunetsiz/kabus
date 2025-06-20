@extends('layouts.app')

@section('content')
<div class="products-common-create-container">
    <div class="products-common-create-card">
        <div class="products-common-create-content">
            <h2 class="products-common-create-title">
                Edit {{ ucfirst($product->type) }} Product
            </h2>

            <form action="{{ route('vendor.products.update', $product) }}" method="POST" class="products-common-create-form">
                @csrf
                @method('PATCH')
                
                <!-- Product Activation Toggle -->
                <div class="products-common-edit-visibility">
                    <h3 class="products-common-edit-visibility-title">
                        Product Visibility
                    </h3>
                    
                    <div class="products-common-edit-visibility-toggle">
                        <input type="checkbox" name="active" id="active" value="1" 
                            class="products-common-edit-visibility-checkbox"
                            {{ $product->active ? 'checked' : '' }}>
                    </div>
                    
                    <span class="products-common-edit-visibility-status {{ $product->active ? 'active' : '' }}">
                        {{ $product->active ? 'This product is currently visible to customers' : 'This product is currently hidden from customers' }}
                    </span>
                    
                    <p class="products-common-edit-visibility-hint">
                        When inactive, the product will be hidden from all marketplace listings.
                    </p>
                </div>

                <!-- Product Pictures Section (Read-only) -->
                <div class="products-common-create-section">
                     <div class="products-common-create-field">
                        <label class="products-common-create-label products-common-edit-photos-title">
                            Photos
                        </label>
                        <div class="products-common-edit-photos-container">
                            <div class="products-common-edit-photo">
                                <img src="{{ $product->product_picture_url }}" alt="Product Picture" class="products-common-create-preview">
                            </div>
                            @foreach($product->additional_photos_urls as $photoUrl)
                                <div class="products-common-edit-photo">
                                    <img src="{{ $photoUrl }}" alt="Additional Product Picture" class="products-common-create-preview">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Name (Read-only) -->
                <div class="products-common-create-field">
                    <label class="products-common-create-label">
                        Product Name
                    </label>
                    <input type="text" value="{{ $product->name }}" class="products-common-create-input" readonly disabled>
                </div>

                <!-- Description -->
                <div class="products-common-create-field">
                    <label for="description" class="products-common-create-label">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4" required
                        class="products-common-create-textarea" 
                        minlength="4" maxlength="2400">{{ old('description', $product->description) }}</textarea>
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
                        <input type="number" name="price" id="price" required step="0.01" min="0" max="80000"
                            class="products-common-create-price-input"
                            value="{{ old('price', $product->price) }}">
                    </div>
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
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            <!-- Subcategories -->
                            @foreach($category->children as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ old('category_id', $product->category_id) == $subcategory->id ? 'selected' : '' }}>
                                    -- {{ $subcategory->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <!-- Stock Amount -->
                <div class="products-common-create-field">
                    <label for="stock_amount" class="products-common-create-label">
                        Stock Amount
                    </label>
                    <input type="number" name="stock_amount" id="stock_amount" required min="0" max="80000"
                        class="products-common-create-input"
                        value="{{ old('stock_amount', $product->stock_amount) }}">
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
                            <option value="{{ $value }}" {{ old('measurement_unit', $product->measurement_unit) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
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
                                <option value="{{ $country }}" {{ old('ships_from', $product->ships_from) == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ships To -->
                    <div class="products-common-create-field">
                        <label for="ships_to" class="products-common-create-label">
                            To:
                        </label>
                        <select name="ships_to" id="ships_to" required
                            class="products-common-create-select">
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('ships_to', $product->ships_to) == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Delivery Options -->
                <div class="products-common-create-section">
                    <h3 class="products-common-create-section-title">
                        @if($product->type === 'deaddrop')
                            Pickup Options
                        @else
                            Delivery Options
                        @endif
                    </h3>
                    <p class="products-common-create-section-desc">
                        Add between 1 and 4 {{ $product->type === 'deaddrop' ? 'pickup' : 'delivery' }} options. At least one option is required.
                    </p>
                    
                    @for ($i = 0; $i < 4; $i++)
                        <div class="products-common-create-option-card">
                            <h4 class="products-common-create-option-title">
                                {{ $product->type === 'deaddrop' ? 'Pickup' : 'Delivery' }} Option {{ $i + 1 }}
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
                                    value="{{ old('delivery_options.'.$i.'.description', $product->delivery_options[$i]['description'] ?? '') }}"
                                    placeholder="{{ $product->type === 'digital' 
                                        ? 'e.g., Instant download for free after purchase, Delivered within 24 hours'
                                        : ($product->type === 'cargo' 
                                            ? 'e.g., Standard Delivery (3-5 business days via UPS), Express Shipping (1-2 business days via FedEx)'
                                            : 'e.g., Pickup near Central Park, NYC within 24 hours, Locker #31 at Union Station') }}"
                                    {{ $i === 0 ? 'required' : '' }}
                                    minlength="4" maxlength="160">
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
                                        max="80000"
                                        class="products-common-create-price-input"
                                        value="{{ old('delivery_options.'.$i.'.price', $product->delivery_options[$i]['price'] ?? '') }}"
                                        placeholder="0.00"
                                        {{ $i === 0 ? 'required' : '' }}>
                                </div>
                            </div>
                        </div>
                    @endfor

                </div>

                <!-- Bulk Options -->
                <div class="products-common-create-section">
                    <h3 class="products-common-create-section-title">Bulk Options</h3>
                    <p class="products-common-create-section-desc">
                        Optionally add up to 8 bulk purchase options. Leave empty if not offering bulk pricing.
                    </p>
                    
                    @for ($i = 0; $i < 8; $i++)
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
                                    step="1"
                                    min="0"
                                    max="80000"
                                    class="products-common-create-input"
                                    value="{{ old('bulk_options.'.$i.'.amount', $product->bulk_options[$i]['amount'] ?? '') }}"
                                    placeholder="Enter bulk quantity">
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
                                        max="80000"
                                        class="products-common-create-price-input"
                                        value="{{ old('bulk_options.'.$i.'.price', $product->bulk_options[$i]['price'] ?? '') }}"
                                        placeholder="Enter bulk price">
                                </div>
                            </div>
                        </div>
                    @endfor

                </div>

                <!-- Submit Button -->
                <div class="products-common-create-submit-wrapper">
                    <button type="submit" class="products-common-create-submit-btn">
                        Update {{ ucfirst($product->type) }} Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
