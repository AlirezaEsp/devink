<?php

namespace App\Features\Article\Controllers;

use App\Features\Article\Models\Article;
use App\Features\Article\Requests\StoreArticleRequest;
use App\Features\Article\Resources\ArticleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function Pest\Laravel\artisan;

class ArticleController
{    
    /**
     * StoreArticle
     *
     * @param StoreArticleRequest $request 
     *
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse {
        // get new article data
        $articleData = $request->validated();

        // create new article in db
        $newArticle = $request->user()->articles()->create($articleData);

        // return successful message with new stored article resource
        return response()->json([
            'message' => 'Article created successfully.',
            'article' => new ArticleResource($newArticle),
        ], 201);
    }
}
