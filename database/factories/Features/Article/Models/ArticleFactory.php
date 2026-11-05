<?php

namespace Database\Factories\Features\Article\Models;

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    // related model
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(5),
            'slug' => fake()->unique()->slug(),
            'body' => fake()->paragraph(5, true),
            'status' => ArticleStatus::Draft,
            'published_at' => null,
        ];
    }

    // creates published article
    public function published(): static {
        return $this->state(fn () => [
            'status' => ArticleStatus::Published,
            'published_at' => now()
        ]);
    }   
}
