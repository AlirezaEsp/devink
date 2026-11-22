<?php

namespace App\Shared\Providers;

use App\Features\Account\Events\PasswordChanged;
use App\Features\Account\Listeners\SendPasswordChangedNotification;
use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use App\Features\Reaction\Events\ArticleLiked;
use App\Features\Reaction\Listeners\SendArticleLikedNotification;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Relation::enforceMorphMap([
            'user' => User::class,
            'article' => Article::class,
            PasswordChanged::class,
            SendPasswordChangedNotification::class,
        ]);
        Event::listen(
            ArticleLiked::class,
            SendArticleLikedNotification::class,
        );
    }
}
