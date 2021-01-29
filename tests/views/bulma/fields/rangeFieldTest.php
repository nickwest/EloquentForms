<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\rangeFieldTestInterface;

class rangeFieldTest extends FieldViewBulmaTestCase implements rangeFieldTestInterface
{
    protected $test_value = '55';
    protected $test_type = 'range';
    protected $expected_type_class = '';

    // Run all basic tests

    // TODO: add range validation tests?
}
