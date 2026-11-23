<?php

namespace App\Features\Tag\Controllers;

use App\Features\Tag\Models\Tag;
use Illuminate\Http\JsonResponse;
use App\Features\Tag\Resources\TagResource;
use Illuminate\Routing\Controller;


class TagController extends Controller
{

    public function showTags(): JsonResponse
    {
        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        return response()->json([
            'tags' => TagResource::collection($tags),
        ]);
    }}