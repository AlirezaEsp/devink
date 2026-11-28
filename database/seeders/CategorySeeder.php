<?php

namespace Database\Seeders;

use App\Features\Category\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
public function run(): void
    {
        // curated reference data for the platform
        $categories = [
            ['name' => 'برنامه‌نویسی', 'slug' => 'programming'],
            ['name' => 'توسعه وب', 'slug' => 'web-development'],
            ['name' => 'هوش مصنوعی', 'slug' => 'artificial-intelligence'],
            ['name' => 'دواپس و زیرساخت', 'slug' => 'devops'],
            ['name' => 'امنیت', 'slug' => 'security'],
            ['name' => 'موبایل', 'slug' => 'mobile'],
            ['name' => 'دیتابیس', 'slug' => 'database'],
            ['name' => 'مهندسی نرم‌افزار', 'slug' => 'software-engineering'],
            ['name' => 'استارتاپ و محصول', 'slug' => 'startup'],
        ];

        // create only the ones that do not exist yet (safe to re-run)
        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
