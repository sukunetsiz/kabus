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
     * @return array ['valid' => bool, 'reason' => string]
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
                    'reason' => 'different_vendor'
                ];
            }
        }

        // Check if product is active
        if (!$product->active) {
            return [
                'valid' => false,
                'reason' => 'inactive'
            ];
        }

        // Check if vendor is on vacation
        if ($product->user->vendorProfile && $product->user->vendorProfile->vacation_mode) {
            return [
                'valid' => false,
                'reason' => 'vacation'
            ];
        }

        // Check stock availability
        if ($product->stock_amount < 1) {
            return [
                'valid' => false,
                'reason' => 'out_of_stock'
            ];
        }

        return ['valid' => true, 'reason' => ''];
    }

    /**
     * Validate if requested quantity is available in stock
     * 
     * @param Product $product
     * @param int $quantity
     * @param array|null $bulkOption
     * @return array ['valid' => bool, 'reason' => string]
     */
    public static function validateStockAvailability(Product $product, int $quantity, ?array $bulkOption = null): array
    {
        // For bulk options, quantity is already in terms of sets
        // We only multiply by bulk amount when checking against stock
        $totalStockNeeded = $bulkOption 
            ? $quantity * $bulkOption['amount']  // For bulk purchases, multiply sets by amount per set
            : $quantity;                         // For regular purchases, use quantity directly

        // Check if we have sufficient stock
        if ($totalStockNeeded > $product->stock_amount) {
            return [
                'valid' => false,
                'reason' => 'insufficient_stock',
                'available' => $product->stock_amount,
                'requested' => $totalStockNeeded
            ];
        }

        return ['valid' => true, 'reason' => ''];
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
