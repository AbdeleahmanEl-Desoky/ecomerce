<?php

declare(strict_types=1);

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;
use Modules\Category\Models\Category;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create featured products
        $this->createFeaturedProducts();
        
        // Create electronics products
        $this->createElectronicsProducts();
        
        // Create clothing products
        $this->createClothingProducts();
        
        // Create home & garden products
        $this->createHomeGardenProducts();
        
        // Create random products using factory
        $this->createRandomProducts();
    }

    /**
     * Create featured products
     */
    private function createFeaturedProducts(): void
    {
        $iphoneCategory = Category::where('slug', 'iphone')->first();
        $samsungCategory = Category::where('slug', 'samsung-galaxy')->first();
        $laptopsCategory = Category::where('slug', 'laptops-computers')->first();

        $featuredProducts = [
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system. Available in multiple colors with 128GB, 256GB, 512GB, and 1TB storage options.',
                'price' => 1199.99,
                'stock_quantity' => 50,
                'sku' => 'IPHONE-15-PRO-MAX-128',
                'status' => 'active',
                'category_id' => $iphoneCategory?->id ?? $this->getRandomCategoryId(),
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Premium Android smartphone with S Pen, 200MP camera, and AI-powered features. Perfect for productivity and creativity.',
                'price' => 1299.99,
                'stock_quantity' => 35,
                'sku' => 'SAMSUNG-S24-ULTRA-256',
                'status' => 'active',
                'category_id' => $samsungCategory?->id ?? $this->getRandomCategoryId(),
            ],
            [
                'name' => 'MacBook Pro 16" M3 Max',
                'description' => 'Professional laptop with M3 Max chip, 36GB unified memory, and 1TB SSD. Perfect for developers, designers, and content creators.',
                'price' => 3999.99,
                'stock_quantity' => 15,
                'sku' => 'MBP-16-M3-MAX-1TB',
                'status' => 'active',
                'category_id' => $laptopsCategory?->id ?? $this->getRandomCategoryId(),
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise canceling wireless headphones with 30-hour battery life and premium sound quality.',
                'price' => 399.99,
                'stock_quantity' => 75,
                'sku' => 'SONY-WH1000XM5-BLACK',
                'status' => 'active',
                'category_id' => $this->getCategoryBySlug('audio-headphones'),
            ],
            [
                'name' => 'iPad Pro 12.9" M2',
                'description' => 'Most advanced iPad with M2 chip, Liquid Retina XDR display, and support for Apple Pencil and Magic Keyboard.',
                'price' => 1099.99,
                'stock_quantity' => 40,
                'sku' => 'IPAD-PRO-129-M2-128',
                'status' => 'active',
                'category_id' => $this->getCategoryBySlug('smartphones-tablets'),
            ]
        ];

        foreach ($featuredProducts as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, ['id' => Uuid::uuid4()->toString()])
            );
        }

        $this->command->info('✅ Featured products created successfully');
    }

    /**
     * Create electronics products
     */
    private function createElectronicsProducts(): void
    {
        $electronicsProducts = [
            // Gaming Products
            [
                'name' => 'PlayStation 5 Console',
                'description' => 'Next-generation gaming console with ultra-high speed SSD and ray tracing support.',
                'price' => 499.99,
                'stock_quantity' => 25,
                'sku' => 'PS5-CONSOLE-STD',
                'category_slug' => 'gaming-consoles',
            ],
            [
                'name' => 'Xbox Series X',
                'description' => 'Most powerful Xbox ever with 4K gaming and Quick Resume feature.',
                'price' => 499.99,
                'stock_quantity' => 30,
                'sku' => 'XBOX-SERIES-X',
                'category_slug' => 'gaming-consoles',
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Portable gaming console with vibrant OLED screen and enhanced audio.',
                'price' => 349.99,
                'stock_quantity' => 60,
                'sku' => 'SWITCH-OLED-WHITE',
                'category_slug' => 'gaming-consoles',
            ],
            
            // Audio Products
            [
                'name' => 'AirPods Pro 2nd Gen',
                'description' => 'Wireless earbuds with active noise cancellation and spatial audio.',
                'price' => 249.99,
                'stock_quantity' => 100,
                'sku' => 'AIRPODS-PRO-2ND',
                'category_slug' => 'audio-headphones',
            ],
            [
                'name' => 'Bose QuietComfort 45',
                'description' => 'Premium noise-canceling headphones with exceptional comfort.',
                'price' => 329.99,
                'stock_quantity' => 45,
                'sku' => 'BOSE-QC45-BLACK',
                'category_slug' => 'audio-headphones',
            ],
            
            // Smart Home
            [
                'name' => 'Amazon Echo Dot 5th Gen',
                'description' => 'Smart speaker with Alexa voice control and improved sound quality.',
                'price' => 49.99,
                'stock_quantity' => 150,
                'sku' => 'ECHO-DOT-5TH-BLUE',
                'category_slug' => 'smart-home-iot',
            ],
            [
                'name' => 'Google Nest Hub Max',
                'description' => 'Smart display with Google Assistant and built-in camera.',
                'price' => 229.99,
                'stock_quantity' => 35,
                'sku' => 'NEST-HUB-MAX-CHALK',
                'category_slug' => 'smart-home-iot',
            ],
        ];

        foreach ($electronicsProducts as $productData) {
            $categoryId = $this->getCategoryBySlug($productData['category_slug']);
            unset($productData['category_slug']);
            
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, [
                    'id' => Uuid::uuid4()->toString(),
                    'status' => 'active',
                    'category_id' => $categoryId
                ])
            );
        }

        $this->command->info('✅ Electronics products created successfully');
    }

    /**
     * Create clothing products
     */
    private function createClothingProducts(): void
    {
        $clothingProducts = [
            [
                'name' => 'Nike Air Force 1 White',
                'description' => 'Classic white leather sneakers with iconic design and comfort.',
                'price' => 90.00,
                'stock_quantity' => 80,
                'sku' => 'NIKE-AF1-WHITE-10',
                'category_slug' => 'shoes-footwear',
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Premium running shoes with responsive Boost midsole.',
                'price' => 180.00,
                'stock_quantity' => 65,
                'sku' => 'ADIDAS-UB22-BLACK-9',
                'category_slug' => 'shoes-footwear',
            ],
            [
                'name' => 'Levi\'s 501 Original Jeans',
                'description' => 'Classic straight-leg jeans in authentic indigo denim.',
                'price' => 69.99,
                'stock_quantity' => 120,
                'sku' => 'LEVIS-501-INDIGO-32',
                'category_slug' => 'jeans-pants',
            ],
            [
                'name' => 'Champion Reverse Weave Hoodie',
                'description' => 'Premium heavyweight hoodie with iconic logo and comfortable fit.',
                'price' => 65.00,
                'stock_quantity' => 90,
                'sku' => 'CHAMPION-RW-HOODIE-L',
                'category_slug' => 'mens-clothing',
            ],
            [
                'name' => 'Ray-Ban Aviator Classic',
                'description' => 'Iconic aviator sunglasses with gold frame and green lenses.',
                'price' => 154.00,
                'stock_quantity' => 55,
                'sku' => 'RAYBAN-AVIATOR-GOLD',
                'category_slug' => 'sunglasses-eyewear',
            ],
        ];

        foreach ($clothingProducts as $productData) {
            $categoryId = $this->getCategoryBySlug($productData['category_slug']);
            unset($productData['category_slug']);
            
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, [
                    'id' => Uuid::uuid4()->toString(),
                    'status' => 'active',
                    'category_id' => $categoryId
                ])
            );
        }

        $this->command->info('✅ Clothing products created successfully');
    }

    /**
     * Create home & garden products
     */
    private function createHomeGardenProducts(): void
    {
        $homeProducts = [
            [
                'name' => 'IKEA MALM Bed Frame',
                'description' => 'Modern bed frame with clean lines and under-bed storage space.',
                'price' => 179.00,
                'stock_quantity' => 25,
                'sku' => 'IKEA-MALM-BED-QUEEN',
                'category_slug' => 'furniture',
            ],
            [
                'name' => 'KitchenAid Stand Mixer',
                'description' => 'Professional 5-quart stand mixer perfect for baking and cooking.',
                'price' => 379.99,
                'stock_quantity' => 30,
                'sku' => 'KITCHENAID-MIXER-RED',
                'category_slug' => 'kitchen-dining',
            ],
            [
                'name' => 'Dyson V15 Detect Vacuum',
                'description' => 'Cordless vacuum with laser dust detection and powerful suction.',
                'price' => 749.99,
                'stock_quantity' => 20,
                'sku' => 'DYSON-V15-DETECT',
                'category_slug' => 'home-decor',
            ],
            [
                'name' => 'Weber Genesis II Gas Grill',
                'description' => 'Premium gas grill with GS4 grilling system and porcelain-enameled lid.',
                'price' => 899.00,
                'stock_quantity' => 15,
                'sku' => 'WEBER-GENESIS-II-E335',
                'category_slug' => 'garden-outdoor',
            ],
        ];

        foreach ($homeProducts as $productData) {
            $categoryId = $this->getCategoryBySlug($productData['category_slug']);
            unset($productData['category_slug']);
            
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, [
                    'id' => Uuid::uuid4()->toString(),
                    'status' => 'active',
                    'category_id' => $categoryId
                ])
            );
        }

        $this->command->info('✅ Home & Garden products created successfully');
    }

    /**
     * Create random products using factory
     */
    private function createRandomProducts(): void
    {
        // Get all categories for random assignment
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('⚠️ No categories found. Creating products without categories.');
            return;
        }

        // Create 50 random products
        Product::factory()
            ->count(50)
            ->create([
                'category_id' => function () use ($categories) {
                    return $categories->random()->id;
                }
            ]);

        $this->command->info('✅ Random products created using factory');
        $this->command->info('📊 Total products created: ' . Product::count());
    }

    /**
     * Get category ID by slug
     */
    private function getCategoryBySlug(string $slug): ?string
    {
        $category = Category::where('slug', $slug)->first();
        return $category?->id ?? $this->getRandomCategoryId();
    }

    /**
     * Get random category ID
     */
    private function getRandomCategoryId(): ?string
    {
        $category = Category::inRandomOrder()->first();
        return $category?->id;
    }
}
