<?php

namespace Database\Seeders;

use App\Features\Article\Models\Article;
use Illuminate\Database\Seeder;
use App\Features\Account\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // generate test user
        $testUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'stringst',
            'username' => 'string',
            'full_name' => 'string',
            'bio' => 'string',
            'avatar' => 'string'
        ]);

        // generate mock users
        $mockUsers = User::factory(9)->create();

        // generate few articles for test user
        Article::factory()
            ->count(2)
            ->for($testUser)
            ->create();
        Article::factory()
            ->count(3)
            ->published()
            ->for($testUser)
            ->create();

        // generate few articles for mock users
        foreach ($mockUsers as $user) {
            Article::factory()
                ->count(1)
                ->for($user)
                ->create();
            Article::factory()
                ->count(1)
                ->published()
                ->for($user)
                ->create();
        }
    }
}
