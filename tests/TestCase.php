<?php namespace Nickwest\EloquentForms\Test;

use Cache;
use Artisan;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // SQLite
        $app['config']->set('database.default', env('DB_CONNECTION'));
    }

    public function setUp() {
        parent::setUp();

        // Clear any cache
        Cache::flush();
        Artisan::run('view:clear');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class,
            'Nickwest\EloquentForms\EloquentFormsServiceProvider',
            'Nickwest\EloquentForms\Themes\bulma\EloquentFormsBulmaThemeServiceProvider',
        ];
    }
}
