<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
        'selected_delivery_option',
        'selected_bulk_option'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'selected_delivery_option' => 'array',
        'selected_bulk_option' => 'array'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product in the cart.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price for this cart item including delivery option
     */
    public function getTotalPrice(): float
    {
        // For bulk options, quantity represents number of sets
        // price represents the price per set
        $basePrice = $this->price * $this->quantity;

        return $basePrice + ($this->selected_delivery_option['price'] ?? 0);
    }

    /**
     * Validate if a product can be added to user's cart
     * 
     * @param User $user
     * @param Product $product
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateProductAddition(User $user, Product $product): array
    {
        // Check if user has any items in cart
        $existingItem = self::where('user_id', $user->id)->first();
        
        if ($existingItem) {
            // Get vendor of existing cart items
            $existingVendorId = $existingItem->product->user_id;
            
            // Check if new product is from same vendor
            if ($product->user_id !== $existingVendorId) {
                return [
                    'valid' => false,
                    'message' => 'You can only add products from the same vendor to your cart.'
                ];
            }
        }

        // Check if product is active
        if (!$product->active) {
            return [
                'valid' => false,
                'message' => 'This product is currently not available.'
            ];
        }

        // Check if vendor is on vacation
        if ($product->user->vendorProfile && $product->user->vendorProfile->vacation_mode) {
            return [
                'valid' => false,
                'message' => 'This vendor is currently on vacation.'
            ];
        }

        // Check stock availability
        if ($product->stock_amount < 1) {
            return [
                'valid' => false,
                'message' => 'This product is out of stock.'
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Get the total price for all items in a user's cart
     * 
     * @param User $user
     * @return float
     */
    public static function getCartTotal(User $user): float
    {
        return self::where('user_id', $user->id)
            ->get()
            ->sum(function ($item) {
                return $item->getTotalPrice();
            });
    }
}