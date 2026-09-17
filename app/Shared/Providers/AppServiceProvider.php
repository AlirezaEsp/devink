<?php

namespace App\Shared\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Features\Auth\Events\UserRegistered;
use App\Features\Account\Listeners\CreateProfileOnUserRegistered;

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

        Event::listen(
            UserRegistered::class,
            CreateProfileOnUserRegistered::class
        );
    }
}
