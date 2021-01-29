<?php namespace Nickwest\EloquentForms\test\view\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\rangeFieldTestInterface;

class rangeFieldTest extends FieldViewTestCase implements rangeFieldTestInterface
{
    protected $test_value = '55';
    protected $test_type = 'range';

    // Run all basic tests

    // TODO: add range validation tests?
}
