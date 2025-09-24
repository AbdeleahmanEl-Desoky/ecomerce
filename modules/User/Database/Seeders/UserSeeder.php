<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        $this->createAdminUsers();
        
        // Create Customer Users
        $this->createCustomerUsers();
        
        // Create Additional Test Users
        $this->createTestUsers();
    }

    /**
     * Create admin users
     */
    private function createAdminUsers(): void
    {
        $adminUsers = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Super Admin',
                'email' => 'admin@ecommerce.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'John Admin',
                'email' => 'john.admin@ecommerce.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sarah Manager',
                'email' => 'sarah.manager@ecommerce.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($adminUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Admin users created successfully');
    }

    /**
     * Create customer users
     */
    private function createCustomerUsers(): void
    {
        $customerUsers = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Carol Williams',
                'email' => 'carol@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Emma Davis',
                'email' => 'emma@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($customerUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Customer users created successfully');
    }

    /**
     * Create additional test users using factory
     */
    private function createTestUsers(): void
    {
        // Create 10 random customers
        User::factory()
            ->count(10)
            ->customer()
            ->create();

        // Create 3 random admins
        User::factory()
            ->count(3)
            ->admin()
            ->create();

        $this->command->info('✅ Additional test users created successfully');
    }
}
