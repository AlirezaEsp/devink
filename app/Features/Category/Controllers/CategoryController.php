<?php

namespace App\Features\Category\Controllers;

use App\Features\Category\Models\Category;
use App\Features\Category\Resources\CategoryResource;

class CategoryController
{
    public function showCategories()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();
        return CategoryResource::collection($categories);
}
}