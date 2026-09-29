<?php

namespace Database\Factories\Features\Account\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Features\Account\Models\Profile;
use App\Features\Account\Models\User;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    // Related model
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'username' => fake()->unique()->userName(),
            'full_name' => fake()->firstName() . ' ' . fake()->lastName(),
            'bio' => fake()->paragraph(2),
            'avatar' => fake()->filePath(),
        ];
    }
}
