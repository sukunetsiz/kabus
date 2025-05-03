<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Orders;
use App\Models\OrderItem;
use App\Models\ProductReviews;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ReviewSeeder extends Seeder
{
    // Array of potential review texts for different sentiments
    private $positiveReviews = [
        "Exactly what I was looking for! Excellent quality and fast delivery.",
        "This product exceeded my expectations. Very happy with my purchase.",
        "Great value for the price. Will definitely buy from this vendor again.",
        "Superb quality and the vendor was very helpful throughout the process.",
        "Quick delivery and the product works perfectly. Highly recommend!",
        "One of the best purchases I've made. Exactly as described.",
        "Outstanding quality and excellent customer service from the vendor.",
        "Received earlier than expected and in perfect condition. Very satisfied!",
        "Amazing value for money. The quality is much better than I expected.",
        "Perfect fit for what I needed. The vendor was very responsive and helpful."
    ];

    private $mixedReviews = [
        "Good product but delivery took longer than expected.",
        "Quality is great but the price is a bit high compared to similar products.",
        "Works as described, but packaging could be improved.",
        "Decent product overall, though there were minor issues with the setup.",
        "The product is good but the instructions could be clearer.",
        "Satisfied with the purchase but shipping was a bit delayed.",
        "Works fine but not quite as impressive as I expected for the price.",
        "Good quality but customer service could be more responsive.",
        "Product meets expectations but took some time to figure out how to use it properly.",
        "Does what it's supposed to do, but the design could be more user-friendly."
    ];

    private $negativeReviews = [
        "Not as described. Disappointed with the quality.",
        "Had issues with the product right after receiving it. Not worth the price.",
        "Delivery was extremely delayed and the product arrived damaged.",
        "Poor quality control. The item had defects that weren't mentioned in the description.",
        "Doesn't work as advertised. Would not recommend.",
        "Overpriced for what you get. I expected much better quality.",
        "Vendor was unresponsive when I had issues with the product.",
        "The product stopped working after a few days. Very disappointed.",
        "Not what I was expecting at all. The description is misleading.",
        "Poor craftsmanship and materials. Definitely not worth the price."
    ];

    /**
     * Create a simulated completed order for review purposes
     */
    private function createCompletedOrder($user, $vendor, $product)
    {
        try {
            // Create the order with completed status
            $order = new Orders();
            $order->id = (string) Str::uuid();
            $order->user_id = $user->id;
            $order->vendor_id = $vendor->id;
            $order->unique_url = Str::random(30);
            $order->subtotal = $product->price;
            $order->commission = $product->price * 0.05; // 5% commission
            $order->total = $order->subtotal + $order->commission;
            $order->status = Orders::STATUS_COMPLETED;
            $order->is_paid = true;
            $order->is_sent = true;
            $order->is_completed = true;
            $order->paid_at = now()->subDays(14);
            $order->sent_at = now()->subDays(10);
            $order->completed_at = now()->subDays(7);
            $order->save();

            // Create order item
            $orderItem = new OrderItem();
            $orderItem->id = (string) Str::uuid();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->product_name = $product->name;
            $orderItem->product_description = $product->description;
            $orderItem->price = $product->price;
            $orderItem->quantity = 1;
            $orderItem->measurement_unit = $product->measurement_unit;
            
            // Add random delivery option if available
            if (!empty($product->delivery_options)) {
                $orderItem->delivery_option = $product->delivery_options[array_rand($product->delivery_options)];
            }
            
            // Add random bulk option if available
            if (!empty($product->bulk_options)) {
                $orderItem->bulk_option = $product->bulk_options[array_rand($product->bulk_options)];
            }
            
            $orderItem->save();

            return [
                'order' => $order,
                'orderItem' => $orderItem
            ];
        } catch (\Exception $e) {
            Log::error("Error creating simulated order: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to seed product reviews...');

        // Get users who are not vendors (regular users)
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'vendor');
        })->get();

        if ($users->isEmpty()) {
            $this->command->error('No regular users found. Please run UserSeeder first.');
            return;
        }

        // Get all products with their vendors
        $products = Product::with('user')->get();

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductSeeder first.');
            return;
        }

        $this->command->info('Found ' . $users->count() . ' users and ' . $products->count() . ' products.');
        $this->command->info('Generating reviews...');

        $successCount = 0;
        $errorCount = 0;

        // For each product, generate 2-5 reviews from different users
        foreach ($products as $product) {
            $numReviews = rand(2, 5);
            $this->command->info("Creating {$numReviews} reviews for product: {$product->name}");

            // Shuffle users to pick random reviewers
            $shuffledUsers = $users->shuffle()->take($numReviews);

            foreach ($shuffledUsers as $user) {
                try {
                    // Create a completed order for this user and product
                    $orderData = $this->createCompletedOrder($user, $product->user, $product);
                    
                    if (!$orderData) {
                        $this->command->error("Failed to create order for product {$product->name} and user {$user->username}");
                        $errorCount++;
                        continue;
                    }

                    $order = $orderData['order'];
                    $orderItem = $orderData['orderItem'];

                    // Randomly select sentiment
                    $sentiments = [
                        ProductReviews::SENTIMENT_POSITIVE => 70,
                        ProductReviews::SENTIMENT_MIXED => 20,
                        ProductReviews::SENTIMENT_NEGATIVE => 10
                    ];

                    // Weighted random selection
                    $rand = rand(1, 100);
                    $sentiment = ProductReviews::SENTIMENT_POSITIVE; // Default
                    $cumulative = 0;
                    
                    foreach ($sentiments as $key => $weight) {
                        $cumulative += $weight;
                        if ($rand <= $cumulative) {
                            $sentiment = $key;
                            break;
                        }
                    }

                    // Get review text based on sentiment
                    $reviewText = match($sentiment) {
                        ProductReviews::SENTIMENT_POSITIVE => $this->positiveReviews[array_rand($this->positiveReviews)],
                        ProductReviews::SENTIMENT_MIXED => $this->mixedReviews[array_rand($this->mixedReviews)],
                        ProductReviews::SENTIMENT_NEGATIVE => $this->negativeReviews[array_rand($this->negativeReviews)],
                        default => $this->positiveReviews[array_rand($this->positiveReviews)]
                    };

                    // Create the review
                    ProductReviews::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'review_text' => $reviewText,
                        'sentiment' => $sentiment,
                    ]);

                    $successCount++;
                    $this->command->info("Created {$sentiment} review for product: {$product->name} by user: {$user->username}");
                } catch (\Exception $e) {
                    $this->command->error("Error creating review for product {$product->name}: " . $e->getMessage());
                    Log::error("Error seeding review for product {$product->id}: " . $e->getMessage());
                    $errorCount++;
                }
            }

            $this->command->info("Finished creating reviews for product: {$product->name}");
            $this->command->info("---");
        }

        $this->command->info("Review seeding completed.");
        $this->command->info("Successfully created reviews: {$successCount}");
        $this->command->info("Failed operations: {$errorCount}");
    }
}