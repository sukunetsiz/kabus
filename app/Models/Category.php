<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
        ];
    }

    /**
     * Get the parent category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the subcategories for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check if the category is a main category (has no parent).
     *
     * @return bool
     */
    public function isMainCategory(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get all main categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function mainCategories()
    {
        return static::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get formatted name for display.
     *
     * @return string
     */
    public function getFormattedName(): string
    {
        if ($this->isMainCategory()) {
            return $this->name;
        }
        return $this->parent->name . ' > ' . $this->name;
    }

    /**
     * Get validation rules for category.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'name' => 'required|string|min:1|max:16',
            'parent_id' => 'nullable|exists:categories,id'
        ];
    }
}