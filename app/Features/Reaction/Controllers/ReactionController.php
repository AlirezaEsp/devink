<?php

namespace App\Features\Reaction\Controllers;

use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use App\Features\Reaction\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController
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

        // check for reaction
        $reactionExists = $article->reactions()
            ->where('user_id', $user->id)
            ->exists();

        // return conflict message
        if ($reactionExists) {
            return response()->json([
                'message' => 'Reaction already exists.'
            ], 409);
        }

        // create reaction
        $article->reactions()->create([
            'user_id' => $user->id,
        ]);

        // return successful message
        return response()->json([
            'message' => 'Reaction added successfully.'
        ], 201);
    }
    
    /**
     * Destroy
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse {
        // get user
        $user = $request->user();

        // fetch desired article
        $article = Article::query()
            ->where('status', ArticleStatus::Published)
            ->findOrFail($id);

        // check for reaction
        $reactionExists = $article->reactions()
            ->where('user_id', $user->id)
            ->exists();

        // return conflict message
        if (!$reactionExists) {
            return response()->json([
                'message' => 'Reaction does not exist.'
            ], 409);
        }

        // remove reaction
        $article->reactions()
            ->where('user_id', $user->id)
            ->delete();
    
        // return successful message
        return response()->json([
            'message' => 'Reaction removed successfully.'
        ]);
    }
}
