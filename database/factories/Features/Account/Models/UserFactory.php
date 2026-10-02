<?php

namespace Database\Factories\Features\Account\Models;

use App\Features\Account\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    // related model
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('stringst'),
            'username' => fake()->unique()->userName(),
            'full_name' => fake()->firstName() . ' ' . fake()->lastName(),
            'bio' => fake()->paragraph(2),
            'avatar' => fake()->filePath(),
        ];
    }

    // Returns and creates testing user with default info
    public function defaultTestingUser(): static {
        return $this->state(fn (array $attributes) => [
            "email" => "user@example.com",
            "password" => "stringst",
            "username" => "string",
            "full_name" => "string",
            'bio' => null,
            'avatar' => null,
        ]);
    }

    /**
     * Indicate that the model's email address should be verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the model's deleted_at field should be true.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now()
        ]);
    }
}
