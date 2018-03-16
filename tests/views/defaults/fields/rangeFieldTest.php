<?php namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\rangeFieldTestInterface;

class rangeFieldTest extends FieldViewTestCase implements rangeFieldTestInterface
{
    protected $test_value = '55';
    protected $test_type = 'range';

    // Run all basic tests

    // TODO: add range validation tests?
}
