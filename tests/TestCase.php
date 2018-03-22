<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test;

use Cache;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // SQLite
        $app['config']->set('database.default', env('DB_CONNECTION'));
    }

    public function setUp(): void
    {
        parent::setUp();

        // Clear any cache
        Cache::flush();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class,
            \Nickwest\EloquentForms\EloquentFormsServiceProvider::class,
            \Nickwest\EloquentForms\Themes\bulma\EloquentFormsBulmaThemeServiceProvider::class,
        ];
    }
}
