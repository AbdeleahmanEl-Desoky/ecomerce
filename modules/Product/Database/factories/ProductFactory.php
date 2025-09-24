<?php

declare(strict_types=1);

namespace Modules\Product\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Models\Product;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Product name templates for realistic data
     */
    private array $productTemplates = [
        'Electronics' => [
            'Wireless Bluetooth Headphones',
            'Smart Fitness Tracker',
            'Portable Power Bank',
            'USB-C Fast Charger',
            'Wireless Mouse',
            'Mechanical Gaming Keyboard',
            'HD Webcam',
            'Bluetooth Speaker',
            'Phone Case',
            'Screen Protector'
        ],
        'Clothing' => [
            'Cotton T-Shirt',
            'Denim Jeans',
            'Hoodie Sweatshirt',
            'Running Shoes',
            'Baseball Cap',
            'Winter Jacket',
            'Polo Shirt',
            'Cargo Shorts',
            'Sneakers',
            'Backpack'
        ],
        'Home' => [
            'Coffee Mug',
            'Throw Pillow',
            'LED Desk Lamp',
            'Storage Box',
            'Picture Frame',
            'Candle Set',
            'Kitchen Utensil Set',
            'Bathroom Towel',
            'Wall Clock',
            'Plant Pot'
        ]
    ];

    public function definition(): array
    {
        $category = $this->faker->randomElement(array_keys($this->productTemplates));
        $productName = $this->faker->randomElement($this->productTemplates[$category]);
        $brand = $this->faker->randomElement(['Apple', 'Samsung', 'Nike', 'Adidas', 'Sony', 'LG', 'HP', 'Dell', 'Canon', 'Generic']);
        
        $fullName = $brand . ' ' . $productName;
        $price = $this->faker->randomFloat(2, 9.99, 999.99);
        
        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => $fullName,
            'description' => $this->generateProductDescription($productName, $brand),
            'price' => $price,
            'stock_quantity' => $this->faker->numberBetween(0, 200),
            'sku' => $this->generateSKU($brand, $productName),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'category_id' => null, // Will be set by seeder
        ];
    }

    /**
     * Generate realistic product description
     */
    private function generateProductDescription(string $productName, string $brand): string
    {
        $features = [
            'High-quality materials and construction',
            'Durable and long-lasting design',
            'Easy to use and maintain',
            'Excellent value for money',
            'Backed by manufacturer warranty',
            'Fast and reliable performance',
            'Sleek and modern appearance',
            'Environmentally friendly materials',
            'Compatible with most devices',
            'Professional grade quality'
        ];
        
        $selectedFeatures = $this->faker->randomElements($features, $this->faker->numberBetween(2, 4));
        
        return "Premium {$productName} from {$brand}. " . 
               implode('. ', $selectedFeatures) . '. ' .
               "Perfect for everyday use and makes an excellent gift.";
    }

    /**
     * Generate realistic SKU
     */
    private function generateSKU(string $brand, string $productName): string
    {
        $brandCode = strtoupper(substr($brand, 0, 3));
        $productCode = strtoupper(substr(str_replace(' ', '', $productName), 0, 6));
        $randomNumber = $this->faker->numberBetween(100, 999);
        
        return "{$brandCode}-{$productCode}-{$randomNumber}";
    }

    /**
     * Create an active product
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Create an inactive product
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    /**
     * Create a low stock product
     */
    public function lowStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_quantity' => $this->faker->numberBetween(1, 10),
            ];
        });
    }

    /**
     * Create an out of stock product
     */
    public function outOfStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_quantity' => 0,
            ];
        });
    }

    /**
     * Create a high stock product
     */
    public function highStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_quantity' => $this->faker->numberBetween(100, 500),
            ];
        });
    }

    /**
     * Create an expensive product
     */
    public function expensive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->randomFloat(2, 500, 2000),
            ];
        });
    }

    /**
     * Create a cheap product
     */
    public function cheap(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->randomFloat(2, 5, 50),
            ];
        });
    }
}
