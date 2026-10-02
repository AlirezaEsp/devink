<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Features\Account\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'stringst',
            'username' => 'string',
            'full_name' => 'string',
            'bio' => 'string',
            'avatar' => 'string'
        ]);

        $mockUsers = User::factory(9)->create();
    }
}
