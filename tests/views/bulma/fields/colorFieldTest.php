<?php

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\colorFieldTestInterface;

class colorFieldTest extends FieldViewBulmaTestCase implements colorFieldTestInterface
{
    protected $test_value = '#ff00ff';
    protected $test_type = 'color';

    // Run all basic tests
}
