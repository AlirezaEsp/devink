<?php

namespace App\Features\Article\Models;

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use App\Features\Bookmark\Models\Bookmark;
use App\Features\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'slug', 'body'])]
class Article extends Model
{
    use SoftDeletes, HasFactory;

    protected function casts(): array {
        return [
            'status' => ArticleStatus::class,
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function reactions(): HasMany {
        return $this->hasMany(Reaction::class);
    }

    public function bookmarks(): HasMany {
        return $this->hasMany(Bookmark::class);
    }

    protected $attributes = [
        'status' => 'draft',
    ];
}
