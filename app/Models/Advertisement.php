<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Advertisement extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'user_id',
        'slot_number',
        'duration_days',
        'starts_at',
        'ends_at',
        'payment_identifier',
        'payment_address',
        'payment_address_index',
        'total_received',
        'required_amount',
        'payment_completed',
        'payment_completed_at',
        'expires_at'
    ];

    protected $casts = [
        'slot_number' => 'integer',
        'duration_days' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'total_received' => 'decimal:12',
        'required_amount' => 'decimal:12',
        'payment_completed' => 'boolean',
        'payment_completed_at' => 'datetime',
        'expires_at' => 'datetime'
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
            
            if (empty($model->payment_identifier)) {
                $model->payment_identifier = Str::random(64);
            }
        });
    }

    /**
     * Calculate the required payment amount for an advertisement.
     *
     * @param int $slotNumber
     * @param int $durationDays
     * @return float
     */
    public static function calculateRequiredAmount(int $slotNumber, int $durationDays): float
    {
        $basePrice = config('monero.advertisement_base_price');
        $multipliers = config('monero.advertisement_slot_multipliers');
        
        if (!isset($multipliers[$slotNumber])) {
            throw new \InvalidArgumentException('Invalid slot number');
        }

        return $basePrice * $multipliers[$slotNumber] * $durationDays;
    }

    /**
     * Check if the slot is available for the given time period.
     *
     * @param int $slotNumber
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return bool
     */
    public static function isSlotAvailable(int $slotNumber, Carbon $startDate, Carbon $endDate): bool
    {
        return !static::query()
            ->where('slot_number', $slotNumber)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('starts_at', '<=', $endDate)
                      ->where('ends_at', '>=', $startDate);
                });
            })
            ->where('payment_completed', true)
            ->exists();
    }

    /**
     * Get all active advertisements for display.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveAdvertisements()
    {
        $now = Carbon::now();
        return static::with(['product', 'product.user'])
            ->where('payment_completed', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->orderBy('slot_number')
            ->get();
    }

    /**
     * Check if the payment has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the advertisement period has started.
     *
     * @return bool
     */
    public function hasStarted(): bool
    {
        return $this->starts_at && $this->starts_at->isPast();
    }

    /**
     * Check if the advertisement period has ended.
     *
     * @return bool
     */
    public function hasEnded(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Check if the advertisement is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->payment_completed &&
               $this->starts_at &&
               $this->ends_at &&
               $this->starts_at <= $now &&
               $this->ends_at >= $now;
    }

    /**
     * Get the product associated with the advertisement.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who created the advertisement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a vendor has reached their daily advertisement request limit.
     *
     * @param int|string $userId
     * @return bool
     */
    public static function hasReachedDailyLimit($userId): bool
    {
        $last24Hours = Carbon::now()->subHours(24);
        $requestCount = static::where('user_id', (int) $userId)
            ->where('created_at', '>=', $last24Hours)
            ->count();
        
        return $requestCount >= 8;
    }

    /**
     * Get the cooldown end time for a vendor who has reached their limit.
     *
     * @param int|string $userId
     * @return \Carbon\Carbon|null
     */
    public static function getCooldownEndTime($userId): ?Carbon
    {
        $oldestRequest = static::where('user_id', (int) $userId)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->first();

        return $oldestRequest ? $oldestRequest->created_at->addHours(24) : null;
    }

    /**
     * Check if a product is currently being advertised.
     *
     * @param mixed $productId
     * @return bool
     */
    public static function isProductAdvertised($productId): bool
    {
        return static::query()
            ->where('product_id', $productId)
            ->where('payment_completed', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();
    }
}