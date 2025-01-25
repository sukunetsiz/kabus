@extends('layouts.app')

@section('title', 'Add Cargo Product')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                Add New Cargo Product
            </h2>

            <form action="{{ route('vendor.products.cargo.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf

                <!-- Product Picture -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Product Picture
                    </label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-32 w-32 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                <img id="preview-image" src="{{ asset('images/default-product-picture.png') }}" 
                                    alt="Product preview" class="h-full w-full object-cover">
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <label for="product_picture" 
                                class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Choose Picture
                            </label>
                            <input type="file" name="product_picture" id="product_picture" 
                                class="hidden" accept="image/jpeg,image/png,image/gif,image/webp"
                                onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Optional. JPEG, PNG, GIF, WebP. Max 800KB.
                            </p>
                        </div>
                    </div>
                    @error('product_picture')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Product Name
                    </label>
                    <input type="text" name="name" id="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Price (USD)
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="price" id="price" required step="0.01" min="0"
                            class="pl-7 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            value="{{ old('price') }}">
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Category
                    </label>
                    <select name="category_id" id="category_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Amount -->
                <div>
                    <label for="stock_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Stock Amount
                    </label>
                    <input type="number" name="stock_amount" id="stock_amount" required min="0" max="999999"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        value="{{ old('stock_amount', 0) }}">
                    @error('stock_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Measurement Unit -->
                <div>
                    <label for="measurement_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Measurement Unit
                    </label>
                    <select name="measurement_unit" id="measurement_unit" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select a measurement unit</option>
                        @foreach($measurementUnits as $value => $label)
                            <option value="{{ $value }}" {{ old('measurement_unit') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('measurement_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Options -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Delivery Options</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Add between 1 and 4 delivery options. At least one option is required.
                    </p>
                    
                    @for ($i = 0; $i < 4; $i++)
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                Delivery Option {{ $i + 1 }}
                            </h4>
                            
                            <!-- Description -->
                            <div class="mb-4">
                                <label for="delivery_options_{{ $i }}_description" 
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <input type="text" 
                                    name="delivery_options[{{ $i }}][description]" 
                                    id="delivery_options_{{ $i }}_description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    value="{{ old('delivery_options.'.$i.'.description') }}"
                                    placeholder="e.g., Standard Shipping, Express Shipping">
                                @error('delivery_options.'.$i.'.description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="delivery_options_{{ $i }}_price" 
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Additional Price (USD)
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                        name="delivery_options[{{ $i }}][price]" 
                                        id="delivery_options_{{ $i }}_price"
                                        step="0.01" 
                                        min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        value="{{ old('delivery_options.'.$i.'.price') }}"
                                        placeholder="0.00">
                                    @error('delivery_options.'.$i.'.price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor

                    @error('delivery_options')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bulk Options -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Bulk Options</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Optionally add up to 4 bulk purchase options. Leave empty if not offering bulk pricing.
                    </p>
                    
                    @for ($i = 0; $i < 4; $i++)
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                Bulk Option {{ $i + 1 }}
                            </h4>
                            
                            <!-- Amount -->
                            <div class="mb-4">
                                <label for="bulk_options_{{ $i }}_amount" 
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bulk Amount
                                </label>
                                <input type="number" 
                                    name="bulk_options[{{ $i }}][amount]" 
                                    id="bulk_options_{{ $i }}_amount"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    value="{{ old('bulk_options.'.$i.'.amount') }}"
                                    placeholder="Enter bulk quantity">
                                @error('bulk_options.'.$i.'.amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="bulk_options_{{ $i }}_price" 
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bulk Price (USD)
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                        name="bulk_options[{{ $i }}][price]" 
                                        id="bulk_options_{{ $i }}_price"
                                        step="0.01" 
                                        min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        value="{{ old('bulk_options.'.$i.'.price') }}"
                                        placeholder="Enter bulk price">
                                    @error('bulk_options.'.$i.'.price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor

                    @error('bulk_options')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection