<?php

namespace App\Features\Bookmark\Controllers;

use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController
{    
    /**
     * Store
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function store(Request $request, int $id): JsonResponse {
        // get user
        $user = $request->user();

        // fetch desired article
        $article = Article::query()
            ->where('status', ArticleStatus::Published)
            ->findOrFail($id);
        
        // check for bookmark
        $bookmarkExists = $article->bookmarks()
            ->where('user_id', $user->id)
            ->exists();
        
        // return conflict message
        if ($bookmarkExists) {
            return response()->json([
                'message' => 'Article already bookmarked.'
            ], 409);
        }

        // create bookmark
        $article->bookmarks()->create([
            'user_id' => $user->id,
        ]);

        // return successful message
        return response()->json([
            'message' => 'Article bookmarked successfully.'
        ], 201);
    }
}
