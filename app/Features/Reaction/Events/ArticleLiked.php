<?php

namespace App\Features\Reaction\Events;

use App\Features\Reaction\Models\Reaction;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired when a user likes a published article.
 *
 * Kept in the Reaction feature because that is where the trigger lives.
 */
final readonly class ArticleLiked
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @param Reaction $reaction
     */
    public function __construct(
        public Reaction $reaction,
    ) {}
}
