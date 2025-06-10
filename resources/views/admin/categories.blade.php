@extends('layouts.app')

@section('content')

<div class="categories-index-container">

    @if($errors->any())
        <div class="alert alert-error" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="categories-index-header">
        <h1 class="categories-index-title">Category Management</h1>
    </div>

    <div class="categories-index-grid">
        <!-- Create Category Form -->
        <div class="categories-index-card">
            <h2 class="categories-index-card-title">Create Category</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="categories-index-form-group">
                    <label for="name" class="categories-index-label">Category Name</label>
                    <input type="text" name="name" id="name" 
                           class="categories-index-input"
                           required minlength="1" maxlength="16" 
                           value="{{ old('name') }}">
                    <p class="categories-index-help-text">Between 1 and 16 characters</p>
                </div>

                <div class="categories-index-form-group">
                    <label for="parent_id" class="categories-index-label">Parent Category (Optional)</label>
                    <select name="parent_id" id="parent_id" class="categories-index-select">
                        <option value="">None (Main Category)</option>
                        @foreach($mainCategories as $category)
                            <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="categories-index-submit">
                    Create Category
                </button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="categories-index-card">
            <h2 class="categories-index-card-title">Categories</h2>
            
            @if($mainCategories->isEmpty())
                <p class="categories-index-empty">No categories found.</p>
            @else
                <div class="categories-index-list">
                    @foreach($mainCategories as $mainCategory)
                        <div class="categories-index-category">
                            <div class="categories-index-category-header">
                                <span class="categories-index-category-name">{{ $mainCategory->name }}</span>
                                <form action="{{ route('admin.categories.delete', $mainCategory) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="categories-index-delete-btn">
                                        Delete
                                    </button>
                                </form>
                            </div>

                            @if($mainCategory->children->isNotEmpty())
                                <div class="categories-index-subcategories">
                                    @foreach($mainCategory->children as $subCategory)
                                        <div class="categories-index-subcategory">
                                            <span class="categories-index-subcategory-name">{{ $subCategory->name }}</span>
                                            <form action="{{ route('admin.categories.delete', $subCategory) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="categories-index-delete-btn">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
