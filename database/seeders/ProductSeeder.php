<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Generate random delivery options based on product type
     */
    private function generateDeliveryOptions(string $method): array
    {
        $numOptions = rand(1, 4); // Random number of options between 1 and 4
        $options = [];

        $digitalOptions = [
            ['description' => 'Instant Download', 'price' => 0],
            ['description' => 'Priority Processing (1 hour)', 'price' => 4.99],
            ['description' => 'Express Processing (30 minutes)', 'price' => 9.99],
            ['description' => 'VIP Processing (15 minutes)', 'price' => 14.99]
        ];

        $cargoOptions = [
            ['description' => 'Standard Shipping (5-7 days)', 'price' => 9.99],
            ['description' => 'Express Shipping (2-3 days)', 'price' => 19.99],
            ['description' => 'Next Day Delivery', 'price' => 29.99],
            ['description' => 'Same Day Delivery', 'price' => 49.99]
        ];

        $deadDropOptions = [
            ['description' => 'Standard Pickup Window (24h)', 'price' => 0],
            ['description' => '12h Pickup Window', 'price' => 9.99],
            ['description' => '6h Pickup Window', 'price' => 19.99],
            ['description' => 'Flexible Pickup Time', 'price' => 29.99]
        ];

        // Select options based on product type
        $availableOptions = match($method) {
            'createDigital' => $digitalOptions,
            'createCargo' => $cargoOptions,
            'createDeadDrop' => $deadDropOptions,
            default => $digitalOptions,
        };

        // Randomly select N unique options
        shuffle($availableOptions);
        return array_slice($availableOptions, 0, $numOptions);
    }

    /**
     * Get random measurement unit based on product type
     */
    private function getRandomMeasurementUnit(string $method): string
    {
        switch ($method) {
            case 'createDigital':
                return collect(['piece', 'unit', 'hour', 'day', 'month'])->random();
            case 'createCargo':
                return collect(['g', 'kg', 'cm', 'm', 'piece'])->random();
            case 'createDeadDrop':
                return collect(['piece', 'unit', 'hour', 'day', 'month'])->random();
            default:
                return 'piece';
        }
    }
    public function run(): void
    {
        $this->command->info('Starting to seed categories and products...');

        // Create main categories
        $electronicsCategory = Category::create(['name' => 'Electronics']);
        $booksCategory = Category::create(['name' => 'Books']);
        $homeGoodsCategory = Category::create(['name' => 'Home Goods']);

        // Create subcategories
        $electronicsSubcategories = [
            $electronicsCategory->children()->create(['name' => 'Smartphones']),
            $electronicsCategory->children()->create(['name' => 'Laptops']),
            $electronicsCategory->children()->create(['name' => 'Accessories']),
            $electronicsCategory->children()->create(['name' => 'Gaming'])
        ];

        $booksSubcategories = [
            $booksCategory->children()->create(['name' => 'Fiction']),
            $booksCategory->children()->create(['name' => 'Non-Fiction']),
            $booksCategory->children()->create(['name' => 'Educational']),
            $booksCategory->children()->create(['name' => 'Children\'s'])
        ];

        $homeGoodsSubcategories = [
            $homeGoodsCategory->children()->create(['name' => 'Kitchen']),
            $homeGoodsCategory->children()->create(['name' => 'Decor']),
            $homeGoodsCategory->children()->create(['name' => 'Furniture']),
            $homeGoodsCategory->children()->create(['name' => 'Garden'])
        ];

        // Define all products with their creation method
        $allProducts = [
            // Digital Products (Instant Internet Delivery)
            [
                'name' => 'Windows 11 Pro License Key',
                'price' => 199,
                'description' => 'Genuine Windows 11 Pro digital license key with instant email delivery. Includes free upgrade support and activation guarantee.',
                'category' => $electronicsSubcategories[0],
                'method' => 'createDigital',
            ],
            [
                'name' => 'Adobe Creative Cloud 1-Year',
                'price' => 599,
                'description' => 'Full Adobe Creative Cloud subscription with immediate access. Includes Photoshop, Illustrator, and all Creative Cloud apps.',
                'category' => $electronicsSubcategories[1],
                'method' => 'createDigital',
            ],
            [
                'name' => 'Complete Web Development Course',
                'price' => 89,
                'description' => 'Comprehensive online course covering HTML, CSS, JavaScript, and React. Instant access to all video lessons and resources.',
                'category' => $electronicsSubcategories[2],
                'method' => 'createDigital',
            ],
            [
                'name' => 'Steam Gift Card $100',
                'price' => 100,
                'description' => 'Digital Steam gift card code for instant gaming purchases. Valid worldwide, delivered immediately via email.',
                'category' => $electronicsSubcategories[3],
                'method' => 'createDigital',
            ],
            // Cargo Products (Regular Shipping)
            [
                'name' => 'Samsung Galaxy S23 Ultra',
                'price' => 1199,
                'description' => 'Latest Samsung flagship smartphone with 256GB storage and S Pen. Ships in original packaging with tracking number.',
                'category' => $booksSubcategories[0],
                'method' => 'createCargo',
            ],
            [
                'name' => 'Nike Air Max 2023',
                'price' => 179,
                'description' => 'Premium running shoes with latest Air cushioning technology. Available in multiple sizes, ships within 24 hours.',
                'category' => $booksSubcategories[1],
                'method' => 'createCargo',
            ],
            [
                'name' => 'Professional Camera Tripod',
                'price' => 149,
                'description' => 'Carbon fiber camera tripod with fluid head. Lightweight and portable, perfect for photography and videography.',
                'category' => $booksSubcategories[2],
                'method' => 'createCargo',
            ],
            [
                'name' => 'Wireless Gaming Mouse',
                'price' => 79,
                'description' => 'High-precision wireless gaming mouse with RGB lighting and programmable buttons. Ships in protective packaging.',
                'category' => $booksSubcategories[3],
                'method' => 'createCargo',
            ],
            // Dead Drop Products (Local Delivery/Pickup)
            [
                'name' => 'Custom Gaming PC Setup',
                'price' => 2499,
                'description' => 'High-end custom-built gaming PC with local setup and installation included. Personal delivery and setup by our technician.',
                'category' => $homeGoodsSubcategories[0],
                'method' => 'createDeadDrop',
            ],
            [
                'name' => 'Luxury Corner Sofa',
                'price' => 1299,
                'description' => 'Premium L-shaped sofa with chaise lounge. Includes local delivery, assembly, and old furniture removal.',
                'category' => $homeGoodsSubcategories[1],
                'method' => 'createDeadDrop',
            ],
            [
                'name' => 'Smart Home Installation Package',
                'price' => 799,
                'description' => 'Complete smart home setup including doorbell, cameras, and hub. Professional installation and configuration included.',
                'category' => $homeGoodsSubcategories[2],
                'method' => 'createDeadDrop',
            ],
            [
                'name' => 'Garden Landscaping Service',
                'price' => 1499,
                'description' => 'Professional garden design and landscaping service. Includes consultation, materials, and complete installation.',
                'category' => $homeGoodsSubcategories[3],
                'method' => 'createDeadDrop',
            ],
        ];

        // Shuffle the products to randomize assignment
        shuffle($allProducts);

        // Get vendor users
        $vendors = User::whereHas('roles', function ($query) {
            $query->where('name', 'vendor');
        })->get();

        if ($vendors->isEmpty()) {
            $this->command->error('No vendors found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Found ' . $vendors->count() . ' vendors. Creating products...');

        $successCount = 0;
        $errorCount = 0;

        foreach ($vendors as $vendor) {
            $this->command->info("Creating products for vendor: {$vendor->username}");

            try {
                // Assign 4 products to the vendor
                $productsForVendor = array_splice($allProducts, 0, 4);
                foreach ($productsForVendor as $product) {
                    $method = $product['method'];
                    unset($product['method']); // Remove method from product data

                    Product::$method([
                        'user_id' => $vendor->id,
                        'category_id' => $product['category']->id,
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'active' => true,
                        'product_picture' => 'default-product-picture.png',
                        'stock_amount' => rand(50, 1000),
                        'measurement_unit' => $this->getRandomMeasurementUnit($method),
                        'delivery_options' => $this->generateDeliveryOptions($method)
                    ]);

                    $successCount++;
                    $this->command->info("Created product: {$product['name']} for vendor: {$vendor->username}");
                }

            } catch (\Exception $e) {
                $this->command->error("Error creating products for vendor {$vendor->username}: " . $e->getMessage());
                Log::error("Error seeding products for vendor {$vendor->username}: " . $e->getMessage());
                $errorCount++;
            }

            $this->command->info("Finished creating products for vendor: {$vendor->username}");
            $this->command->info("---");
        }

        $this->command->info("Product seeding completed.");
        $this->command->info("Successfully created products: {$successCount}");
        $this->command->info("Failed operations: {$errorCount}");
    }
}