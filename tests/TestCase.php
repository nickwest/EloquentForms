<?php namespace Nickwest\EloquentForms\Test;

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
        return parent::setUp();
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
