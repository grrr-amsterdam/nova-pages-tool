<?php

namespace Grrr\Pages;

use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Events\SavedPage;
use Grrr\Pages\Listeners\UpdatePageUrl;
use Grrr\Pages\Listeners\UpdateChildPageUrls;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pages');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'pages');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

        // Handle a page's URL composition.
        Event::listen(SavingPage::class, [UpdatePageUrl::class, 'handle']);
        Event::listen(SavedPage::class, [UpdateChildPageUrls::class, 'handle']);
    }
}
