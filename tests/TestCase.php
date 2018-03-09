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
        $app['config']->set('database.default', 'testing');
    }

    public function setUp() {
        return parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class,
            'Nickwest\EloquentForms\EloquentFormsServiceProvider',
            'Nickwest\EloquentForms\bulma\EloquentFormsBulmaThemeServiceProvider',
        ];
    }
}
