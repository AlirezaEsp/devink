<?php

namespace App\Features\Article\Resources;

use App\Features\Article\Models\Article;
use App\Features\Category\Models\Category;
use App\Features\Category\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ArticleResource
 * 
 * Article model serialization
 * 
 * @mixin Article
 */
class ArticleResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->whenLoaded('category', fn () =>
            $this->category
                ? new CategoryResource($this->category)
                : null
            ),
                ];
    }
}
