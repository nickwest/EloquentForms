<?php namespace Nickwest\EloquentForms\test\view\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\numberFieldTestInterface;

class numberFieldTest extends FieldViewTestCase implements numberFieldTestInterface
{
    protected $test_value = '525600';
    protected $test_type = 'number';

    // Run all basic tests
}
