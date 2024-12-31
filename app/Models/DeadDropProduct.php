<?php

namespace App\Models;

class DeadDropProduct extends Product
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deaddrop_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'product_id'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        parent::booted();

        // Global scope to only get deaddrop products
        static::addGlobalScope('deaddrop', function ($query) {
            $query->where('type', self::TYPE_DEADDROP);
        });

        // Set type when creating a new instance
        static::creating(function ($product) {
            $product->type = self::TYPE_DEADDROP;
        });
    }

    /**
     * Create a new instance of the model.
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes = [])
    {
        // Create the main product record
        $product = parent::create(array_merge($attributes, [
            'type' => self::TYPE_DEADDROP
        ]));

        // Create the deaddrop product record
        static::query()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'product_id' => $product->id
        ]);

        return $product;
    }
}