<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductReviews extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    // Review sentiment constants
    public const SENTIMENT_POSITIVE = 'positive';
    public const SENTIMENT_MIXED = 'mixed';
    public const SENTIMENT_NEGATIVE = 'negative';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'order_item_id',
        'review_text',
        'sentiment',
    ];

    /**
     * Get all available sentiment options.
     *
     * @return array
     */
    public static function getSentimentOptions(): array
    {
        return [
            self::SENTIMENT_POSITIVE => 'Positive',
            self::SENTIMENT_MIXED => 'Mixed',
            self::SENTIMENT_NEGATIVE => 'Negative',
        ];
    }

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
     * Get the user that owns the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was reviewed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order associated with this review.
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    /**
     * Get the order item associated with this review.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * Check if the review is positive.
     */
    public function isPositive(): bool
    {
        return $this->sentiment === self::SENTIMENT_POSITIVE;
    }

    /**
     * Check if the review is mixed.
     */
    public function isMixed(): bool
    {
        return $this->sentiment === self::SENTIMENT_MIXED;
    }

    /**
     * Check if the review is negative.
     */
    public function isNegative(): bool
    {
        return $this->sentiment === self::SENTIMENT_NEGATIVE;
    }

    /**
     * Get all reviews for a product.
     * 
     * @param string $productId The product ID to get reviews for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductReviews($productId)
    {
        return self::where('product_id', $productId)
            ->with(['user:id,username'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get the formatted created at date.
     */
    public function getFormattedDate()
    {
        return $this->created_at->format('Y-m-d');
    }
}