<?php

namespace App\Features\Article\Resources;

use App\Features\Article\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ArticleDetailedResource
 * 
 * Article model serialization
 * 
 * @mixin Article
 */
class ArticleDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->user->username,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            /**
             * @var 'draft'|'published'
             */
            'status' => $this->status->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'published_at' => $this->published_at
        ];
    }
}
