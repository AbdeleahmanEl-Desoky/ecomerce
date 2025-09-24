<?php

declare(strict_types=1);

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\Category;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create root categories
        $rootCategories = $this->createRootCategories();
        
        // Create subcategories
        $this->createSubcategories($rootCategories);
        
        // Create sub-subcategories
        $this->createSubSubcategories();
    }

    /**
     * Create root categories
     */
    private function createRootCategories(): array
    {
        $rootCategories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets'
            ],
            [
                'name' => 'Clothing & Fashion',
                'slug' => 'clothing-fashion',
                'description' => 'Apparel and fashion accessories'
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Home improvement and garden supplies'
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'description' => 'Sports equipment and outdoor gear'
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media',
                'description' => 'Books, movies, music and digital media'
            ],
            [
                'name' => 'Health & Beauty',
                'slug' => 'health-beauty',
                'description' => 'Health products and beauty items'
            ]
        ];

        $createdCategories = [];
        
        foreach ($rootCategories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'id' => Uuid::uuid4()->toString(),
                    'name' => $categoryData['name'],
                    'slug' => $categoryData['slug'],
                    'parent_id' => null,
                ]
            );
            
            $createdCategories[$categoryData['slug']] = $category;
        }

        $this->command->info('✅ Root categories created successfully');
        
        return $createdCategories;
    }

    /**
     * Create subcategories
     */
    private function createSubcategories(array $rootCategories): void
    {
        $subcategories = [
            'electronics' => [
                'Smartphones & Tablets',
                'Laptops & Computers',
                'Audio & Headphones',
                'Cameras & Photography',
                'Gaming & Consoles',
                'Smart Home & IoT',
                'Accessories & Cables'
            ],
            'clothing-fashion' => [
                'Men\'s Clothing',
                'Women\'s Clothing',
                'Kids & Baby',
                'Shoes & Footwear',
                'Bags & Accessories',
                'Jewelry & Watches',
                'Sunglasses & Eyewear'
            ],
            'home-garden' => [
                'Furniture',
                'Kitchen & Dining',
                'Bedding & Bath',
                'Home Decor',
                'Garden & Outdoor',
                'Tools & Hardware',
                'Lighting & Electrical'
            ],
            'sports-outdoors' => [
                'Fitness & Exercise',
                'Team Sports',
                'Outdoor Recreation',
                'Water Sports',
                'Winter Sports',
                'Cycling & Biking',
                'Hunting & Fishing'
            ],
            'books-media' => [
                'Fiction Books',
                'Non-Fiction Books',
                'Educational Books',
                'Movies & TV',
                'Music & Vinyl',
                'Video Games',
                'Digital Downloads'
            ],
            'health-beauty' => [
                'Skincare',
                'Makeup & Cosmetics',
                'Hair Care',
                'Personal Care',
                'Health Supplements',
                'Medical Supplies',
                'Fragrances'
            ]
        ];

        foreach ($subcategories as $parentSlug => $children) {
            $parentCategory = $rootCategories[$parentSlug];
            
            foreach ($children as $childName) {
                $slug = Str::slug($childName);
                
                Category::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'name' => $childName,
                        'slug' => $slug,
                        'parent_id' => $parentCategory->id,
                    ]
                );
            }
        }

        $this->command->info('✅ Subcategories created successfully');
    }

    /**
     * Create sub-subcategories for demonstration
     */
    private function createSubSubcategories(): void
    {
        // Get some subcategories to create deeper nesting
        $smartphonesCategory = Category::where('slug', 'smartphones-tablets')->first();
        $mensClothingCategory = Category::where('slug', 'mens-clothing')->first();
        $fitnessCategory = Category::where('slug', 'fitness-exercise')->first();

        if ($smartphonesCategory) {
            $smartphoneSubcategories = [
                'iPhone',
                'Samsung Galaxy',
                'Google Pixel',
                'OnePlus',
                'Xiaomi',
                'Tablet Accessories',
                'Phone Cases & Covers'
            ];

            foreach ($smartphoneSubcategories as $name) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'parent_id' => $smartphonesCategory->id,
                    ]
                );
            }
        }

        if ($mensClothingCategory) {
            $mensSubcategories = [
                'T-Shirts & Polos',
                'Shirts & Dress Shirts',
                'Jeans & Pants',
                'Shorts',
                'Jackets & Coats',
                'Suits & Blazers',
                'Underwear & Socks'
            ];

            foreach ($mensSubcategories as $name) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'parent_id' => $mensClothingCategory->id,
                    ]
                );
            }
        }

        if ($fitnessCategory) {
            $fitnessSubcategories = [
                'Cardio Equipment',
                'Strength Training',
                'Yoga & Pilates',
                'Fitness Accessories',
                'Protein & Supplements',
                'Fitness Trackers',
                'Gym Bags'
            ];

            foreach ($fitnessSubcategories as $name) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'parent_id' => $fitnessCategory->id,
                    ]
                );
            }
        }

        $this->command->info('✅ Sub-subcategories created successfully');
        $this->command->info('📊 Total categories created: ' . Category::count());
    }
}
