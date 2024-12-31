<?php

namespace App\Models;

class CargoProduct extends Product
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cargo_products';

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

        // Global scope to only get cargo products
        static::addGlobalScope('cargo', function ($query) {
            $query->where('type', self::TYPE_CARGO);
        });

        // Set type when creating a new instance
        static::creating(function ($product) {
            $product->type = self::TYPE_CARGO;
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
            'type' => self::TYPE_CARGO
        ]));

        // Create the cargo product record
        static::query()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'product_id' => $product->id
        ]);

        return $product;
    }
}