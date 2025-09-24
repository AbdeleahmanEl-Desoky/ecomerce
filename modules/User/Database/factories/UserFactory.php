<?php

declare(strict_types=1);

namespace Modules\User\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use Ramsey\Uuid\Uuid;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'), // Default password for testing
            'role' => $this->faker->randomElement(['customer', 'admin']),
            'email_verified_at' => $this->faker->optional(0.8)->dateTime(), // 80% verified
        ];
    }

    /**
     * Create a customer user
     */
    public function customer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'customer',
            ];
        });
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        });
    }

    /**
     * Create an unverified user
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Create a verified user
     */
    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => now(),
            ];
        });
    }
}
