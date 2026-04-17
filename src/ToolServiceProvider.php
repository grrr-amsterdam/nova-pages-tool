<?php

namespace Grrr\Pages;

use Grrr\Pages\Events\AttachedTranslation;
use Grrr\Pages\Events\DeletedPage;
use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Events\SavedPage;
use Grrr\Pages\Listeners\AttachBidirectionalTranslation;
use Grrr\Pages\Listeners\UpdatePageUrl;
use Grrr\Pages\Listeners\DeleteConnectedMenuItems;
use Grrr\Pages\Listeners\UpdateChildPageUrls;
use Grrr\Pages\Models\PageTranslation;
use Grrr\Pages\Observers\PageTranslationObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'pages');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'seo-meta');
        $this->loadRoutesFrom(__DIR__ . '/routes/seo.php');

        $this->publishes(
            [
                __DIR__ . '/../config/nova-pages-tool.php' => config_path(
                    'nova-pages-tool.php'
                ),
            ],
            'config'
        );

        $this->publishes(
            [
                __DIR__ . '/../config/seo.php' => config_path('seo.php'),
            ],
            'config'
        );

        // Register SEO meta field assets.
        Nova::serving(function (ServingNova $event) {
            Nova::script('seo-meta', __DIR__ . '/../dist/js/field.js');
            Nova::style('seo-meta', __DIR__ . '/../dist/css/field.css');
        });

        // Handle a page's URL composition.
        Event::listen(SavingPage::class, [UpdatePageUrl::class, 'handle']);
        Event::listen(SavedPage::class, [UpdateChildPageUrls::class, 'handle']);
        Event::listen(DeletedPage::class, [
            DeleteConnectedMenuItems::class,
            'handle',
        ]);
        Event::listen(AttachedTranslation::class, [
            AttachBidirectionalTranslation::class,
            'handle',
        ]);

        PageTranslation::observe(PageTranslationObserver::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/nova-pages-tool.php',
            'nova-pages-tool'
        );

        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'seo');
    }
}
