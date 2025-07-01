<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeaturedProduct extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'admin_id'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the product that is featured.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the admin who featured the product.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get all featured products with their associated data in random order.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllFeaturedProducts()
    {
        return static::with(['product', 'product.user'])
            ->inRandomOrder()
            ->get();
    }

    /**
     * Check if a product is currently featured.
     *
     * @param mixed $productId
     * @return bool
     */
    public static function isProductFeatured($productId): bool
    {
        return static::where('product_id', $productId)->exists();
    }
}