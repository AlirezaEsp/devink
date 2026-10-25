<?php

namespace App\Features\Article\Models;

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'slug', 'body'])]
class Article extends Model
{
    use SoftDeletes, HasFactory;

    protected function casts(): array {
        return [
            'status' => ArticleStatus::class,
            'published_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
