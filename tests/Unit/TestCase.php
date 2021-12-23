<?php

namespace Tests\Unit;

use Grrr\Pages\ToolServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use OptimistDigital\MenuBuilder\MenuBuilderServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('logging.default', 'stderr');
    }

    protected function getPackageProviders($app)
    {
        return [ToolServiceProvider::class, MenuBuilderServiceProvider::class];
    }

    protected function defineDatabaseMigrations()
    {
        // Since our migrations depends on the default laravel migrations, we
        // cannot use the RefreshDatabase trait. Because that trait will wipe
        // the default laravel mirgations.
        // That is why we do a migrate and rollback here.
        $this->loadLaravelMigrations([
            '--database' => 'mysql',
        ]);
        $this->artisan('migrate', ['--database' => 'mysql'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', [
                '--database' => 'mysql',
            ])->run();
        });
    }
}
