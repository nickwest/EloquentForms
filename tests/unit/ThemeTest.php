<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Theme;
use Nickwest\EloquentForms\DefaultTheme;
use Nickwest\EloquentForms\Themes\bulma\Theme as bulmaTheme;

use Nickwest\EloquentForms\Test\TestCase;

class ThemeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_theme_returns_view_namespace()
    {
        $Theme = new DefaultTheme();
        $this->assertEquals('Nickwest\\EloquentForms', $Theme->getViewNamespace());
    }

    public function test_bulma_theme_returns_view_namespace()
    {
        $Theme = new bulmaTheme();
        $this->assertEquals('Nickwest\\EloquentForms\\bulma', $Theme->getViewNamespace());
    }

    //TODO: Add Bulma tests to verify function manipulate Field Displays

}
