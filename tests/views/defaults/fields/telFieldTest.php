<?php namespace Nickwest\EloquentForms\test\view\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\telFieldTestInterface;

class telFieldTest extends FieldViewTestCase implements telFieldTestInterface
{
    protected $test_value = '(206) 555-1212 x505';
    protected $test_type = 'tel';

    // Run all basic tests

    // TODO: add tel validation tests?
}
