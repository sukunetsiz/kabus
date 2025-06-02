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
        'selected_bulk_option',
        'encrypted_message'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'selected_delivery_option' => 'array',
        'selected_bulk_option' => 'array',
        'encrypted_message' => 'string'
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
     * 
     * This relationship can include soft-deleted products if needed.
     * 
     * @param bool $withTrashed Whether to include soft-deleted products
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product($withTrashed = false)
    {
        $relation = $this->belongsTo(Product::class);
        
        if ($withTrashed) {
            $relation->withTrashed();
        }
        
        return $relation;
    }
    
    /**
     * Check if the cart item has a deleted product.
     * 
     * @return bool
     */
    public function hasDeletedProduct()
    {
        return $this->product === null && $this->product(true)->first() !== null;
    }

    /**
     * Calculate total price for this cart item including delivery option
     */
    /**
     * Encrypt a message using the vendor's PGP key
     * 
     * @param string $message The message to encrypt
     * @return string|false The encrypted message or false on failure
     */
    public function encryptMessageForVendor(string $message)
    {
        try {
            // Get vendor's PGP key through product relationship
            $vendorPgpKey = $this->product->user->pgpKey;
            
            if (!$vendorPgpKey || !$vendorPgpKey->public_key) {
                return false;
            }

            // Initialize GnuPG
            putenv("GNUPGHOME=/tmp");
            $gpg = new \gnupg();
            $gpg->seterrormode(\gnupg::ERROR_EXCEPTION);

            // Import vendor's public key
            $info = $gpg->import($vendorPgpKey->public_key);
            if (!$info) {
                return false;
            }

            // Add encryption key and encrypt the message
            $gpg->addencryptkey($info['fingerprint']);
            return $gpg->encrypt($message);

        } catch (\Exception $e) {
            return false;
        }
    }

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
