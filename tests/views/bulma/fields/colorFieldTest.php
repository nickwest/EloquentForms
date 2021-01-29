<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\colorFieldTestInterface;

class colorFieldTest extends FieldViewBulmaTestCase implements colorFieldTestInterface
{
    protected $test_value = '#ff00ff';
    protected $test_type = 'color';

    // Run all basic tests
}
