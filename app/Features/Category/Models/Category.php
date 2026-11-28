<?php

namespace App\Features\Category\Models;

use App\Features\Article\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Category extends Model
{
    use HasFactory;

    public function articles():HasMany
    {
       return $this->hasMany(Article::class);
    }
}
