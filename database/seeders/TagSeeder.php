<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Features\Tag\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'meta_title' => 'آموزش لاراول',
                'meta_description' => 'مقالات و آموزش‌های مربوط به فریم‌ورک لاراول.',
                'canonical' => null,
                'description' => 'مطالب مرتبط با فریم‌ورک Laravel',
            ],
            [
                'name' => 'PHP',
                'slug' => 'php',
                'meta_title' => 'آموزش PHP',
                'meta_description' => 'مقالات و آموزش‌های زبان برنامه‌نویسی PHP.',
                'canonical' => null,
                'description' => 'مطالب مرتبط با زبان برنامه‌نویسی PHP',
            ],
            [
                'name' => 'Database',
                'slug' => 'database',
                'meta_title' => 'آموزش دیتابیس',
                'meta_description' => 'مقالات مرتبط با پایگاه داده و مدیریت اطلاعات.',
                'canonical' => null,
                'description' => 'مطالب مرتبط با پایگاه داده',
            ],
            [
                'name' => 'Docker',
                'slug' => 'docker',
                'meta_title' => 'آموزش Docker',
                'meta_description' => 'مقالات و آموزش‌های مرتبط با Docker.',
                'canonical' => null,
                'description' => 'مطالب مرتبط با Docker',
            ],
            [
                'name' => 'Git',
                'slug' => 'git',
                'meta_title' => 'آموزش Git',
                'meta_description' => 'مقالات مرتبط با کنترل نسخه و Git.',
                'canonical' => null,
                'description' => 'مطالب مرتبط با Git',
            ],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }
    }
}