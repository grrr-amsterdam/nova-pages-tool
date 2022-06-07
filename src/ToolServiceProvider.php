<?php

namespace Grrr\Pages;

use Grrr\Pages\Events\AttachedTranslation;
use Grrr\Pages\Events\DeletedPage;
use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Events\SavedPage;
use Grrr\Pages\Http\Middleware\Authorize;
use Grrr\Pages\Listeners\AttachBidirectionalTranslation;
use Grrr\Pages\Listeners\UpdatePageUrl;
use Grrr\Pages\Listeners\DeleteConnectedMenuItems;
use Grrr\Pages\Listeners\UpdateChildPageUrls;
use Grrr\Pages\Models\PageTranslation;
use Grrr\Pages\Observers\PageTranslationObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

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

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authorize::class], 'nova-pages-tool')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/nova-pages-tool')
            ->group(__DIR__.'/../routes/api.php');
    }

    
}
