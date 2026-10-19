<?php

namespace App\Providers;

use App\Models\Achievement;
use App\Observers\AchievementObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Achievement::observe(AchievementObserver::class);
    }
}
