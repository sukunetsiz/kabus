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
    public const STATUS_PRODUCT_SENT = 'product_sent';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DISPUTED = 'disputed';

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
        'is_sent',
        'is_completed',
        'is_disputed',
        'paid_at',
        'sent_at',
        'completed_at',
        'disputed_at',
        'payment_address',
        'payment_address_index',
        'required_xmr_amount',
        'total_received_xmr',
        'xmr_usd_rate',
        'expires_at',
        'payment_completed_at'
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
        'required_xmr_amount' => 'decimal:12',
        'total_received_xmr' => 'decimal:12',
        'xmr_usd_rate' => 'decimal:2',
        'is_paid' => 'boolean',
        'is_sent' => 'boolean',
        'is_completed' => 'boolean',
        'is_disputed' => 'boolean',
        'paid_at' => 'datetime',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'disputed_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_completed_at' => 'datetime',
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
     * Get the dispute for this order.
     */
    public function dispute()
    {
        return $this->hasOne(Dispute::class, 'order_id');
    }

    /**
     * Generate a Monero subaddress for the order payment.
     * 
     * @param \MoneroIntegrations\MoneroPhp\walletRPC $walletRPC
     * @return bool
     */
    public function generatePaymentAddress($walletRPC)
    {
        try {
            // Only generate an address if none exists
            if (empty($this->payment_address)) {
                $result = $walletRPC->create_address(0, "Order Payment " . $this->id);
                
                $this->payment_address = $result['address'];
                $this->payment_address_index = $result['address_index'];
                $this->expires_at = now()->addMinutes((int) config('monero.address_expiration_time', 1440));
                $this->save();
            }
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to generate payment address: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check for and process new payments.
     * 
     * @param \MoneroIntegrations\MoneroPhp\walletRPC $walletRPC
     * @return bool
     */
    public function checkPayments($walletRPC)
    {
        if ($this->is_paid || $this->status !== self::STATUS_WAITING_PAYMENT) {
            return false;
        }

        try {
            // Check for new payments
            $transfers = $walletRPC->get_transfers([
                'in' => true,
                'pool' => true,
                'subaddr_indices' => [$this->payment_address_index]
            ]);

            // Calculate minimum accepted payment amount (10% of required amount)
            $minPaymentPercentage = 0.10; // Could also use a config value like for advertisements
            $minAcceptedAmount = $this->required_xmr_amount * $minPaymentPercentage;

            $totalReceived = 0;
            foreach (['in', 'pool'] as $type) {
                if (isset($transfers[$type])) {
                    foreach ($transfers[$type] as $transfer) {
                        // Only count payments that meet the minimum threshold
                        $amount = $transfer['amount'] / 1e12;
                        if ($amount >= $minAcceptedAmount) {
                            $totalReceived += $amount;
                        }
                    }
                }
            }

            // Update received amount
            $this->total_received_xmr = $totalReceived;

            // Check if payment is completed
            if ($totalReceived >= $this->required_xmr_amount && !$this->is_paid) {
                $this->status = self::STATUS_PAYMENT_RECEIVED;
                $this->is_paid = true;
                $this->paid_at = now();
                $this->payment_completed_at = now();
            }

            $this->save();
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error checking order payments: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the payment has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
    
    /**
     * Handle expired payment by automatically cancelling the order.
     * 
     * @return bool
     */
    public function handleExpiredPayment(): bool
    {
        // Only handle orders in waiting_payment status
        if ($this->status !== self::STATUS_WAITING_PAYMENT) {
            return false;
        }
        
        // Check if payment has expired
        if (!$this->isExpired()) {
            return false;
        }
        
        // Cancel the order
        return $this->markAsCancelled();
    }
    
    /**
     * Check if the order should be auto-cancelled because it hasn't been marked as sent
     * within 96 hours (4 days) of payment being received.
     * 
     * @return bool
     */
    public function shouldAutoCancelIfNotSent(): bool
    {
        // Only handle orders in payment_received status
        if ($this->status !== self::STATUS_PAYMENT_RECEIVED) {
            return false;
        }
        
        // Check if 96 hours (4 days) have passed since payment was received
        if (!$this->paid_at || $this->paid_at->addHours(96)->isFuture()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Auto-cancel an order that hasn't been marked as sent within the time limit.
     * 
     * @return bool
     */
    public function autoCancelIfNotSent(): bool
    {
        if (!$this->shouldAutoCancelIfNotSent()) {
            return false;
        }
        
        // Cancel the order
        return $this->markAsCancelled();
    }
    
    /**
     * Check if the order should be auto-completed because it hasn't been marked as completed
     * within 192 hours (8 days) of being marked as sent.
     * 
     * @return bool
     */
    public function shouldAutoCompleteIfNotConfirmed(): bool
    {
        // Only handle orders in product_sent status
        if ($this->status !== self::STATUS_PRODUCT_SENT) {
            return false;
        }
        
        // Don't auto-complete if there's an active dispute
        if ($this->is_disputed || $this->status === self::STATUS_DISPUTED) {
            return false;
        }
        
        // Check if 192 hours (8 days) have passed since product was marked as sent
        if (!$this->sent_at || $this->sent_at->addHours(192)->isFuture()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Auto-complete an order that hasn't been marked as completed within the time limit.
     * 
     * @return bool
     */
    public function autoCompleteIfNotConfirmed(): bool
    {
        if (!$this->shouldAutoCompleteIfNotConfirmed()) {
            return false;
        }
        
        // Complete the order
        return $this->markAsCompleted();
    }
    
    /**
     * Get the time remaining before an order is auto-cancelled if not marked as sent.
     * 
     * @return \Illuminate\Support\Carbon|null
     */
    public function getAutoCancelDeadline()
    {
        if ($this->status !== self::STATUS_PAYMENT_RECEIVED || !$this->paid_at) {
            return null;
        }
        
        return $this->paid_at->addHours(96);
    }
    
    /**
     * Get the time remaining before an order is auto-completed if not marked as completed.
     * 
     * @return \Illuminate\Support\Carbon|null
     */
    public function getAutoCompleteDeadline()
    {
        if ($this->status !== self::STATUS_PRODUCT_SENT || !$this->sent_at || $this->is_disputed) {
            return null;
        }
        
        return $this->sent_at->addHours(192);
    }
    
    /**
     * Find all orders that need to be auto-cancelled because they haven't been
     * marked as sent within 96 hours of payment received.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findOrdersToAutoCancel()
    {
        return self::where('status', self::STATUS_PAYMENT_RECEIVED)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', now()->subHours(96))
            ->get();
    }
    
    /**
     * Find all orders that need to be auto-completed because they haven't been
     * marked as completed within 192 hours of being marked as sent.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findOrdersToAutoComplete()
    {
        return self::where('status', self::STATUS_PRODUCT_SENT)
            ->where('is_disputed', false)
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now()->subHours(192))
            ->get();
    }
    
    /**
     * Process all orders that need auto status changes.
     * 
     * @return array Array containing counts of cancelled and completed orders
     */
    public static function processAllAutoStatusChanges()
    {
        $cancelCount = 0;
        $completeCount = 0;
        
        // Process auto-cancellations
        $ordersToCancel = self::findOrdersToAutoCancel();
        foreach ($ordersToCancel as $order) {
            if ($order->autoCancelIfNotSent()) {
                $cancelCount++;
            }
        }
        
        // Process auto-completions
        $ordersToComplete = self::findOrdersToAutoComplete();
        foreach ($ordersToComplete as $order) {
            if ($order->autoCompleteIfNotConfirmed()) {
                $completeCount++;
            }
        }
        
        return [
            'cancelled' => $cancelCount,
            'completed' => $completeCount
        ];
    }

    /**
     * Calculate required XMR amount based on USD price and current XMR rate.
     *
     * @param float $xmrUsdRate
     * @return float
     */
    public function calculateRequiredXmrAmount($xmrUsdRate)
    {
        if ($xmrUsdRate <= 0) {
            throw new \InvalidArgumentException('XMR rate must be greater than zero');
        }
        
        return $this->total / $xmrUsdRate;
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
     * Mark the order as sent.
     */
    public function markAsSent()
    {
        if ($this->status !== self::STATUS_PAYMENT_RECEIVED) {
            return false;
        }

        $this->status = self::STATUS_PRODUCT_SENT;
        $this->is_sent = true;
        $this->sent_at = now();
        $this->save();

        return true;
    }

    /**
     * Mark the order as completed.
     */
    public function markAsCompleted()
    {
        if ($this->status !== self::STATUS_PRODUCT_SENT && $this->status !== self::STATUS_DISPUTED) {
            return false;
        }

        // Store current status before changing it
        $currentStatus = $this->status;
        
        $this->status = self::STATUS_COMPLETED;
        $this->is_completed = true;
        $this->completed_at = now();
        
        // If this was a disputed order, reset the disputed flag
        if ($currentStatus === self::STATUS_DISPUTED) {
            $this->is_disputed = false;
        }
        
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

        // Store current status before changing it
        $currentStatus = $this->status;
        
        $this->status = self::STATUS_CANCELLED;
        
        // If this was a disputed order, reset the disputed flag
        if ($currentStatus === self::STATUS_DISPUTED) {
            $this->is_disputed = false;
        }
        
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
            self::STATUS_PRODUCT_SENT => 'Product Sent',
            self::STATUS_COMPLETED => 'Order Completed',
            self::STATUS_CANCELLED => 'Order Cancelled',
            self::STATUS_DISPUTED => 'Order Disputed',
            default => 'Unknown Status'
        };
    }
    
    /**
     * Open a dispute for the order.
     */
    public function openDispute($reason)
    {
        // Only allow disputes for orders in "product sent" status
        if ($this->status !== self::STATUS_PRODUCT_SENT) {
            return false;
        }

        // Update order status
        $this->status = self::STATUS_DISPUTED;
        $this->is_disputed = true;
        $this->disputed_at = now();
        $this->save();

        // Create dispute record
        $dispute = new Dispute([
            'order_id' => $this->id,
            'status' => Dispute::STATUS_ACTIVE,
            'reason' => $reason
        ]);
        $dispute->save();

        return $dispute;
    }

    /**
     * Check if the order has an active dispute.
     */
    public function hasActiveDispute()
    {
        return $this->dispute && $this->dispute->status === Dispute::STATUS_ACTIVE;
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
     * Check if a user has too many cancelled orders from a specific vendor.
     * 
     * @param int $userId
     * @param int $vendorId
     * @param int $limit
     * @return bool
     */
    public static function hasExcessiveCancelledOrders($userId, $vendorId, $limit = 4)
    {
        $cancelledCount = self::where('user_id', $userId)
            ->where('vendor_id', $vendorId)
            ->where('status', self::STATUS_CANCELLED)
            ->count();
            
        return $cancelledCount >= $limit;
    }
    
    /**
     * Check if a user has a pending payment order from a specific vendor.
     * 
     * @param int $userId
     * @param int $vendorId
     * @return bool
     */
    public static function hasPendingPaymentOrder($userId, $vendorId)
    {
        return self::where('user_id', $userId)
            ->where('vendor_id', $vendorId)
            ->where('status', self::STATUS_WAITING_PAYMENT)
            ->exists();
    }
    
    /**
     * Check if a user can create a new order with a specific vendor.
     * 
     * @param int $userId
     * @param int $vendorId
     * @return array [bool $canCreate, string $reason]
     */
    public static function canCreateNewOrder($userId, $vendorId)
    {
        // Check for excessive cancelled orders
        if (self::hasExcessiveCancelledOrders($userId, $vendorId)) {
            return [false, 'You have too many cancelled orders with this vendor. Please contact support.'];
        }
        
        // Check for pending payment orders
        if (self::hasPendingPaymentOrder($userId, $vendorId)) {
            return [false, 'You already have a pending payment order with this vendor. Please complete or cancel it before creating a new order.'];
        }
        
        return [true, ''];
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
