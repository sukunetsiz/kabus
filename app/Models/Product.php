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
        'product_picture'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'product_picture_url'
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

            // Generate unique 80-character slug if not set
            if (empty($model->slug)) {
                $model->slug = Str::random(80);
            }

            // Set default product picture if not set
            if (empty($model->product_picture)) {
                $model->product_picture = 'default-product-picture.png';
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