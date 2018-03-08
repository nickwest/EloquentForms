<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Theme;
use Nickwest\EloquentForms\DefaultTheme;

use Nickwest\EloquentForms\Test\TestCase;

class ThemeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_theme_returns_view_namespace()
    {
        $Theme = new DefaultTheme();

        $this->assertNotEmpty($Theme->view_namespace);
    }

    //TODO: Add Bulma tests to verify function manipulate Field Displays

}
