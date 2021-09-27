<?php

namespace Grrr\Pages;

use Grrr\Pages\Events\AttachedTranslation;
use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Events\SavedPage;
use Grrr\Pages\Listeners\AttachTranslationBothWays;
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

        $this->publishes(
            [
                __DIR__ . '/../config/nova-pages-tool.php' => config_path(
                    'nova-pages-tool.php'
                ),
            ],
            'config'
        );

        // Handle a page's URL composition.
        Event::listen(SavingPage::class, [UpdatePageUrl::class, 'handle']);
        Event::listen(SavedPage::class, [UpdateChildPageUrls::class, 'handle']);
        Event::listen(AttachedTranslation::class, [
            AttachTranslationBothWays::class,
            'handle',
        ]);
    }

    /**
     * register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/nova-pages-tool.php',
            'nova-pages-tool'
        );
    }
}
