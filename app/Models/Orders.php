<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Orders extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    // Order status constants
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_PAYMENT_RECEIVED = 'payment_received';
    public const STATUS_PRODUCT_DELIVERED = 'product_delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'vendor_id',
        'unique_url',
        'subtotal',
        'commission',
        'total',
        'status',
        'shipping_address',
        'delivery_option',
        'encrypted_message',
        'is_paid',
        'is_delivered',
        'is_completed',
        'paid_at',
        'delivered_at',
        'completed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'commission' => 'decimal:2',
        'total' => 'decimal:2',
        'is_paid' => 'boolean',
        'is_delivered' => 'boolean',
        'is_completed' => 'boolean',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // Set UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            // Generate unique 30-character URL if not set
            if (empty($model->unique_url)) {
                $model->unique_url = Str::random(30);
            }

            // Set default status to waiting for payment
            if (empty($model->status)) {
                $model->status = self::STATUS_WAITING_PAYMENT;
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'unique_url';
    }

    /**
     * Get the user (buyer) that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vendor for this order.
     */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the items for this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Mark the order as paid.
     */
    public function markAsPaid()
    {
        if ($this->status !== self::STATUS_WAITING_PAYMENT) {
            return false;
        }

        $this->status = self::STATUS_PAYMENT_RECEIVED;
        $this->is_paid = true;
        $this->paid_at = now();
        $this->save();

        return true;
    }

    /**
     * Mark the order as delivered.
     */
    public function markAsDelivered()
    {
        if ($this->status !== self::STATUS_PAYMENT_RECEIVED) {
            return false;
        }

        $this->status = self::STATUS_PRODUCT_DELIVERED;
        $this->is_delivered = true;
        $this->delivered_at = now();
        $this->save();

        return true;
    }

    /**
     * Mark the order as completed.
     */
    public function markAsCompleted()
    {
        if ($this->status !== self::STATUS_PRODUCT_DELIVERED) {
            return false;
        }

        $this->status = self::STATUS_COMPLETED;
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();

        // Reduce stock for all products in this order
        foreach ($this->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }
            
            // Calculate actual quantity based on bulk options
            $actualQuantity = $item->quantity;
            if ($item->bulk_option && isset($item->bulk_option['amount'])) {
                $actualQuantity = $item->quantity * $item->bulk_option['amount'];
            }
            
            // Only reduce stock if there's enough available
            if ($product->stock_amount >= $actualQuantity) {
                $product->stock_amount -= $actualQuantity;
                $product->save();
            }
        }

        return true;
    }

    /**
     * Mark the order as cancelled.
     */
    public function markAsCancelled()
    {
        // Only allow cancellation for orders that aren't completed
        if ($this->status === self::STATUS_COMPLETED) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->save();

        return true;
    }

    /**
     * Get the formatted status.
     */
    public function getFormattedStatus()
    {
        return match($this->status) {
            self::STATUS_WAITING_PAYMENT => 'Waiting for Payment',
            self::STATUS_PAYMENT_RECEIVED => 'Payment Received',
            self::STATUS_PRODUCT_DELIVERED => 'Product Delivered',
            self::STATUS_COMPLETED => 'Order Completed',
            self::STATUS_CANCELLED => 'Order Cancelled',
            default => 'Unknown Status'
        };
    }

    /**
     * Get all orders for a user (as buyer).
     */
    public static function getUserOrders($userId)
    {
        return self::where('user_id', $userId)
            ->with(['items', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all orders for a vendor.
     */
    public static function getVendorOrders($vendorId)
    {
        return self::where('vendor_id', $vendorId)
            ->with(['items', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find order by its unique URL.
     */
    public static function findByUrl($url)
    {
        return self::where('unique_url', $url)
            ->with(['items', 'user', 'vendor'])
            ->first();
    }

    /**
     * Create an order from cart items.
     */
    public static function createFromCart($user, $cartItems, $subtotal, $commission, $total)
    {
        // Get vendor ID from the first cart item
        $vendorId = $cartItems->first()->product->user_id;

        // Create the order
        $order = self::create([
            'user_id' => $user->id,
            'vendor_id' => $vendorId,
            'subtotal' => $subtotal,
            'commission' => $commission,
            'total' => $total,
            'status' => self::STATUS_WAITING_PAYMENT
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_description' => $product->description,
                'price' => $cartItem->price,
                'quantity' => $cartItem->quantity,
                'measurement_unit' => $product->measurement_unit,
                'delivery_option' => $cartItem->selected_delivery_option,
                'bulk_option' => $cartItem->selected_bulk_option,
            ]);

            // Save encrypted message if exists
            if ($cartItem->encrypted_message) {
                $order->encrypted_message = $cartItem->encrypted_message;
                $order->save();
            }
        }

        return $order;
    }
}

/**
 * OrderItem model represents individual items within an order.
 */
class OrderItem extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_description',
        'price',
        'quantity',
        'measurement_unit',
        'delivery_option',
        'bulk_option',
        'delivery_text'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'delivery_option' => 'array',
        'bulk_option' => 'array'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // Set UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the order that owns this item.
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    /**
     * Get the original product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Calculate total price for this item.
     */
    public function getTotalPrice()
    {
        // Base price multiplied by quantity
        $basePrice = $this->price * $this->quantity;
        
        // Add delivery option price if set
        $deliveryPrice = 0;
        if (isset($this->delivery_option['price'])) {
            $deliveryPrice = (float) $this->delivery_option['price'];
        }
        
        return $basePrice + $deliveryPrice;
    }

    /**
     * Get formatted delivery option.
     */
    public function getFormattedDeliveryOption()
    {
        if (!$this->delivery_option) {
            return null;
        }

        return [
            'description' => $this->delivery_option['description'] ?? 'N/A',
            'price' => isset($this->delivery_option['price']) 
                ? '$' . number_format($this->delivery_option['price'], 2) 
                : 'N/A'
        ];
    }

    /**
     * Get formatted bulk option.
     */
    public function getFormattedBulkOption()
    {
        if (!$this->bulk_option) {
            return null;
        }

        $unit = $this->measurement_unit;
        $formattedUnit = Product::getMeasurementUnits()[$unit] ?? $unit;

        return [
            'amount' => $this->bulk_option['amount'] ?? 0,
            'price' => isset($this->bulk_option['price']) 
                ? '$' . number_format($this->bulk_option['price'], 2) 
                : 'N/A',
            'display_text' => sprintf('%s %s for $%s',
                number_format($this->bulk_option['amount'] ?? 0),
                $formattedUnit,
                number_format($this->bulk_option['price'] ?? 0, 2)
            )
        ];
    }
}