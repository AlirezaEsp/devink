<?php

namespace App\Features\Tag\Models;

use App\Features\Article\Models\Article;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
#[Fillable(['name', 'slug', 'meta_title', 'meta_description', 'canonical', 'description'])]
class Tag extends Model
{
    public function articels():BelongsToMany
    {
       return $this->belongsToMany(Article::class);
}
}
