<?php namespace Nickwest\EloquentForms\test\views\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\colorFieldTestInterface;

class colorFieldTest extends FieldViewTestCase implements colorFieldTestInterface
{
    protected $test_value = '#ff00ff';
    protected $test_type = 'color';

    // Run all basic tests
}
