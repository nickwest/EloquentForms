<?php namespace Nickwest\EloquentForms\Test;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp() {
        return parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            'Nickwest\EloquentForms\EloquentFormsServiceProvider',
            'Nickwest\EloquentForms\bulma\EloquentFormsBulmaThemeServiceProvider',
        ];
    }
}
