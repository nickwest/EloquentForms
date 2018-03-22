<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\unit;

use Nickwest\EloquentForms\DefaultTheme;
use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Themes\bulma\Theme as bulmaTheme;

class ThemeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_theme_returns_view_namespace(): void
    {
        $Theme = new DefaultTheme();
        $this->assertEquals('Nickwest\\EloquentForms', $Theme->getViewNamespace());
    }

    public function test_bulma_theme_returns_view_namespace(): void
    {
        $Theme = new bulmaTheme();
        $this->assertEquals('Nickwest\\EloquentForms\\bulma', $Theme->getViewNamespace());
    }

    //TODO: Add Bulma tests to verify function manipulate Field Displays
}
