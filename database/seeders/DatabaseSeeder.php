<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Database\Seeders\UserSeeder;
use Modules\Category\Database\Seeders\CategorySeeder;
use Modules\Product\Database\Seeders\ProductSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting E-Commerce Database Seeding...');
        $this->command->newLine();
        
        // Seed in proper order due to foreign key constraints
        $this->call([
            UserSeeder::class,      // First - no dependencies
            CategorySeeder::class,  // Second - no dependencies
            ProductSeeder::class,   // Third - depends on categories
        ]);
        
        $this->command->newLine();
        $this->command->info('🎉 E-Commerce Database Seeding Completed Successfully!');
        $this->command->newLine();
        
        // Display summary
        $this->displaySeedingSummary();
    }
    
    /**
     * Display seeding summary
     */
    private function displaySeedingSummary(): void
    {
        $userCount = \Modules\User\Models\User::count();
        $categoryCount = \Modules\Category\Models\Category::count();
        $productCount = \Modules\Product\Models\Product::count();
        
        $this->command->table(
            ['Entity', 'Count', 'Status'],
            [
                ['Users', $userCount, '✅ Complete'],
                ['Categories', $categoryCount, '✅ Complete'],
                ['Products', $productCount, '✅ Complete'],
            ]
        );
        
        $this->command->info('📊 Database is ready for testing and development!');
        $this->command->newLine();
        
        // Display test credentials
        $this->displayTestCredentials();
    }
    
    /**
     * Display test credentials
     */
    private function displayTestCredentials(): void
    {
        $this->command->info('🔑 Test Credentials:');
        $this->command->newLine();
        
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@ecommerce.com', 'admin123'],
                ['Admin', 'john.admin@ecommerce.com', 'password123'],
                ['Customer', 'alice@example.com', 'customer123'],
                ['Customer', 'bob@example.com', 'customer123'],
            ]
        );
        
        $this->command->warn('⚠️  Remember to change default passwords in production!');
    }
}
