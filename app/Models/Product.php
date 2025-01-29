<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    // Product types
    public const TYPE_DIGITAL = 'digital';
    public const TYPE_CARGO = 'cargo';
    public const TYPE_DEADDROP = 'deaddrop';

    // Measurement units
    public const UNIT_GRAM = 'g';
    public const UNIT_KILOGRAM = 'kg';
    public const UNIT_MILLILITER = 'ml';
    public const UNIT_LITER = 'l';
    public const UNIT_CENTIMETER = 'cm';
    public const UNIT_METER = 'm';
    public const UNIT_INCH = 'in';
    public const UNIT_FOOT = 'ft';
    public const UNIT_SQUARE_METER = 'm²';
    public const UNIT_PIECE = 'piece';
    public const UNIT_DOZEN = 'dozen';
    public const UNIT_HOUR = 'hour';
    public const UNIT_DAY = 'day';
    public const UNIT_MONTH = 'month';

    /**
     * Get all available measurement units.
     *
     * @return array
     */
    public static function getMeasurementUnits(): array
    {
        return [
            self::UNIT_GRAM => 'Gram (g)',
            self::UNIT_KILOGRAM => 'Kilogram (kg)',
            self::UNIT_MILLILITER => 'Milliliter (ml)',
            self::UNIT_LITER => 'Liter (l)',
            self::UNIT_CENTIMETER => 'Centimeter (cm)',
            self::UNIT_METER => 'Meter (m)',
            self::UNIT_INCH => 'Inch (in)',
            self::UNIT_FOOT => 'Foot (ft)',
            self::UNIT_SQUARE_METER => 'Square Meter (m²)',
            self::UNIT_PIECE => 'Unit (piece)',
            self::UNIT_DOZEN => 'Dozen (12 items)',
            self::UNIT_HOUR => 'Hour',
            self::UNIT_DAY => 'Day',
            self::UNIT_MONTH => 'Month'
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'active',
        'user_id',
        'category_id',
        'slug',
        'product_picture',
        'stock_amount',
        'measurement_unit',
        'delivery_options',
        'bulk_options',
        'ships_from',
        'ships_to',
        'additional_photos'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'delivery_options' => 'array',
        'bulk_options' => 'array',
        'additional_photos' => 'array'
    ];

    /**
     * Validate delivery options structure
     *
     * @param array $options
     * @return bool
     */
    public function validateDeliveryOptions(array $options): bool
    {
        if (count($options) < 1 || count($options) > 4) {
            return false;
        }

        foreach ($options as $option) {
            if (!isset($option['description']) || !isset($option['price'])) {
                return false;
            }

            if (!is_string($option['description']) || trim($option['description']) === '') {
                return false;
            }

            if (!is_numeric($option['price']) || $option['price'] < 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate bulk options structure
     *
     * @param array|null $options
     * @return bool
     */
    public function validateBulkOptions(?array $options): bool
    {
        // Bulk options are optional, so null or empty array is valid
        if ($options === null || empty($options)) {
            return true;
        }

        if (count($options) > 4) {
            return false;
        }

        foreach ($options as $option) {
            if (!isset($option['amount']) || !isset($option['price'])) {
                return false;
            }

            if (!is_numeric($option['amount']) || $option['amount'] <= 0) {
                return false;
            }

            // Price must be greater than zero as per requirements
            if (!is_numeric($option['price']) || $option['price'] <= 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get formatted delivery options
     *
     * @param float|string|null $xmrPrice Current XMR price for conversion
     * @return array
     */
    public function getFormattedDeliveryOptions($xmrPrice = null): array
    {
        return array_map(function ($option) use ($xmrPrice) {
            $optionXmrPrice = is_numeric($xmrPrice) && $xmrPrice > 0 
                ? $option['price'] / $xmrPrice 
                : null;
            
            $totalXmrPrice = is_numeric($xmrPrice) && $xmrPrice > 0 
                ? ($this->price + $option['price']) / $xmrPrice 
                : null;

            $priceDisplay = '$' . number_format($option['price'], 2);
            if ($optionXmrPrice !== null) {
                $priceDisplay .= sprintf(' (≈ ɱ%s)', number_format($optionXmrPrice, 4));
            }

            $totalPriceDisplay = '$' . number_format($this->price + $option['price'], 2);
            if ($totalXmrPrice !== null) {
                $totalPriceDisplay .= sprintf(' (≈ ɱ%s)', number_format($totalXmrPrice, 4));
            }

            return [
                'description' => $option['description'],
                'price' => $priceDisplay,
                'total_price' => $totalPriceDisplay
            ];
        }, $this->delivery_options ?? []);
    }

    /**
     * Get formatted bulk options
     *
     * @param float|string|null $xmrPrice Current XMR price for conversion
     * @return array
     */
    public function getFormattedBulkOptions($xmrPrice = null): array
    {
        $measurementUnits = self::getMeasurementUnits();
        $formattedUnit = $measurementUnits[$this->measurement_unit] ?? $this->measurement_unit;

        return array_map(function ($option) use ($xmrPrice, $formattedUnit) {
            $xmrAmount = is_numeric($xmrPrice) && $xmrPrice > 0 
                ? $option['price'] / $xmrPrice 
                : null;

            return [
                'amount' => $option['amount'],
                'price' => number_format($option['price'], 2),
                'display_text' => sprintf('%s %s for $%s%s',
                    number_format($option['amount']),
                    $formattedUnit,
                    number_format($option['price'], 2),
                    $xmrAmount !== null ? sprintf(' (≈ ɱ%s)', number_format($xmrAmount, 4)) : ''
                )
            ];
        }, $this->bulk_options ?? []);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'product_picture_url',
        'additional_photos_urls'
    ];

    /**
     * Get the additional photos URLs.
     */
    public function getAdditionalPhotosUrlsAttribute(): array
    {
        if (empty($this->additional_photos)) {
            return [];
        }

        return array_map(function ($photo) {
            if ($photo === 'default-product-picture.png') {
                return asset('images/default-product-picture.png');
            }
            return route('product.picture', $photo);
        }, $this->additional_photos);
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

            // Generate unique 80-character slug if not set
            if (empty($model->slug)) {
                $model->slug = Str::random(80);
            }

            // Set default product picture if not set
            if (empty($model->product_picture)) {
                $model->product_picture = 'default-product-picture.png';
            }

            // Set default empty array for delivery_options if not set
            if (!isset($model->delivery_options)) {
                $model->delivery_options = [];
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the product picture URL.
     */
    public function getProductPictureUrlAttribute()
    {
        if ($this->product_picture === 'default-product-picture.png') {
            return asset('images/default-product-picture.png');
        }
        
        return route('product.picture', $this->product_picture);
    }

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the users who have wishlisted this product.
     */
    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')
            ->withTimestamps()
            ->orderBy('wishlists.created_at', 'desc');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include products of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if the product is digital.
     */
    public function isDigital(): bool
    {
        return $this->type === self::TYPE_DIGITAL;
    }

    /**
     * Check if the product is cargo.
     */
    public function isCargo(): bool
    {
        return $this->type === self::TYPE_CARGO;
    }

    /**
     * Check if the product is deaddrop.
     */
    public function isDeadDrop(): bool
    {
        return $this->type === self::TYPE_DEADDROP;
    }

    /**
     * Create a new digital product instance.
     */
    public static function createDigital(array $attributes)
    {
        return static::create(array_merge($attributes, ['type' => self::TYPE_DIGITAL]));
    }

    /**
     * Create a new cargo product instance.
     */
    public static function createCargo(array $attributes)
    {
        return static::create(array_merge($attributes, ['type' => self::TYPE_CARGO]));
    }

    /**
     * Create a new deaddrop product instance.
     */
    public static function createDeadDrop(array $attributes)
    {
        return static::create(array_merge($attributes, ['type' => self::TYPE_DEADDROP]));
    }
}
