<?php

namespace App\Features\Article\Controllers;

use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use App\Features\Article\Requests\StoreArticleRequest;
use App\Features\Article\Requests\UpdateArticleRequest;
use App\Features\Article\Resources\ArticleDetailedResource;
use App\Features\Article\Resources\ArticleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
    
    /**
     * ShowMine
     *
     * @param Request $request 
     *
     * @return AnonymousResourceCollection
     */
    public function showMine(Request $request): AnonymousResourceCollection {
        // get authenticated user articles from db
        $articles = Article::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(3);

        // return user articles collection
        return ArticleResource::collection($articles);
    }

    /**
     * PreviewArticle
     *
     * @param Request $request 
     * @param int $id 
     *
     * @return ArticleDetailedResource
     */
    public function preview(Request $request, int $id): ArticleDetailedResource {
        // get authenticated user id from request
        $userId = $request->user()->id;

        // find user desired article from db
        $article = Article::query()
            ->where('user_id', $userId)
            ->where('id', $id)
            ->firstOrFail();
        
        // return founded article resource
        return new ArticleDetailedResource($article);
    }

    /**
     * UpdateArticle
     *
     * @param UpdateArticleRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateArticleRequest $request, int $id): JsonResponse {
        // get user
        $user = $request->user();

        // fetch article from db
        $article = Article::query()
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // get article data to be replaced
        $updateData = $request->validated();

        // update article
        $article->update($updateData);

        // return successful message with updated article resource
        return response()->json([
            'message' => 'Article updated successfully.',
            'article' => new ArticleResource($article)
        ]);
    }

    /**
     * DeleteArticle
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse {
        // get user
        $user = $request->user();

        // fetch article from db
        $article = Article::query()
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // delete article
        $article->delete();

        // return successful message
        return response()->json([
            'message' => 'Article deleted successfully.'
        ]);
    }

    /**
     * PublishArticle
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function publish(Request $request, int $id): JsonResponse {
        // get user
        $user = $request->user();

        // fetch article from db
        $article = Article::query()
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        
        // prevent from publishing an already published article
        if ($article->status === ArticleStatus::Published) {
            return response()->json([
                'message' => 'Article has already been published.'
            ], 409);
        }
        
        // update status and published fields in article
            $article->forceFill([
            'status' => ArticleStatus::Published,
            'published_at' => now()
            ])->save();

        // return successful message with article resource
        return response()->json([
            'message' => 'Article published successfully.',
            'article' => new ArticleResource($article)
        ]);
    }

        /**
     * UnpublishArticle
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function unpublish(Request $request, int $id): JsonResponse {
        // get user
        $user = $request->user();

        // fetch article from db
        $article = Article::query()
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        
        // prevent from unpublishing an already drafted article
        if ($article->status === ArticleStatus::Draft) {
            return response()->json([
                'message' => 'Article is already a draft.'
            ], 409);
        }
        
        // update status and published_at fields in article
            $article->forceFill([
            'status' => ArticleStatus::Draft,
            'published_at' => null
            ])->save();

        // return successful message with article resource
        return response()->json([
            'message' => 'Article unpublished successfully. now it is a draft.',
            'article' => new ArticleResource($article)
        ]);
    }
}
